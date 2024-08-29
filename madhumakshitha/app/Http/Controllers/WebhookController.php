<?php

namespace App\Http\Controllers;
use App\Models\Customerproducts_Details;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\transactions;
use App\Models\Booking_Details;
use App\Models\DeliveryAddress;
use Illuminate\Support\Facades\Log;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
class WebhookController extends Controller
{
    private $whatsapp_cloud_api;

    private $phoneNumberId;

    private $accessToken;

    private $name;

    public function __construct()
    {
        $this->phoneNumberId = env('FROM_PHONE_NUMBER_ID');
        $this->accessToken = env('ACCESS_TOKEN');
        // Require the Composer autoloader.
        //composer require netflie/whatsapp-cloud-api 
        // Initialize the WhatsAppCloudApi with the access token
        $this->whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => env('FROM_PHONE_NUMBER_ID'),
            'access_token' => env('ACCESS_TOKEN'),
        ]);
    }

    public function handleWebhook(Request $request)
    {
        // Get all request data as an array
        $requestData = $request->all();
        Log::info(json_encode($requestData));

        $payload = $request->all();
        $mobile = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ?? null;
        $this->name = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ?? null;
        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'])) {
            $messageBody = strtolower($payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body']);
           
            if (strpos($messageBody, 'hello'|'hi'|'i am interested'|'hey'|'hii'|'honey') !== false) {
                Log::info('going to welcome');
                $this->carousel($mobile);
                return;
            }
        }

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'])) {
            // Retrieve the response_json data
            $responseJson = $payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'];

            $this->saveAddress($responseJson,$mobile);
            $this->payment($mobile);
            
        }
    }

    public function verify(Request $request){
        $hub_verify_token = 'test';

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $hub_verify_token) {
        echo $_GET['hub_challenge'];
        exit;
        }
    }
    public function saveAddress($responseJson, $mobile)
    {
        $formData = json_decode($responseJson, true);
        Log::info('Form data received: ', $formData);
    
        // Retrieve necessary variables with fallback to null
        $addressData = [
            'in_pin_code' => $formData['values']['in_pin_code'] ?? null,
            'building_name' => $formData['values']['building_name'] ?? null,
            'landmark_area' => $formData['values']['landmark_area'] ?? null,
            'address' => $formData['values']['address'] ?? null,
            'city' => $formData['values']['city'] ?? null,
            'name' => $formData['values']['name'] ?? null,
            'phone_number' => $formData['values']['phone_number'] ?? null,
            'house_number' => $formData['values']['house_number'] ?? null,
            'floor_number' => $formData['values']['floor_number'] ?? null,
            'state' => $formData['values']['state'] ?? null,
        ];
    
        try {
            $customer = Customerproducts_Details::where('mobile', $mobile)->latest()->first();
    
            if (!$customer) {
                Log::error("Customer not found for mobile: $mobile");
                return; // or throw an exception
            }
    
            $deliveryAddress = DeliveryAddress::find($customer->delivery_detail_id);
    
            if (!$deliveryAddress) {
                Log::error("Delivery address not found for customer ID: {$customer->id}");
                return; // or throw an exception
            }
    
            // Update the delivery address with the new data
            $deliveryAddress->fill($addressData);
            $deliveryAddress->save();
    
            // Update customer delivery status
            $customer->delivery_detail_status = 'completed';
            $customer->save();
    
            Log::info("Address saved successfully for customer ID: {$customer->id}");
    
        } catch (\Exception $e) {
            Log::error("Error saving address: " . $e->getMessage());
            // Handle or rethrow the exception as needed
        }
    }
    

    public function carousel($mobile)
    {   //$mobile='918639647144';
        
        $phoneid = $this->phoneNumberId;
        $url = "https://graph.facebook.com/v19.0/{$phoneid}/messages";
        //$accessToken = 'EAAL5TyLNC7oBO5eQHaPR9nFDf2M3oL2lsdYW4afo8AWQzr0YqYEHQsfZBIJfEhkbpyifZCUnMcFhVZCDxVP4tZCqjHumnLfDTB4vsK6xm9CopUIuaFeiKxOgIwL1ZB3eoPC9wes9ZBuwmUIi0nWAPv8YxB0j1ubxcIFp9gZAKlucv5vir1TtfWTpt3e8gw8i2PoTYZBOtg6eS2f2ZB0kskkeDeRr0SwZDZD';
    
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $mobile,
            'type' => 'template',
            'template' => [
                'name' => '_madhumakshika_1',
                'language' => [
                    'code' => 'en_US'
                ],
                'components' => [
                    [
                        'type' => 'CAROUSEL',
                        'cards' => [
                            [
                                'card_index' => 0,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                     "link"=> "https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product1_rzawog.jpg"
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product1&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 1,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product2_lzdjtj.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product2&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 2,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product3_evzape.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                               'type' => 'PAYLOAD',
                                                'payload' => "?product=product3&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 3,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724922214/new_honey_mnyluz.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product4&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 4,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    //'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1722854073/Volvo_pgvq3f.png'
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724744152/product6_1_1_azqerj.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product5&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 5,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724739136/product7_cmlg2w.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product6&mobile=$mobile"
                                            ]
                                        ]
                                    ]
                                ]
                            
                            
                            ],
                        ]
                    ]
                ]
            ]
        ];
    
        $jsonData = json_encode($data);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
    
        curl_close($ch);
        log::info($response);
        return $response;
    }

    public function carousel_2($mobile)
    {   //$mobile='918639647144';
        
        $phoneid = $this->phoneNumberId;
        $url = "https://graph.facebook.com/v19.0/{$phoneid}/messages";
        //$accessToken = 'EAAL5TyLNC7oBO5eQHaPR9nFDf2M3oL2lsdYW4afo8AWQzr0YqYEHQsfZBIJfEhkbpyifZCUnMcFhVZCDxVP4tZCqjHumnLfDTB4vsK6xm9CopUIuaFeiKxOgIwL1ZB3eoPC9wes9ZBuwmUIi0nWAPv8YxB0j1ubxcIFp9gZAKlucv5vir1TtfWTpt3e8gw8i2PoTYZBOtg6eS2f2ZB0kskkeDeRr0SwZDZD';
    
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $mobile,
            'type' => 'template',
            'template' => [
                'name' => 'honey',
                'language' => [
                    'code' => 'en_US'
                ],
                'components' => [
                    [
                        'type' => 'CAROUSEL',
                        'cards' => [
                            [
                                'card_index' => 0,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                     "link"=> "https://res.cloudinary.com/drroyvl5p/image/upload/v1724922214/madhusara_300_r2boes.jpg"
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            [
                                                'type' => 'TEXT',
                                                'text' => '300' // Populate bus name
                                            ],
                                            [
                                                'type' => 'TEXT',
                                                'text' => '350' // Populate bus price
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product7&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 1,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724922214/madhusara_150_pj72fh.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            [
                                                'type' => 'TEXT',
                                                'text' => '150' // Populate bus name
                                            ],
                                            [
                                                'type' => 'TEXT',
                                                'text' => '200' // Populate bus price
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product8&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 2,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724922480/WAX_snktsi.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            [
                                                'type' => 'TEXT',
                                                'text' => '50' // Populate bus name
                                            ],
                                            [
                                                'type' => 'TEXT',
                                                'text' => '130' // Populate bus price
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                               'type' => 'PAYLOAD',
                                                'payload' => "?product=product9&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 3,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product4_xjgzvp.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            [
                                                'type' => 'TEXT',
                                                'text' => '(Lehya) 500gm' // Populate bus name
                                            ],
                                            [
                                                'type' => 'TEXT',
                                                'text' => '400' // Populate bus price
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product10&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 4,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    //'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1722854073/Volvo_pgvq3f.png'
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product5_pcbdcl.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            [
                                                'type' => 'TEXT',
                                                'text' => ' 250' // Populate bus name
                                            ],
                                            [
                                                'type' => 'TEXT',
                                                'text' => '250' // Populate bus price
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product11&mobile=$mobile"
                                            ]
                                        ]
                                            ]
                                ]
                            ],
                            [
                                'card_index' => 5,
                                'components' => [
                                    [
                                        'type' => 'HEADER',
                                        'parameters' => [
                                            [
                                                'type' => 'IMAGE',
                                                'image' => [
                                                    'link' => 'https://res.cloudinary.com/drroyvl5p/image/upload/v1724922215/candy_new_lxcixh.jpg'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "BODY",
                                        "parameters" => [
                                            [
                                                'type' => 'TEXT',
                                                'text' => '250' // Populate bus name
                                            ],
                                            [
                                                'type' => 'TEXT',
                                                'text' => '175' // Populate bus price
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'URL',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => "?product=product12&mobile=$mobile"
                                            ]
                                        ]
                                    ]
                                ]
                            
                            
                            ],
                        ]
                    ]
                ]
            ]
        ];
    
        $jsonData = json_encode($data);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
    
        curl_close($ch);
        log::info($response);
        return $response;
    }
 

    public function payment($mobile)
    {   
        // Retrieve the customer record
        $customer = Customerproducts_Details::where('mobile', $mobile)
                                            ->where('delivery_detail_status', 'completed')
                                            ->where('transaction_detail_status', 'pending')
                                            ->latest()
                                            ->first();
    
        // Decode the ordered_products JSON data
        $orderedProducts = json_decode($customer->ordered_products, true);
    
        // Initialize an empty string for the services list
        $servicesList = '';
    
        // Loop through the ordered products and format them into a list
        foreach ($orderedProducts as $product) {
            $servicesList .= "📦 **{$product['name']}** - Quantity: {$product['quantity']}\n";
        }
    
        // Calculate total amount (dummy value used for now)
        $totalAmount =$customer->amount; // Example value, replace with actual calculation if needed
    
        // Make sure you set the actual name
        $name = $customer->name;
    
        $client = new Client([
            'base_uri' => 'https://graph.facebook.com/v19.0/',
        ]);
    
        $response = $client->post("$this->phoneNumberId/messages", [
            'headers' => [
                'Authorization' => 'Bearer ' . env('ACCESS_TOKEN'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $mobile,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'cta_url',
                    'header' => [
                        'type' => 'text',
                        'text' => 'Secure Payment',
                    ],
                    'body' => [
                        'text' => "\nThank you for choosing Madhumakshitha Honey Products 🍯\n\nHere are your ordered products:\n$servicesList\n💳 *Total Amount*: ₹$totalAmount\n\nPlease complete the payment by clicking the button below to finalize your order. 💼\n\n🔒 *Pay ₹$totalAmount*",
                    ],
                    'action' => [
                        'name' => 'cta_url',
                        'parameters' => [
                             'display_text' => "💰 Pay ₹$totalAmount",
                            'url' => env('ngrok') . '/payment?&mobile=' . $mobile,
                        ],
                    ],
                ],
            ],
        ]);
    
        $res = $response->getBody()->getContents();
        Log::info($res);
    }
    

public function selectedProducts(Request $request)
{
    $data = $request->all();
    $mobile=$data['mobile']??null;
    // Decode the JSON string into an array
    $products = json_decode($data['products'] ?? '[]', true);

    // Check if the decoding was successful
    if (!is_array($products)) {
        // Handle the error: return a response or log the issue
        Log::error('Invalid products data received');
        return response()->json(['error' => 'Invalid products data'], 400);
    }

    $totalCost = 0;

    foreach ($products as $product) {
        log::info($product['name']);
        log::info($product['quantity']);
        switch($product['name']){

            case 'Honey 1 kg':
                $cost = $product['quantity'] * 550;
                $totalCost += $cost;
                break;  
            case 'Honey 500gm':
                $cost = $product['quantity'] * 300;
                $totalCost += $cost;
                break;  
            case 'Honey 250gm':
                $cost = $product['quantity'] * 200;
                $totalCost += $cost;
                break;  
            case 'STINGLES BEE HONEY 250gm':
                $cost = $product['quantity'] * 500;
                $totalCost += $cost;
                break;    
            case 'JAFI (JACKFRUIT COFFEE) 250gm':
                $cost = $product['quantity'] * 200;
                $totalCost += $cost;
                break;  
            case 'Madhupeyaras 750gm':
                $cost = $product['quantity'] * 350;
                $totalCost += $cost;
                break;  
            case 'Madhusara 300gm':
                $cost = $product['quantity'] * 350;
                $totalCost += $cost;
                break;  
            case 'Madhusara 150gm':
                $cost = $product['quantity'] * 200;
                $totalCost += $cost;
                break;  
            case 'Bee wax skin care cream 50gm':
                $cost = $product['quantity'] * 130;
                $totalCost += $cost;
                break;  
            case 'Madhukalpa (Lehya)500gm':
                $cost = $product['quantity'] * 400;
                $totalCost += $cost;
                break;  
            case 'Amla candy250gm':
                $cost = $product['quantity'] * 250;
                $totalCost += $cost;
                break;  
            case 'AMLA CHATPATA CANDY 250gm':
                $cost = $product['quantity'] * 175;
                $totalCost += $cost;
                break;  
        }
        log::info($totalCost);
    }
        log::info($mobile);
        $user=DeliveryAddress::where('mobile',$mobile)->whereNotNull('state')->latest()->first();
        log::info($user);
        if($user){
            $this->oldUser($mobile,$user);
        }
        else{
            $this->newUser($mobile);
        }

       $delivery = new  DeliveryAddress();
       $delivery->mobile=$mobile;
       $delivery->save();

       $transaction = new  transactions();
       $transaction->phone=$mobile;
       $transaction->save();


        $customerProductDetail = new Customerproducts_Details();
        $customerProductDetail->name = $data['name'] ?? 'null'; // Assuming 'name' is included in the request
        $customerProductDetail->mobile = $mobile;
        $customerProductDetail->amount = $totalCost;
        $customerProductDetail->ordered_products = json_encode($products); // Store as JSON
        $customerProductDetail->delivery_detail_id = $delivery->id; // Optional
        $customerProductDetail->delivery_detail_status =  'Pending'; // Default status
        $customerProductDetail->transaction_detail_id = $transaction->id; // Optional
        $customerProductDetail->transaction_detail_status = 'Pending'; // Default status
        $customerProductDetail->save();

        $whatsappRedirectUrl = "https://wa.me/+917348839783";
        return redirect()->to($whatsappRedirectUrl);
}

    public function newUser($mobile) {
        log::info('entered in new user'); 
            $phoneNumberId = env('FROM_PHONE_NUMBER_ID'); // Replace with your actual phone number ID
            $accessToken = env('ACCESS_TOKEN'); // Replace with your actual access token
        
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $mobile,
                "type" => "interactive",
                "interactive" => [
                    "type" => "address_message",
                    "body" => [
                        "text" => "Thanks for your order! Tell us what address you’d like this order delivered to."
                    ],
                    "action" => [
                        "name" => "address_message",
                        "parameters" => [
                            "country" => "IN",
                            "values" => [
                                "phone_number" => $mobile
                            ]
                        ]
                    ]
                ]
            ];
        
            $client = new Client();
        
            try {
                $response = $client->post("https://graph.facebook.com/v15.0/{$phoneNumberId}/messages", [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => $data,
                ]);
                //log::info(json_decode($response->getBody()->getContents()));
                // Check if the request was successful
                if ($response->getStatusCode() == 200) {
                    return response()->json(['message' => 'Message sent successfully!', 'response' => json_decode($response->getBody()->getContents())], 200);
                } else {
                    return response()->json(['message' => 'Failed to send message.', 'response' => json_decode($response->getBody()->getContents())], $response->getStatusCode());
                }
            } catch (\Exception $e) {
                log::info( response()->json(['message' => 'Error: ' . $e->getMessage()], 500));
            }
        }

    public function oldUser($mobile,$user) {
       log::info('entered in old user'); 
       $phoneNumberId = env('FROM_PHONE_NUMBER_ID'); // Replace with your actual phone number ID
       $accessToken = env('ACCESS_TOKEN'); // Replace with your actual access token
             
                
            $messagingProduct = 'whatsapp'; // Set the messaging service
            
                $data = [
                    'messaging_product' => $messagingProduct,
                    'recipient_type' => 'individual',
                    'to' => $mobile,
                    'type' => 'interactive',
                    'interactive' => [
                        'type' => 'address_message',
                        'body' => [
                            'text' => 'Thanks for your order! Tell us what address you’d like this order delivered to.'
                        ],
                        'action' => [
                            'name' => 'address_message',
                            'parameters' => [
                                'country' => 'IN',
                                'saved_addresses' => [
                                    [
                                        'id' => 'address1',
                                        'value' => [
                                            'name' => $user->name,
                                            'phone_number' =>$user->phone_number,
                                            'in_pin_code' =>$user->in_pin_code,
                                            'floor_number' =>$user->floor_number,
                                            'building_name' =>$user->building_name,
                                            'address' =>$user->address,
                                            'landmark_area' =>$user->landmark_area,
                                            'city' => $user->city,
                                            'state' => $user->state,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            
                $client = new Client();
            
                try {
                    $response = $client->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json'
                        ],
                        'json' => $data,
                    ]);
                    
                    

                    // Check if the request was successful
                    if ($response->getStatusCode() == 200) {
                        log::info($response->getBody());
                    } else {
                        log::info($response->getBody());
                    }
                } catch (\Exception $e) {
                    log::info( response()->json(['message' => 'Error: ' . $e->getMessage()], 500));
                }
    }


    public function ResumableUploadAPI() {
        // Step 2: Initiate Upload
        
    
        $uploadSessionId = "upload:MTphdHRhY2htZW50OmIyZGI1ZmRjLWYxZDItNGEwNC05NDY3LTY4ZDE2NjczYjI5Yj9maWxlX2xlbmd0aD02ODA5JmZpbGVfbmFtZT1ob25leSZmaWxlX3R5cGU9aW1hZ2UlMkZqcGVn?sig=ARZFVn2Daqh2JttX2c4";
    
        $accessToken = 'EAAVqUkmcZCqEBO9zNW55fZCyyoONfmVK6pZC2heTTb0U9wkxsyeo3BQU5TEKQvE7yysppz0YGHUD4w4YfCBUgN3pJjXvt31MybxSHE0xF01w9kZB4GVRacGcqE77rYZBZADRzVXSOZAe3sjQVtEPffgCjMOCvaUuL6yVbMg1csijjQcJe49RBbSNZCpo1WfbSWahmynvtYL7D99eJx8ZCyEZBmiQZBd5sGhVYCrqQZDZD';
        $fileName = "C:\\Users\\tonys\\Downloads\\bus-1.jpg";
    
        // Define API version
        $apiVersion = 'v19.0';
    
        // Construct the API URL
        $apiUrl = "https://graph.facebook.com/{$apiVersion}/{$uploadSessionId}";
    
        // Define headers
        $headers = [
            "Authorization: OAuth {$accessToken}",
            "file_offset: 0"
        ];
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($fileName)); // Read file contents
        // Execute cURL session and get the response
        $response = curl_exec($ch);
    
        // Check for errors
        if(curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return "Error: {$error}";
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Decode the JSON response
        $responseData = json_decode($response, true);
    
        return $responseData;
    }
}


