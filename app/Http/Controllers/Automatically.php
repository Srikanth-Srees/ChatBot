<?php

namespace App\Http\Controllers;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Row;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Section;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\meeting;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;

class Automatically extends Controller
{    private $whatsapp_cloud_api;
    private $accessToken;

    private $val;
    public function __construct()
    {
        // Initialize the access token
        $this->accessToken = 'EAAF9Td5py1EBO3DFZC9LrIpyvBnVJFrshOYZCnI6CZBw8KlD2jq7ywfQ9GLDjt1T9b9XZCKpRehfuwQZCRmgpScvJpwTR4czscfH2Qq6yydq2WYUEYXDpoPnAU2PaMWtbFWxxvv3iFZCfFcEhsRHAR9JuNnA52G8qvHsOnf3H24kZC6rYOha1Q1rgamT8J8Ll6D3wpw12f0Ih2ACVZBZCzd1THg2WZAzg9gzhTbKsZD';
        
        // Initialize the WhatsAppCloudApi with the access token
        $this->whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => '245096125347045',
            'access_token' => $this->accessToken,
        ]);
    }
    
    public function pay(Request $request)
    {
        Log::info($request->getContent());
        

        $payload = $request->all();
        $mobile= $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id']??null;
        $phone=$mobile;
        // Check if the message body contains "town"
    if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'])) {
        $messageBody = strtolower($payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body']);
        
        if (strpos($messageBody, 'auto') !== false) {
            $this->welcome($mobile); // Call the welcome method
            //return redirect()->route('welcome', ['mobile' => $mobile]);
            return;
        }
    }

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0]['button']['payload'])) {
            Log::info("----------------------------------------------");
            
            
            $payloadValue = $payload['entry'][0]['changes'][0]['value']['messages'][0]['button']['text'];
            switch ($payloadValue) {
                case 'Explore':
                    $this->sendTemplate($mobile);//
                    break;
                
                case 'Schedule Demo':
                    Log::info("hiiii-form");
                    
                    $Value = $payload['entry'][0]['changes'][0]['value']['messages'][0]['button']['payload'];
                    $existingMeeting = Meeting::where('phone', $mobile)->first();
                    $this->service($existingMeeting,$mobile,$Value);
                    $this->formAddress($mobile);
                    break;
        
                case 'Features':
                   Log::info("hiiii-list");
                    $this->sendList($mobile);
                    break;
                 default:
                    // Handle unexpected payload values
                    break;
            }
            
            
        }
            // Check if the payload contains the necessary keys and the action was triggered by a list reply
        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['type']) && 
        $payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['type'] === 'list_reply') {

        // Extract the id from the payload
        $listReplyId = $payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id'];

        // Call the reply method with the extracted id as a parameter
        $this->reply($listReplyId,$mobile);
        }

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'])) {
            // Retrieve the response_json data
            $responseJson = $payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'];
    
            // Call the save method and pass the response_json data as an argument
            $this->save($responseJson,$mobile);
        }
    
}

public function service($existingMeeting, $mobile, $Value)
{
    if ($existingMeeting && $existingMeeting->name != '123') {
        $meet = new Meeting();
        $meet->name = '123';
        $meet->company = 'null';
        $meet->phone = $mobile;
        $meet->date = 'null';
        $meet->time = 'null';
        $meet->service = $Value;
        $meet->save();
        Log::info('Record saved.');
    } elseif (!$existingMeeting) { // This condition will only be true if $existingMeeting is null
        $meet = new Meeting();
        $meet->name = '123';
        $meet->company = 'null';
        $meet->phone = $mobile;
        $meet->date = 'null';
        $meet->time = 'null';
        $meet->service = $Value;
        $meet->save();
        Log::info('Record else if');
    }
}



public function welcome($mobile) {//$mobile
    
    $path = Storage::url('welcome.jpg');  // Assuming main.jpg is stored in storage/app/public
    $fullPath = config('app.url') . $path;  // Get full URL

    $fullPathHttps = 'https://1bd1-2406-7400-56-7f78-b55f-b494-9e11-f9bb.ngrok-free.app/'.asset('image/welcome.jpg');


    $url = 'https://graph.facebook.com/v19.0/245096125347045/messages';
    $accessToken = $this->accessToken;

    $data = [
        'messaging_product' => 'whatsapp',
        'recipient_type' => 'individual',
        'to' =>$mobile,//$mobile,
        'type' => 'template',
        'template' => [
            'name' => 'welcome_message',
            'language' => [
                'code' => 'en_GB'
            ],
            'components' => [
                [
                    'type' => 'header',
                    'parameters' => [
                        [
                            'type' => 'image',
                            'image' => [
                                'link' =>  $fullPathHttps,//"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRPSAfb0I2GaZsukIME6FB02fdUk9jWT7SsR-eiEnKq5RNlRp7ueEX8pXex_A&s",
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'body',
                    'parameters' => []
                ],
                [
                    'type' => 'button',
                    'sub_type' => 'quick_reply',
                    'index' => '0',
                    'parameters' => [
                        [
                            'type' => 'payload',
                            'payload' => 'welcome'
                        ]
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
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}
    public function sendTemplate($mobile) {
        $url = 'https://graph.facebook.com/v19.0/245096125347045/messages';
        //$accessToken = 'EAAF9Td5py1EBO1FYIrKc2EW8OfTZAFy0Rpebc81QXcAndV3YB0nGCQsoel2ZC8tzHdYXqJcb3WMhLkRQAT12fOD1Oj6AeVkqvOi0P6ZBDeKCEnQtMEE9J6DO1NaPPWS7nW2jZAKgUwfxdbuSFZBcaVUJ1ybjnd4pbkgyBOxb9IJ20WDqYe0Kx9YKFQ1N8mUHWT0dGAIAuX7TwVpuZBsIFzaeCtwjzBmUqpuGYZD'; // Replace with your actual access token
        $accessToken = $this->accessToken;
    
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $mobile,
            'type' => 'template',
            'template' => [
                'name' => 'carousel_town',
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
                                                    'id' => '1896254550832809'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'Meta Automation'
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '1',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'faq'
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
                                                    'id' => '443059651608309'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'Chat-Bot Builder'
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '1',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'faq'
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
                                                    'id' => '7587386174675369'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'Connecting – Near By'
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '1',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'faq'
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
                                                    'id' => '759251839325975'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'AIT'
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '1',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'faq'
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
                                                    'id' => '1594738621282376'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '0',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'Links'
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'BUTTON',
                                        'sub_type' => 'QUICK_REPLY',
                                        'index' => '1',
                                        'parameters' => [
                                            [
                                                'type' => 'PAYLOAD',
                                                'payload' => 'faq'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
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
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
    
        curl_close($ch);
    
        return $response;
    }
    
    public function formAddress($mobile) {//$mobile
        
    
        $url = 'https://graph.facebook.com/v19.0/245096125347045/messages';
        //$accessToken = 'EAAF9Td5py1EBO1FYIrKc2EW8OfTZAFy0Rpebc81QXcAndV3YB0nGCQsoel2ZC8tzHdYXqJcb3WMhLkRQAT12fOD1Oj6AeVkqvOi0P6ZBDeKCEnQtMEE9J6DO1NaPPWS7nW2jZAKgUwfxdbuSFZBcaVUJ1ybjnd4pbkgyBOxb9IJ20WDqYe0Kx9YKFQ1N8mUHWT0dGAIAuX7TwVpuZBsIFzaeCtwjzBmUqpuGYZD'; // Replace with your actual access token
        $accessToken = $this->accessToken;
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $mobile,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'flow',
                'header' => [
                    'type' => 'text',
                    'text' => 'Schedule Demo'
                ],
                'body' => [
                    'text' => "Automatically Live\nplease provide the following information"
                ],
                'footer' => [
                    'text' => 'Submit'
                ],
                'action' => [
                    'name' => 'flow',
                    'parameters' => [
                        'flow_message_version' => '3',
                        'flow_token' => 'AQAAAAACS5FpgQ_cAAAAAD0QI3s',
                        'flow_id' => '449336007777786',
                        'flow_cta' => 'Schedule',
                        'flow_action' => 'navigate',
                        'flow_action_payload' => [
                            'screen' => 'SIGN_UP',
                            'data' => [
                                'screen_0_firstName_0' => '${form.firstName}',
                                'screen_0_lastName_1' => '${form.lastName}',
                                'screen_0_email_2' => '${form.email}',
                                'screen_0_DatePicker_3' => '${form.DatePicker_dcd0d1}',
                                'screen_0_Dropdown_4' => '${form.Dropdown_5021c7}'
                            ]
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
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
    
        curl_close($ch);
    
        return $response;
    }
    
    public function sendList($mobile){

        
        $rows = [
            new Row('1',  " ","How does Meta Automation revolutionize social media engagement?"),
            new Row('2',    " ","What are the key features of AI Chatbot Builder?"),
            new Row('3',    " ","How does Automatically Live enhance social media management?"),
            new Row('4',    " ","What distinguishes Automatically Live from other management software?"),
            new Row('5',    " ","How does Meta Automation simplify business communication ?"),
        ];
        $sections = [new Section('Stars', $rows)];
        $action = new Action('Submit', $sections);
        
        $list = $this->whatsapp_cloud_api->sendList(
            $mobile,
            'Curious about our features?',
            'Check out these FAQs to learn more about what we offer!',
            'Feel free to reach out if you have more questions!',
            $action
        );  
        // dd($list);
    }
    
    public function reply($listReplyId,$mobile)
{
    // Logic to handle the list reply based on the extracted id
    // For example, you can process the list reply, send a specific message, or perform an action
    // depending on the list reply id
     // Instantiate the WhatsAppCloudApi class
    //  $whatsapp_cloud_api = new WhatsAppCloudApi([
    //     'from_phone_number_id' => '245096125347045',
    //     'access_token' => 'EAAF9Td5py1EBO1FYIrKc2EW8OfTZAFy0Rpebc81QXcAndV3YB0nGCQsoel2ZC8tzHdYXqJcb3WMhLkRQAT12fOD1Oj6AeVkqvOi0P6ZBDeKCEnQtMEE9J6DO1NaPPWS7nW2jZAKgUwfxdbuSFZBcaVUJ1ybjnd4pbkgyBOxb9IJ20WDqYe0Kx9YKFQ1N8mUHWT0dGAIAuX7TwVpuZBsIFzaeCtwjzBmUqpuGYZD',
    // ]);

    // Here's a simple example:
    switch ($listReplyId) {
            case '1':
                $this->whatsapp_cloud_api->sendTextMessage($mobile, "🌟 **Meta Automation** \nrevolutionizes social media engagement by offering automated replies and AI-powered chatbots for platforms like Facebook and Instagram. 🤖 It saves time ⏰ by handling repetitive tasks, ensuring smooth interactions with the audience, and keeping followers engaged effortlessly. 🚀
                .
                ");
            break;
            case '2':
                // Logic for handling list reply with id 5
                $this->whatsapp_cloud_api->sendTextMessage($mobile,
                "🤖 **AI Chatbot Builder** offers several key features:\n\n👩‍💻 **No Coding Required:**- It eliminates the need for coding skills, making chatbot creation accessible to a wide range of users.\n\n🎨 **Intuitive Interface:**- The platform provides a user-friendly interface for easy chatbot creation with drag-and-drop elements.\n\n🌐 **Multi-Platform Support:**- It integrates with various social media platforms, ensuring seamless interaction across different channels.\n\n🧠 **AI-Powered Natural Language Processing (NLP):**- Chatbots built with this tool can understand and interpret user inputs accurately for natural conversations.\n\n📝 **Customizable Templates:**- Users can choose from a range of templates covering various use cases like customer support, lead generation, and e-commerce.\n\n📊 **Analytics and Insights:**- The platform offers robust analytics to track metrics like user engagement and conversion rates, allowing users to optimize chatbots effectively.\n\n🔗 **Integration Capabilities:**- It seamlessly integrates with existing business systems for enhanced functionality."
                );
                break;
            case '3':
                    // Logic for handling list reply with id 5
                    $this->whatsapp_cloud_api->sendTextMessage($mobile, "✨ **Automatically Live**\nenhances social media management by offering a complete Meta management software solution for WhatsApp, Facebook, and Instagram. 📱 It harnesses the power of Artificial Intelligence 🧠 for seamless integration with the latest social media technology, allowing businesses to engage with their audience more effectively. 🚀
                    .");
            break;
            case '4':
                // Logic for handling list reply with id 5
                $this->whatsapp_cloud_api->sendTextMessage($mobile, "✨ **Automatically Live** stands out due to several factors:\n\n🔍 **Meta Verified Tech Partner:** - It is verified by Meta, ensuring reliability and credibility. ✅\n\n🔗 **Effortless Integration:** - It seamlessly integrates with social media platforms for smooth operation. 🔄\n\n🧠 **Unlock the Power of AI:** - It leverages AI to enhance social media engagement and management. 🤖\n\n💳 **Secure Payment Integration:** - It offers secure payment integration for e-commerce transactions. 🔒\n\n🛠️ **Tailored Solutions:** - It provides customized solutions and dedicated support to meet specific business needs. 🎯
                ");
                break;
            case '5':
                // Logic for handling list reply with id 5
                $this->whatsapp_cloud_api->sendTextMessage($mobile, "🌐 **Meta Automation**\n\nsimplifies business communication across platforms by providing automated replies and AI chatbots.\n\n🤖 It ensures that businesses can efficiently connect with their audience on various social media channels without spending excessive time⏳ on manual responses.\n\n🔄 This streamlined communication process enhances engagement ❤️ and boosts brand visibility effectively. 🚀
                ");
                break;
        default:
            // Default logic if the list reply id doesn't match any case
            $this->sendDefaultMessage();
            break;
    }
}
public function save($responseJson, $mobile)
{   
    Log::info($mobile);
    
    // Decode the response_json data
    $formData = json_decode($responseJson, true);

    // Retrieve and log the necessary variables
    $firstName = $formData['screen_0_firstName_0'] ?? null;
    $lastName = $formData['screen_0_lastName_1'] ?? null;
    $timestamp = $formData['screen_0_DatePicker_3'] ?? null;
    $dropdownValue = $formData['screen_0_Dropdown_4'] ?? null;

    Log::info($timestamp);
    // Convert timestamp to a readable date format with time in 24-hour format
    date_default_timezone_set('Asia/Kolkata'); // Set timezone to IST
    $datePicker = date('Y-m-d', $timestamp / 1000);
    Log::info('normal date'.$datePicker);

    $currentDate = date('Y-m-d');
    Log::info('current date'.$currentDate);

    $timeRange = explode('-', $dropdownValue);
     $startTime = trim($timeRange[0]);
     Log::info('meeting time'.$startTime);

     $currentHour = date('H');
     Log::info('current time'.$currentHour);

     if ($datePicker> $currentDate) {
    // Retrieve all records with the given phone number
            $existingMeetings = Meeting::where('phone', $mobile)->get();

            foreach ($existingMeetings as $existingMeeting) {
                if ($existingMeeting->name === '123') {
                    Log::info("Phone number exists and name is '123'. Updating record.");

                    // Update the 'service', 'name', and 'updated_at' columns of the existing record
                    $existingMeeting->name = $firstName;
                    $existingMeeting->company = $lastName;
                    $existingMeeting->date = $datePicker;
                    $existingMeeting->time = $dropdownValue; // Or any other name value you want to set
                    $existingMeeting->updated_at = now(); // Update the 'updated_at' timestamp
                    $existingMeeting->save();
                    $this->whatsapp_cloud_api->sendTextMessage($mobile, "📅 Thanks for sending over your details. We're on it and will be in touch shortly to set up our meeting🤝");
                    Log::info('Record updated.');
                }
            }
    
    }
    else if($datePicker>= $currentDate && $startTime > $currentHour){
        $existingMeetings = Meeting::where('phone', $mobile)->get();

        foreach ($existingMeetings as $existingMeeting) {
            if ($existingMeeting->name === '123') {
                Log::info("Phone number exists and name is '123'. Updating record.");

                // Update the 'service', 'name', and 'updated_at' columns of the existing record
                $existingMeeting->name = $firstName;
                $existingMeeting->company = $lastName;
                $existingMeeting->date = $datePicker;
                $existingMeeting->time = $dropdownValue; // Or any other name value you want to set
                $existingMeeting->updated_at = now(); // Update the 'updated_at' timestamp
                $existingMeeting->save();
                $this->whatsapp_cloud_api->sendTextMessage($mobile, "📅 Thanks for sending over your details. We're on it and will be in touch shortly to set up our meeting🤝");
                Log::info('Record updated.');
            }
        }
    }
    else {

        $this->whatsapp_cloud_api->sendTextMessage($mobile, "📅Enter valid date and time");
        $this->formAddress($mobile);
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
}