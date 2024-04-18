<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
class carouselcontroller extends Controller
{

    //  we can create a carousel template using code present in meta for developers 

//  but we feel  some what trickly while creating the card header 
//  type": "HEADER",
//   "format": "<CARD_HEADER_FORMAT>",
//    "example": {
//    "header_handle": ["<CARD_HEADER_HANDLE>"]
//
// CARD_HEADER_HANDLE we can create in Resumable Upload API
//Resumable Upload API----1)session id 2)upload image using the session id 

//1)Session Id : we can create simply by sending request to 
//curl -X POST \
//"https://graph.facebook.com/v19.0/584544743160774/uploads?file_length=109981&file_type=image/png&access_token=EAAIT..." in graph api 

//2)upload image using the session id : below is the code 

public function ResumableUploadAPI() {
    // Step 2: Initiate Upload
    

    $uploadSessionId = "upload:MTphdHRhY2htZW50OmU4ZmMxMzQ2LTgyYjEtNDM4ZC04YTBjLTUyODUwZjg2MzUzZj9maWxlX2xlbmd0aD04NzkyJmZpbGVfdHlwZT1pbWFnZSUyRmpwZWc=?sig=ARY94ih-n7MPBkcjmmk";

    $accessToken = "EAALUTFGmTbYBOylcbEipNW6xzqbpZBzJQOiRijI86RTPIZBQMIFZCHQVZCPlehyWjnNykbaadE5Sv9qI11yHvnZCZBhCpEyx9qGsDb0rqubZBwz1bMMpxbG2dQMgFzxQ85HG9zuLyEl8FuOGxL1CuhqFwCYaBjEuGZApYeGZApbBhPef6K7dYWkqc6Fy7ZCNiJZAOvgPNZA5fSPgeNkUZANBLr5EZD";
    $fileName = "C:\\Users\\tonys\\Downloads\\carousel2.jpeg";

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

//after creating the Carousel Template to send the template we need image ID : below is the code for getting image id 

public function uploadImage()
    {
            $phoneNumberId = '199957899878586'; // Replace with your actual phone number ID
        $accessToken = 'EAADZAFtRmkG8BO4eaXfzt9nLQ8GgzD4KfexikCE5McAOLPXIqgqRTDdNSWN1k4LB1MSa4Fq7sxWzPCYL9GoC9jrxe3odqIDZBtuIFlhHqqqy3SxFjx9eiQ5G0JXWqkEoLUpslglmUw70CY2p7r004WQbKTKoKpvZAtCberDcdQI3wmwUzP7dAc8eUQG8nALuWXNbZBWCuVYrn568KJ8Y'; // Replace with your actual access token
        $file = 'https://amateurphotographer.com/wp-content/uploads/sites/7/2023/09/Samsung_S23Ultra_vs_iPhone15ProMax_03.jpg'; // Replace with the path to your media file
        $type = 'image/jpeg'; // Set the type of media file being uploaded
        $messagingProduct = 'whatsapp'; // Set the messaging service

        $client = new Client();
        $response = $client->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/media", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($file, 'r'),
                    'filename' => basename($file),
                ],
                [
                    'name' => 'type',
                    'contents' => $type,
                ],
                [
                    'name' => 'messaging_product',
                    'contents' => $messagingProduct,
                ],
            ],
        ]);

        // Check if the upload was successful
        if ($response->getStatusCode() == 200) {
            $mediaId = json_decode($response->getBody())->id;
            return $mediaId;
        } else {
            return 'Failed to upload media.';
        }
    }

    //sending the carousel template~
public function carouselTemplate() {
    $url = 'https://graph.facebook.com/v19.0/199957899878586/messages';
    $accessToken = 'EAALUTFGmTbYBOylcbEipNW6xzqbpZBzJQOiRijI86RTPIZBQMIFZCHQVZCPlehyWjnNykbaadE5Sv9qI11yHvnZCZBhCpEyx9qGsDb0rqubZBwz1bMMpxbG2dQMgFzxQ85HG9zuLyEl8FuOGxL1CuhqFwCYaBjEuGZApYeGZApbBhPef6K7dYWkqc6Fy7ZCNiJZAOvgPNZA5fSPgeNkUZANBLr5EZD'; // Replace with your actual access token

    $data = [
        'messaging_product' => 'whatsapp',
        'recipient_type' => 'individual',
        'to' => '918639647144',
        'type' => 'template',
        'template' => [
            'name' => 'summer_carousel_promo_2023',
            'language' => [
                'code' => 'en_US'
            ],
            'components' => [
                [
                    'type' => 'BODY',
                    'parameters' => [
                        [
                            'type' => 'TEXT',
                            'text' => '20OFF'
                        ],
                        [
                            'type' => 'TEXT',
                            'text' => '20%'
                        ]
                    ]
                ],
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
                                                'id' => '440863815001452'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'BODY',
                                    'parameters' => [
                                        [
                                            'type' => 'TEXT',
                                            'text' => '10OFF'
                                        ],
                                        [
                                            'type' => 'TEXT',
                                            'text' => '10%'
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
                                            'payload' => '59NqSd'
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'button',
                                    'sub_type' => 'URL',
                                    'index' => '1',
                                    'parameters' => [
                                        [
                                            'type' => 'payload',
                                            'payload' => 'last_chance_2023'
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
                                                'id' => '440863815001452'
                                            ]
                                        ]
                                    ]
                           ],
                                [
                                    'type' => 'BODY',
                                    'parameters' => [
                                        [
                                            'type' => 'TEXT',
                                            'text' => '10OFF'
                                        ],
                                        [
                                            'type' => 'TEXT',
                                            'text' => '10%'
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
                                            'payload' => '59NqSd'
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'BUTTON',
                                    'sub_type' => 'URL',
                                    'index' => '1',
                                    'parameters' => [
                                        [
                                            'type' => 'payload',
                                            'payload' => 'last_chance_2023'
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
                                                'id' => '964710851685404'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'BODY',
                                    'parameters' => [
                                        [
                                            'type' => 'TEXT',
                                            'text' => '30OFF'
                                        ],
                                        [
                                            'type' => 'TEXT',
                                            'text' => '30%'
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
                                            'payload' => '7C4xhY'
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'BUTTON',
                                    'sub_type' => 'URL',
                                    'index' => '1',
                                    'parameters' => [
                                        [
                                            'type' => 'payload',
                                            'payload' => 'summer_blues_2023'
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
}
