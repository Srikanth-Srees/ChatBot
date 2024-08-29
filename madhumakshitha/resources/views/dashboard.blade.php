<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto py-8 p-4">
    <h2 class="text-2xl font-bold mb-6">Completed Orders</h2>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="min-w-full bg-white table-fixed">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="w-1/12 text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                                <th class="w-2/12 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                                <th class="w-2/12 text-left py-3 px-4 uppercase font-semibold text-sm">Mobile</th>
                                <th class="w-2/12 text-left py-3 px-4 uppercase font-semibold text-sm">Amount</th>
                                <th class="w-3/12 text-left py-3 px-4 uppercase font-semibold text-sm">Ordered Products</th>
                                <th class="w-5/12 text-left py-3 px-4 uppercase font-semibold text-sm">Delivery Address</th>
                                <th class="w-2/12 text-left py-3 px-4 uppercase font-semibold text-sm">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @foreach($orders as $order)
                                <tr>
                                    <td class="text-left py-3 px-4">{{ $order->id }}</td>
                                    <td class="text-left py-3 px-4">{{ $order->deliveryDetail->name}}</td>
                                    <td class="text-left py-3 px-4">{{ $order->mobile }}</td>
                                    <td class="text-left py-3 px-4">{{ $order->amount }}</td>
                                    <td class="text-left py-3 px-4">
                                        @foreach(json_decode($order->ordered_products) as $product)
                                            {{ $product->name }} (x{{ $product->quantity }})<br>
                                        @endforeach
                                    </td>
                                    <td class="text-left py-3 px-4" style="width: 300px;">
                                        <strong>Address:</strong> {{ $order->deliveryDetail->address ?? 'N/A' }}<br>
                                        <strong>Landmark Area:</strong> {{ $order->deliveryDetail->landmark_area ?? 'N/A' }}<br>
                                        <strong>Building Name:</strong> {{ $order->deliveryDetail->building_name ?? 'N/A' }}<br>
                                        <strong>City:</strong> {{ $order->deliveryDetail->city ?? 'N/A' }}<br>
                                        <strong>State:</strong> {{ $order->deliveryDetail->state ?? 'N/A' }}<br>
                                        <strong>PIN Code:</strong> {{ $order->deliveryDetail->in_pin_code ?? 'N/A' }}<br>
                                        <strong>Phone:</strong> {{ $order->deliveryDetail->phone_number?? 'N/A' }}
                                    </td>
                                    
                                    <td class="text-left py-3 px-4">{{ $order->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>

</body>
</html>
