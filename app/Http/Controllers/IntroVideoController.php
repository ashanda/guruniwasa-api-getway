<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class IntroVideoController extends Controller
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

    public function student(Request $request)
    {
        $tag = 'student-intro';
        try {
            $response_serviceCall = $this->callService($tag);
            return response()->json($response_serviceCall, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
    public function teacher(Request $request)
    {
        $tag = 'teacher-intro';
        try {
            $response_serviceCall = $this->callService($tag);
            return response()->json($response_serviceCall, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
    public function staff(Request $request)
    {
        $tag = 'staff-intro';
        try {
            $response_serviceCall = $this->callService($tag);
            return response()->json($response_serviceCall, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
    public function admin(Request $request)
    {
        $tag = 'admin-intro';
        try {
            $response_serviceCall = $this->callService($tag);
            return response()->json($response_serviceCall, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
       private function callService($tag)
    {
        
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/$tag", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

}
