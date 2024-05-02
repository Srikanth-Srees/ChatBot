<?php

namespace App\Http\Controllers;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Netflie\WhatsAppCloudApi\Message\Template\Component;
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
    

    $uploadSessionId = "upload:MTphdHRhY2htZW50OjMxZjA0YTkyLWIwODEtNGZiYy05N2FjLWRkYjM4MjQ5NDJiZD9maWxlX2xlbmd0aD0yNzY5NCZmaWxlX3R5cGU9aW1hZ2UlMkZqcGc=?sig=ARaEJ13ziD7HBW9M75k";

    $accessToken = "EAAF9Td5py1EBO6nh9llPU0jN5ZAmsJndjuAsF1s5ve2FGzmwqZB30qacriBzoRLFmZARD4t6o2qM14aZApXzLXaBjZCOgptygmR4N7dIQwASuM5QNNhr2ZBiXVJoBZB9HMftcux7Cdc57qilNzVHFWRsgUrxGnM8vJAwEbaEI7W35o8CljasMTFzXa8OcZAeZCTGhGUSiiCeX4BNuNwy9vhaAAfmk8kQaEgyxy5AZD";
    $fileName = "C:\\Users\\tonys\\Downloads\\final-card.jpg";

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
            $phoneNumberId = '245096125347045'; // Replace with your actual phone number ID
        $accessToken = 'EAAF9Td5py1EBOwcIEWAEO7AGGYTgT0U6QNbdyTdHtoIb0WZBDpc1sGBe9YnQTK7CZB892MZBeSpDSL8wzCmxoNBIqxT6M2B1luAxM6bHKhstFt76EOZCNTWvQH1L7rhrowkv3ZAMrQQJFnLaAq51adO0QlBYfzZCvuJB5ccDQp3E1ZAENBiHpJwoZCw9aTbXyZBubf4fwzMfap3F4wgR3QVy8wbjCOD4bEFn7ZAXAZD'; // Replace with your actual access token
        $file = "C:\\Users\\tonys\\Downloads\\Untitled design (5).png"; // Replace with the path to your media file
        $type = 'image/png'; // Set the type of media file being uploaded
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
}
