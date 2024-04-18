<?php

namespace App\Http\Controllers;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Row;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Section;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Action;
use CURLFile;
use App\Models\payDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Integration;
use App\Models\Integrate;
use App\Models\Detail;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
class phonepeController extends Controller
{   
    private $merchantId;
    private $merchantUserId;
    private $merchantTransactionId;

    private $baseUrl;
    private $phoneId;
    private $accessToken;
    private $whatsappCloudApi;
    public function __construct()
    {    $this->phoneId = '199957899878586';
        $this->merchantId = config('phonepe.merchantId');
        $this->merchantUserId = config('phonepe.merchantUserId');
        $this->merchantTransactionId = config('phonepe.merchantTransactionId');
        $this->accessToken = 'EAALUTFGmTbYBO8nWUJH0MQfGsxzPxw9jUQf8t3tDvzAtEQHHzbSbDIyC98auBS7XT9DXukvRXTGrkl9W3kzfcqO5LXD5ZA7JWgrxY3Tuv5Gpym5VUBztjwpanWLZBRWnrroZBpZAhfBbD2REZCaZCCH7pCOhJvnjbZAKAWUyomdU60jZBpzBAAk5OJh1wVae9Y32uciJnkDA4xfexbNmRp8ZD';

        $this->baseUrl = config('phonepe.env') == 'production' ? 'https://api.phonepe.com/apis/hermes' : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
      
        $whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => '199957899878586',
            'access_token' => 'EAALUTFGmTbYBO8nWUJH0MQfGsxzPxw9jUQf8t3tDvzAtEQHHzbSbDIyC98auBS7XT9DXukvRXTGrkl9W3kzfcqO5LXD5ZA7JWgrxY3Tuv5Gpym5VUBztjwpanWLZBRWnrroZBpZAhfBbD2REZCaZCCH7pCOhJvnjbZAKAWUyomdU60jZBpzBAAk5OJh1wVae9Y32uciJnkDA4xfexbNmRp8ZD',
        ]);
       
    }

   public function pay(Request $request){

        $whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => '199957899878586',
            'access_token' => 'EAALUTFGmTbYBO8nWUJH0MQfGsxzPxw9jUQf8t3tDvzAtEQHHzbSbDIyC98auBS7XT9DXukvRXTGrkl9W3kzfcqO5LXD5ZA7JWgrxY3Tuv5Gpym5VUBztjwpanWLZBRWnrroZBpZAhfBbD2REZCaZCCH7pCOhJvnjbZAKAWUyomdU60jZBpzBAAk5OJh1wVae9Y32uciJnkDA4xfexbNmRp8ZD',
        ]);
        // $hub_verify_token = 'testing';    
        //     //Check if the request method is GET and verify the token
        //     if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $hub_verify_token) {
        //         echo $_GET['hub_challenge'];
        //        exit;
        //     }
        $payload = json_decode($request->getContent(), true);
                
                $dueDate = date('d-m-y');
                Log::info(json_encode($payload));
                $waId = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ;
          
               // $text = $this->$whatsapp_cloud_api->sendTextMessage('918639647144', 'Hello srikanth..! How are you??');
                //Log::info($waId . "\n" . $text);
                // http_response_code(200);
                $name = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name']  ?? null;
                $textMessage = $payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] ?? null;
                Log::info("wa_id: $waId, name: $name, body: $textMessage");
            
                if (preg_match('/^Pay \d+$/', $textMessage)) {
                  preg_match('/\d+/', $textMessage, $matches);
                  $amount = $matches[0];
                  Log::info("Payment amount: $amount");
            
                  $client = new Client([
                    'base_uri' => 'https://graph.facebook.com/v19.0/',
                  ]);
            
                  $response = $client->post("$this->phoneId/messages", [  // Sends a POST request to the specified URL
                    'headers' => [                                      // Sets the headers of the request
                        'Authorization' => "Bearer $this->accessToken", // Adds the Authorization header with the access token
                        'Content-Type' => 'application/json',            // Specifies the content type as JSON
                    ],
                    'json' => [                                 // Specifies the body of the request is in JSON format
                      'messaging_product' => 'whatsapp',      // Specifies the messaging product as WhatsApp
                      'recipient_type' => 'individual',        // Specifies the recipient type as individual
                      'to' => $waId,                           // Sets the recipient's WhatsApp ID
                      'type' => 'interactive',                 // Specifies the message type as interactive
                      'interactive' => [                       // Provides details for the interactive content
                          'type' => 'cta_url',                 // Specifies the type of interactive content as CTA(call to action) URL
                          'header' => [                        // Provides the header details for the interactive message
                              'type' => 'text',                // Specifies the type of header as text
                              'text' => 'Payment',              // Sets the text content of the header
                          ],
                          'body' => [                            // Provides the body content for the interactive message
                            'text' => "Dear  $name,            
              
              I hope this message finds you well! \n\nJust a friendly reminder regarding the pending payment for [Product/Service]. Your prompt payment is highly appreciated.
              
              💳 Total Amount : $amount
              📆 Due Date: $dueDate
              
              Feel free to reach out if you have any questions or concerns. Thank you for your cooperation! 💼
              
              Best regards,
              Automatically Live",
                          ],
                          'footer' => [                        // Provides the footer details for the interactive message
                            'text' => 'click the link below', // Footer text prompting the recipient to click the link
                        ],
                          'action' => [                        // Provides the action button details
                            'name' => 'cta_url',               // Specifies the action name
                            'parameters' => [                   // Provides the parameters for the action button
                              'display_text' => "Pay $amount",    // Display text for the action button
                              'url' => "https://8b55-2406-7400-56-7f78-6906-7aaa-e9e9-caf2.ngrok-free.app/webhook/phonepe?amt=$amount&ph=$waId", // URL for payment
                            ],
                          ],
                        ],
                    ],
                  ]);
            
                  $res = $response->getBody()->getContents();
                  Log::info($res);
                  http_response_code(200);
            
           }
    }

      
    
      


          public function show(Request $request){
                $hub_verify_token = 'testing';    
                //Check if the request method is GET and verify the token
                if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $hub_verify_token) {
                    echo $_GET['hub_challenge'];
                   exit;
                }
        
        }

      
    public function phonePe(Request $request)
    {
        Log::info('in phonepe ');
        $amount = $request->input('amt');
        $waId = $request->input('ph');
        $data = array(
            "merchantId" => $this->merchantId,
            "merchantTransactionId" => $this->merchantTransactionId,
            "merchantUserId" => $this->merchantUserId,
            "amount" =>1*100,//$amount*100,
            "redirectUrl" => route('response').'?amt='. $amount.'&ph='.$waId,
            "redirectMode" => "POST",
            "callbackUrl" =>route('response'),
            "mobileNumber" => $waId,
            "paymentInstrument" => array(
                "type" => "PAY_PAGE"
            ),
        );

        $encode = base64_encode(json_encode($data));
        $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
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

    public function response(Request $request)
    {
        Log::info('in response');
        $amount = $request->query('amt');
        $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
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
        //dd( $response);
        curl_close($curl);


        if (json_decode($response)->success == true) {
            //  $responseData = json_decode($response);
            //  $pay_details=new payDetails();
            //  $pay_details->amount = $amount;
            //  $pay_details->phone = $ph;
            //  $pay_details->transactionId = $responseData->data->transactionId;
            //  $pay_details->state = $responseData->data->state;
            //  $pay_details->save();

            // $details= new Detail();
            // $details->name = $name;
            // $details->email = $email;
            // $details->phone = $phone;
            // $details->transactionId = $responseData->data->transactionId;
            // $details->amount = $responseData->data->amount;
            // $details->state = $responseData->data->state;
            // $details->save();
            // $this->phoneId = '199957899878586';
            // $this->accessToken = 'EAALUTFGmTbYBO6rtqfQL5pz2ehctQ1rc0Qj38xgwOHhUDktsTXpOjhco2ynh9Bnkc3mZAZBeZACIJ4LxsC3XpZAItamBQLOqbl61DQT44DhgqqWJsHhsrZCz2TWlDGXpTknGjtcNacnZAxJ8D1Po2w2R8Sp2hIDAWkdKiketBeWeQrXsDFfXpCeUujZB31vJWZCOJrWmVro9VBhSzI73iPQZD';
    
            $whatsapp_cloud_api = new WhatsAppCloudApi([
                'from_phone_number_id' => '199957899878586',
                'access_token' => 'EAALUTFGmTbYBO8nWUJH0MQfGsxzPxw9jUQf8t3tDvzAtEQHHzbSbDIyC98auBS7XT9DXukvRXTGrkl9W3kzfcqO5LXD5ZA7JWgrxY3Tuv5Gpym5VUBztjwpanWLZBRWnrroZBpZAhfBbD2REZCaZCCH7pCOhJvnjbZAKAWUyomdU60jZBpzBAAk5OJh1wVae9Y32uciJnkDA4xfexbNmRp8ZD',
            ]);
            
            $text=$whatsapp_cloud_api->sendTextMessage('918639647144', 'Thank you for paying $ '.$amount.' have a nice day');
            
            $whatsappRedirectUrl = "https://wa.me/+918591633466";
            
            // Redirect user to his WhatsApp number
            return redirect()->to($whatsappRedirectUrl);

            // return dd('ok');
        }
        else {

            $whatsapp_cloud_api = new WhatsAppCloudApi([
                'from_phone_number_id' => '199957899878586',
                'access_token' => 'EAALUTFGmTbYBO8nWUJH0MQfGsxzPxw9jUQf8t3tDvzAtEQHHzbSbDIyC98auBS7XT9DXukvRXTGrkl9W3kzfcqO5LXD5ZA7JWgrxY3Tuv5Gpym5VUBztjwpanWLZBRWnrroZBpZAhfBbD2REZCaZCCH7pCOhJvnjbZAKAWUyomdU60jZBpzBAAk5OJh1wVae9Y32uciJnkDA4xfexbNmRp8ZD',
            ]);
            
            $text=$whatsapp_cloud_api->sendTextMessage('918639647144', 'Something wrong try again');
            
            $whatsappRedirectUrl = "https://wa.me/+918591633466";
            
            // Redirect user to his WhatsApp number
            return redirect()->to($whatsappRedirectUrl);

        }
    }
    

}
