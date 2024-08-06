<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    private $ServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }
    //Send Otp
    public function sendOtp(Request $request)
    { 
         
        $response = $this->callService($request->all());
       
        return response()->json($response);

    }



    private function callService($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->post("$this->ServiceUrl/send-otp", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'form_params' => [
                        'phone' => $data['phone'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

}
