<?php

namespace App\Http\Controllers\Globle;

use App\Http\Controllers\Controller;
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
    public function sms(Request $request)
    { 
        $slug = "send-sms"; 
        $response = $this->callService($request->all(),$slug);
       
        return response()->json($response);

    }

    public function sendOtp(Request $request)
    { 
         $slug= "send-otp";
        $response = $this->callService($request->all(),$slug);
       
        return response()->json($response);

    }


    private function callService($data,$slug)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->post("$this->ServiceUrl/$slug", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'form_params' => [
                        'phone' => $data['phone'],
                         'message' => $data['message'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

}
