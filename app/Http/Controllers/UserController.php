<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use HandlesHTTPRequests;
    use S3UploadTrait;
    private $UserServiceUrl;
    private $CoreServiceUrl;
    private $PaymentServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->UserServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
        $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->PaymentServiceUrl = env('PAYMENT_SERVICE');
        $this->apiKey = env('API_KEY');
    }

    public function index()
    {
        $client = new Client();
        $response = $client->get('http://user-service/api/users');
        return $response->getBody();
    }

    public function allStaff(Request $request)
    {
       try {
            // First service call to get student data
            $response_serviceCall = $this->callServiceStudent($request->bearerToken());
            
            
            $status = $response_serviceCall['status'];
            // Validate if 'data' field exists and is an array
            if ($status == 200) {      
                
                return response()->json([
                    'status' => $status,
                    'staff' => $response_serviceCall,
                ], 200);
            } else {
                return response()->json(['error' => 'No staff found in response'], 400);
            }
        } catch (Exception $exception) {
            // Log the exception message for debugging
            Log::error('Error in StudentSearch: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    private function callServiceStudent($access_token = null)
    {
        $http = new Client();
        $response = $http->get("$this->UserServiceUrl/all-staff", [
            'headers' => [
                'API-Key' => $this->apiKey,
                'Authorization' => $access_token ? 'Bearer ' . $access_token : null, 
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
