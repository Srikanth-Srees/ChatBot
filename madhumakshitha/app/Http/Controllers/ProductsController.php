<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
class ProductsController extends Controller
{
    public function product(Request $request) {
        $productKey = $request->query('product'); // Example: 'product1'
        $mobile = $request->query('mobile');
        
        // Define the product details
        $products = [
            'product1' => [
                'name' => 'Honey 1 kg',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product1_rzawog.jpg',
                'price' => 'Price: ₹550',
                'cost' => '550',
                'description' => 'Collected from a honey box, harvested naturally.',
            ],
            'product2' => [
                'name' => 'Honey 500gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product2_lzdjtj.jpg',
                'price' => 'Price: ₹300',
                'cost' => '300',
                'description' => 'Collected from a honey box, harvested naturally.',
            ],
            'product3' => [
                'name' => 'Honey 250gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product3_evzape.jpg',
                'price' => 'Price: ₹200',
                'cost' => '200',
                'description' => 'Collected from a honey box, harvested naturally.',
            ],
            'product4' => [
                'name' => 'STINGLES BEE HONEY 250gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724840432/temp1_card4_1_ugn9vj.jpg',
                'price' => 'Price: ₹500',
                'cost' => '500',
                'description' => 'Nutrient-Rich, Raw, and Pure Honey with Health Benefits',
            ],
            'product5' => [
                'name' => 'JAFI (JACKFRUIT COFFEE) 250gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724744152/product6_1_1_azqerj.jpg',
                'price' => 'Price: ₹200',
                'cost' => '200',
                'description' => 'A Caffeine-Free Coffee Alternative Made from Roasted Jackfruit Seeds and Cardamom',
            ],
            'product6' => [
                'name' => 'Madhupeyaras 750gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724739136/product7_cmlg2w.jpg',
                'price' => 'Price: ₹350',
                'cost' => '350',
                'description' => 'Our Raw Honey is unprocessed and retains all its natural goodness from the Western Ghats.',
            ],
            'product7' => [
                'name' => 'Madhusara 300gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724845066/MADUSARA_472_1_pu6htj.jpg',
                'price' => 'Price: ₹350',
                'cost' => '350',
                'description' => 'Honey-based Madhusara Syrup with Tulsi and Ginger provides quick, side-effect-free cough relief.',
            ],
            'product8' => [
                'name' => 'Madhusara 150gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724845066/MADUSARA_472_1_pu6htj.jpg',
                'price' => 'Price: ₹200',
                'cost' => '200',
                'description' => 'Honey-based Madhusara Syrup with Tulsi and Ginger provides quick, side-effect-free cough relief.',
            ],
            'product9' => [
                'name' => 'Bee wax skin care cream 50gm',
                'image'=>'https://madhumakshika.com/wp-content/uploads/2023/11/WAX-scaled.jpg',
                'price' => 'Price: ₹130',
                'cost' => '130',
                'description' => 'Natural blend of bee wax, oils, and herbs. Heals cracks and enhances skin glow',
            ],
            'product10' => [
                'name' => 'Madhukalpa (Lehya)500gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product4_xjgzvp.jpg',
                'price' => 'Price: ₹400',
                'cost' => '400',
                'description' => 'Madhukalpa boosts immunity, purifies blood, and enhances overall health',
            ],
            'product11' => [
                'name' => 'Amla candy250gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product5_pcbdcl.jpg',
                'price' => 'Price: ₹250',
                'cost' => '250',
                'description' => 'Health Benefits of Gooseberry Candy with Honey: Blood Sugar, Immunity, and Digestion',
            ],
            'product12' => [
                'name' => 'AMLA CHATPATA CANDY 250gm',
                'image'=>'https://res.cloudinary.com/drroyvl5p/image/upload/v1724738928/product5_pcbdcl.jpg',
                'price' => 'Price: ₹175',
                'cost' => '175',
                'description' => 'Cures acidity, indigestion,skin disorders boosts resistance against respiratory issues like cough and cold',
            ],
        ];
    
        // Check if the product key exists in the array
        if (array_key_exists($productKey, $products)) {
            $requestedProductDetails = $products[$productKey];
            // Remove the requested product from the products array to get the remaining products
            $remainingProducts = array_diff_key($products, [$productKey => $requestedProductDetails]);
    
            // Log the requested product details and remaining products
            Log::info('Requested Product: ', $requestedProductDetails);
            Log::info('Remaining Products: ', $remainingProducts);

            return view('Products',compact('requestedProductDetails','remainingProducts','mobile'));
    
            // Further processing can be done with $requestedProductDetails and $remainingProducts
        } else {
            // Handle the case where the product key doesn't exist
            Log::info('Product not found.');
        }


    }

    public function scrape(Request $request)
    {
        $url = "https://www.linkedin.com/search/results/content/?datePosted=%22past-24h%22&keywords=%22java%20developer%22%20or%20%22software%20developer%22%20on%20hiring&origin=FACETED_SEARCH&sid=mcv";

        // Initialize Guzzle client
        $client = new Client();

        try {
            $response = $client->request('GET', $url);

            // Check if the request was successful
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to fetch the page');
            }

            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            // Example: Extract all text from the page and search for phone numbers
            $textContent = $crawler->filter('body')->text();

            // Regular expression to match phone numbers (you may need to adjust this for different formats)
            // Regular expression to match phone numbers more accurately
            $phonePattern = '/\+?\d{1,4}[\s\-\.]?\(?\d{1,4}\)?[\s\-\.]?\d{1,4}[\s\-\.]?\d{1,9}/';
            $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/';

            //preg_match_all($phonePattern, $textContent, $matches);
            preg_match_all($emailPattern, $textContent, $matches);
           // $phoneNumbers = array_unique($matches[0]);
            $emails = array_unique($matches[0]);
            return response()->json([
                //'phone_numbers' => $phoneNumbers,
                'emails' => $emails
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
}
