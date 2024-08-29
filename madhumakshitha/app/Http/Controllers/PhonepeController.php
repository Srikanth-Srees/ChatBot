<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customerproducts_Details;
use App\Models\transactions;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
class PhonepeController extends Controller
{
    private $merchantId;
    private $merchantUserId;
    private $merchantTransactionId;
    private $waId;
    private $baseUrl;
    private $phoneId;
    private $accessToken;
    private $whatsappCloudApi;
    public function __construct()
    { 
        $this->accessToken = env('ACCESS_TOKEN');

        $this->baseUrl = config('phonepe.env') == 'production' ? 'https://api.phonepe.com/apis/hermes' : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        
        $this->whatsappCloudApi = new WhatsAppCloudApi([
            'from_phone_number_id' =>env('FROM_PHONE_NUMBER_ID'),
            'access_token' => $this->accessToken,
        ]);
       
    }

    public function payment(Request $request)
    {
        // Retrieve the customer record
        $mobile=$request->query('mobile');
        $customer = Customerproducts_Details::where('mobile', $mobile)
            ->where('delivery_detail_status', 'completed')
            ->where('transaction_detail_status', 'pending')
            ->latest()
            ->first();

        $orderedProducts = json_decode($customer->ordered_products, true);

        $cost=$customer->amount;    // Initialize variables for total cost and formatted product list
        log::info($cost);
        
        $productPrices = [
            'Honey 1 kg' => 500,
            'Honey 500gm' => 300,
            'Honey 250gm' => 200,
            'Madhukalpa (Lehya)500gm' => 400,
            'Amla candy250gm' => 250,
            'JAFI (JACKFRUIT COFFEE) 250gm' =>250,
            'Madhupeyaras 750gm' =>350
        ];


        return view('Payment', compact('orderedProducts','productPrices','cost','mobile'));
    }


    public function phonepe(Request $request)
    {
        $waId = $request->input('mobile');

        $customer = Customerproducts_Details::where('mobile',$waId)
            ->where('delivery_detail_status', 'completed')
            ->where('transaction_detail_status', 'pending')
            ->latest()
            ->first();

        $amount=$customer->amount;

        $data = array(
            "merchantId" =>env('PHONEPE_MERCHANT_ID'), 
            "merchantTransactionId" =>  env('PHONEPE_MERCHANT_ID'),
            "merchantUserId" => env('PHONEPE_MERCHANT_USER_ID'),    
            "amount" => $amount*100,
            "redirectUrl" => route('phonepeResponse').'?amt='.$amount.'&ph='.$waId,
            "redirectMode" => "POST",
            "callbackUrl" =>route('phonepeResponse'),
            "mobileNumber" => $waId,
            "paymentInstrument" => array(
                "type" => "PAY_PAGE"
            ),
        );

        $encode = base64_encode(json_encode($data));
        $saltKey = env('PHONEPE_SALT_KEY');
        $saltIndex = 1;

        $string = $encode . '/pg/v1/pay' . $saltKey;
        $sha256 = hash('sha256', $string);

        $finalXHeader = $sha256 . '###' . $saltIndex;

        //download : composer require ixudra/curl

        $response = Curl::to('https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay')
            ->withHeader('Content-Type: application/json')
            ->withHeader('X-VERIFY: ' . $finalXHeader)
            ->withData(json_encode(['request' => $encode]))
            ->post();

        $rData = json_decode($response);
        Log::info(json_encode($rData));
        return redirect()->to($rData->data->instrumentResponse->redirectInfo->url);
       
    }

    public function phonepeResponse(Request $request)
{
    Log::info('in response');
    $amount = $request->query('amt');
    log::info($amount);
    $phId = $request->query('ph');
    log::info($phId);

    $saltKey = env('PHONEPE_SALT_KEY');
    $saltIndex = 1;
    $finalXHeader = hash('sha256', '/pg/v1/status/' . $request['merchantId'] . '/' . $request['transactionId'] . $saltKey) . '###' . $saltIndex;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $this->baseUrl . '/pg/v1/status/' . $request['merchantId'] . '/' . $request['transactionId'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'accept: application/json',
            'X-VERIFY: ' . $finalXHeader,
            'X-MERCHANT-ID: ' . $request['transactionId']
        ),
        CURLOPT_SSL_VERIFYPEER => false,
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    if (json_decode($response)->success == true) {
        $responseData = json_decode($response);
        $transactionData = $responseData->data;
        $transactionId = $transactionData->transactionId;
        $amt = $transactionData->amount;
        $state = $transactionData->state;
        $type = $transactionData->paymentInstrument->type;
        // Extract cardType from the response if available, otherwise store it as null
        $cardType = isset($transactionData->paymentInstrument->cardType) ? $transactionData->paymentInstrument->cardType : null;
        $bankTransactionId=isset($transactionData->paymentInstrument->cardType) ?$transactionData->paymentInstrument->bankTransactionId : null;
      
        $bankId = $transactionData->paymentInstrument->bankId?? null;
        $completeDetails = json_encode($transactionData);

        $customer = Customerproducts_Details::where('mobile',$phId)->where('delivery_detail_status', 'completed')->where('transaction_detail_status', 'pending')->latest() ->first(); 
        if ($customer) {
            // Fetch the transaction associated with the customer
            $transaction = transactions::where('id', $customer->transaction_detail_id)->first();
            log::info($transaction->id);
            if ($transaction) {
                // Update the customer details with the information from the latest transaction
                $transaction->update([
                    'amount' => $amt/100,
                    'status' => $state,
                    'transactionId'=>$transactionId,
                    'type' => $type,
                    'card_type' => $cardType,
                    'bank_transaction_id' => $bankTransactionId,
                    'bank_id' => $bankId,
                    'complete_details' => $completeDetails,
                    
                ]);
                log::info('saved');
                // Send WhatsApp message and redirect

                $customer->transaction_detail_status='completed';
                $customer->save();
            $this->whatsappCloudApi->sendTextMessage($phId, "😊 Thank you for your payment of\n ₹ $amount ! Your Order has been successfully confirmed. Have a wonderful day! 🌟");


            $whatsappRedirectUrl = "https://wa.me/+917348839783";
            
            // Redirect user to his WhatsApp number
            return redirect()->to($whatsappRedirectUrl);
            }
        }
         else {
            Log::error('Transaction not found for phone number: ' . $phId);
            $this->whatsappCloudApi->sendTextMessage($phId, 'Something went wrong. Please try again.');
            
            $whatsappRedirectUrl = "https://wa.me/+917348839783";
            
            // Redirect user to WhatsApp number
            return redirect()->to($whatsappRedirectUrl);
        }
    } else {
        $this->whatsappCloudApi->sendTextMessage($phId, 'Something went wrong. Please try again.');
         
        $whatsappRedirectUrl = "https://wa.me/+917348839783";
        
        // Redirect user to WhatsApp number
        return redirect()->to($whatsappRedirectUrl);
    }
}
}
