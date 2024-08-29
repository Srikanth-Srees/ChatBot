<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">  
    <title>Invoice</title>
</head>
<body class="m-0 h-screen bg-gray-200 p-3 md:p-6">
    <div class="text-center">
        <div class="flex flex-row justify-center gap-8 mb-4">
            <h1 class="text-3xl text-black-500 font-bold">Payment Portal <i class="fa-regular fa-credit-card"></i></h1>
        </div>
         <div class="w-full md:w-1/2 bg-white rounded-xl shadow-lg text-center md:mx-auto p-4">
            <h1 class="text-2xl text-white font-bold p-3 text-left bg-gradient-to-r from-cyan-500 to-blue-500">Product Details &nbsp;🛒</h1>  
            
            <!-- Product Details Section -->
            <div class="flex flex-col gap-4 text-left mt-4">
                <!-- Example Product Item -->
                @foreach ($orderedProducts as  $p)
                <div class="flex justify-between p-2 border-b">
                    <span class="font-semibold">{{$p['name']}}</span>
                    <span>{{$p['quantity']}} x {{$productPrices[$p['name']]}} </span>
                </div>
                @endforeach
               
            </div>
            
            <!-- Payment Summary Section -->
            <div class="mt-6 p-4 border-t border-gray-300">
                <h2 class="text-xl font-bold mb-2">Payment Summary</h2>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Total Amount:</span>
                    <span>₹{{$cost}}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Amount to Pay:</span>
                    <span>₹{{$cost}}</span>
                </div>
                
                <!-- Payment Button -->
                <a href="{{env('ngrok')}}/phonepe?mobile={{$mobile}}" class="mt-4 inline-block bg-green-500 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition duration-300">Pay Now</a>
            </div>
        </div>
    </div>
</body>
</html>
