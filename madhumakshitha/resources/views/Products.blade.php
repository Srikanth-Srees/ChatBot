<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">   <style>
        .product-container {
            max-width: 400px;
            margin-bottom: 1.5rem; /* Add spacing between products */
        }
    </style>
</head>
<body class="bg-gray-200" data-mobile="{{$mobile}}">
    <div class="container mx-auto md:p-6 p-3">
        <h1 class="text-3xl font-extrabold mb-6 text-center text-gray-800">Product List <i class="fa-solid fa-bag-shopping"></i></h1>
        
        <!-- Product 1 -->
        <div class="flex flex-col bg-white rounded-xl shadow-lg text-center border border-gray-300 mb-2 md:mb-4">
            <!-- Product Container -->
            <div class="flex flex-row justify-center product-container mx-auto p-5 mb-0" data-price="{{$requestedProductDetails['cost']}}">
                <div class="w-1/2 flex items-center justify-center">
                    <img class="w-full h-auto object-contain" src="{{$requestedProductDetails['image']}}" alt="Product Image">
                </div>
                <div class="w-1/2 flex flex-col items-center justify-center">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">{{$requestedProductDetails['name']}}</h2>
                    <div class="flex flex-row">
                        <button class="bg-gray-400 text-white px-2 py-1 rounded-lg mr-2 hover:bg-gray-500 decrement">-</button>
                        <input type="number" class="quantity w-10 md:w-24 text-center border border-gray-400 rounded-lg" value="0" min="0">
                        <button class="bg-gray-400 text-white px-2 py-1 rounded-lg ml-2 hover:bg-gray-500 increment">+</button>
                    </div>
                </div>
            </div>
            <!-- Full Width Text Container -->
            <div class="block w-full pb-3 bg-white">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800">{{$requestedProductDetails['price']}}</h1>
                    <h1 class="text-lg text-gray-600">{{$requestedProductDetails['description']}}</h1>
                </div>
            </div>
        </div>

        <!-- Show/Hide Products Button -->
        <button id="toggle-products" class="bg-black text-white border border-black rounded-md shadow-lg shadow-white/50 hover:bg-gray-800 active:shadow-inner  px-4 py-2 mt-4 block mx-auto w-full text-lg">Show More Products</button>

        <!-- Products List -->
        <div id="products-list" class="hidden">
            @foreach ($remainingProducts as $products)
                <div class="flex flex-col bg-white rounded-xl shadow-lg text-center border border-gray-300 mb-2 md:mb-4">
                    <!-- Product Container -->
                    <div class="flex flex-row justify-center product-container mx-auto p-5 mb-0" data-price="{{ $products['cost'] }}">
                        <div class="w-1/2 flex items-center justify-center">
                            <img class="w-full h-auto object-contain" src="{{ $products['image'] }}" alt="Product Image">
                        </div>
                        <div class="w-1/2 flex flex-col items-center justify-center">
                            <h2 class="text-xl font-bold text-gray-700 mb-4">{{ $products['name'] }}</h2>
                            <div class="flex flex-row">
                                <button class="bg-gray-400 text-white px-2 py-1 rounded-lg mr-2 hover:bg-gray-500 decrement">-</button>
                                <input type="number" class="quantity w-10 md:w-24 text-center border border-gray-400 rounded-lg" value="0" min="0">
                                <button class="bg-gray-400 text-white px-2 py-1 rounded-lg ml-2 hover:bg-gray-500 increment">+</button>
                            </div>
                        </div>
                    </div>
                    <!-- Full Width Text Container -->
                    <div class="block w-full pb-3 bg-white">
                        <div class="flex flex-col justify-center gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $products['price'] }}</h1>
                            <h1 class="text-lg text-gray-600">{{ $products['description'] }}</h1>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Total Price and Pay Now Button -->
        <p id="total-price" class="text-2xl font-semibold mt-4 text-center text-gray-800">Total Price: ₹0</p>
        <button id="calculate-total" class="bg-green-600 text-white px-6 py-3 rounded-lg mt-6 block mx-auto text-lg hover:bg-gray-500">PAY NOW</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('toggle-products');
            const productsList = document.getElementById('products-list');
            
            // Toggle visibility of additional products
            toggleButton.addEventListener('click', () => {
                if (productsList.classList.contains('hidden')) {
                    productsList.classList.remove('hidden');
                    toggleButton.textContent = 'Show Less Products';
                } else {
                    productsList.classList.add('hidden');
                    toggleButton.textContent = 'Show More Products';
                }
            });

            // Calculate total price and manage product quantities
            const products = document.querySelectorAll('.product-container');
            const calculateTotalButton = document.getElementById('calculate-total');
            const totalPriceElement = document.getElementById('total-price');
            const body = document.body; // Ensure body is selected correctly
            let totalPrice = 0;
            let productDetails = [];

            products.forEach(product => {
                const incrementButton = product.querySelector('.increment');
                const decrementButton = product.querySelector('.decrement');
                const quantityInput = product.querySelector('.quantity');
                const productName = product.querySelector('h2').textContent;
                const productPrice = parseFloat(product.dataset.price);

                incrementButton.addEventListener('click', () => {
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                    updateTotalPrice(productPrice);
                });

                decrementButton.addEventListener('click', () => {
                    quantityInput.value = Math.max(parseInt(quantityInput.value) - 1, 0);
                    updateTotalPrice(-productPrice);
                });

                function updateTotalPrice(priceChange) {
                    totalPrice += priceChange;
                    totalPriceElement.textContent = `Total Price: ₹${totalPrice.toFixed(2)}`;
                    updateProductDetails(productName, quantityInput.value);
                }

                function updateProductDetails(name, quantity) {
                    const existingProduct = productDetails.find(p => p.name === name);
                    if (existingProduct) {
                        existingProduct.quantity = quantity;
                    } else {
                        productDetails.push({ name, quantity });
                    }
                }
            });

            calculateTotalButton.addEventListener('click', () => {
                const mobile = body.getAttribute('data-mobile'); // Retrieve mobile value
                if (productDetails.length === 0) {
                    alert('Please select at least one product before proceeding to payment.');
                    return;
                }

                // Construct the URL for redirection
                const url = new URL('/redirectProducts', window.location.origin);
                url.searchParams.append('totalPrice', totalPrice);

                url.searchParams.append('mobile', mobile);

                // Serialize the product details as a JSON string
                url.searchParams.append('products', JSON.stringify(productDetails));

                // Redirect to the constructed URL
                window.location.href = url.toString();
            });
        });
    </script>
</body>
</html>