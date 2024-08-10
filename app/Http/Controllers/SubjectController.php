<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    use HandlesHTTPRequests;
    private $ServiceUrl;
    private $CoreServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
         $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }
    public function getSubject(Request $request)
    {
        try {
            $response_serviceCall = $this->callService($request->all());
            return response()->json($response_serviceCall, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
       private function callService($data)
    {
        
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->post("$this->CoreServiceUrl/get-subject", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'subject_ids' => $data['subjects'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    
}
