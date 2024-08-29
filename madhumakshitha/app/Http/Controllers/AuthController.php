<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Customerproducts_Details;
use App\Models\DeliveryAddress;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Log::info('Request Data:', $request->all()); // Log all request data
        Log::info('Request Method:', ['method' => $request->method()]);
        Log::info('Request Path:', ['path' => $request->path()]);
    

       // Validate input
       $credentials = $request->only('email', 'password');
    //    dd($credentials);

       // Find the user by email
       $user = User::where('email', $credentials['email'])->first();

       // Check if user exists and the password is correct
       if ($user) {
           if (Hash::check($credentials['password'], $user->password)) {
               // Authentication passed, log in the user
               Auth::login($user);

               // Redirect to the intended URL or default dashboard
               return redirect()->route('display');
           } else {
               // Password does not match, redirect back with error
               return redirect()->back()->withErrors([
                   'password' => 'The provided password is incorrect.',
               ]);
           }
       } else {
           Log::info('coming to else');
           // User does not exist, redirect back with error
           return redirect()->back()->withErrors([
               'email' => 'The provided email does not match our records.',
           ]);
       }
    }

    public function dashboard()
{
    // Fetch the completed orders along with their delivery details
    $orders = Customerproducts_Details::where('delivery_detail_status', 'completed')
                ->where('transaction_detail_status', 'completed')
                ->with('deliveryDetail') // Use the correct relationship method name
                ->paginate(10); // 10 orders per page
                
    // Return the view with the orders data
    return view('dashboard', compact('orders'));
}

}
