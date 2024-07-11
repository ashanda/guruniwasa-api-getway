<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Traits\HandlesHTTPRequests;
use Illuminate\Support\Facades\Crypt;
class AuthController extends Controller
{
    use HandlesHTTPRequests;
    private $ServiceUrl;
    private $PaymentServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }

  //Student login
    public function Studentlogin(Request $request)
    {
        // Call auth-service to authenticate user and obtain access token
        // Example: Assuming the token is returned in the response data
        $tag = 'students-login';
        $response = $this->callAuthService($request->all(),$tag);
        
        if($response['status'] == 200){
            // Save the token in the gateway's database
             $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }

//Teacher login
    public function Teacherlogin(Request $request)
    {
        // Call auth-service to authenticate user and obtain access token
        // Example: Assuming the token is returned in the response data
        $tag = 'teachers-login';
        $response = $this->callAuthService($request->all(),$tag);
        
        if($response['status'] == 200){
            // Save the token in the gateway's database
             $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }

//Staff login
    public function Stafflogin(Request $request)
    {
        // Call auth-service to authenticate user and obtain access token
        // Example: Assuming the token is returned in the response data
        $tag = 'staff-login';
        $response = $this->callAuthService($request->all(), $tag);
        
        if($response['status'] == 200){
            // Save the token in the gateway's database
             $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }
//Admin login
    public function Adminlogin(Request $request)
    {
        // Call auth-service to authenticate user and obtain access token
        // Example: Assuming the token is returned in the response data
        $tag = 'admins-login';
        $response = $this->callAuthService($request->all(), $tag);
        
        if($response['status'] == 200){
            // Save the token in the gateway's database
             $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }
//SuperAdmin login
    public function SupedAdminlogin(Request $request)
    {
        // Call auth-service to authenticate user and obtain access token
        // Example: Assuming the token is returned in the response data
        $tag = 'super-admins-login';
        $response = $this->callAuthService($request->all(), $tag);
        
        if($response['status'] == 200){
            // Save the token in the gateway's database
             $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }

      public function Studentlogout(Request $request)
    {
        // Assuming you receive the token to delete in the request
        
        $response = $this->sendHttpRequest('POST', "$this->ServiceUrl/student/logout", $request->bearerToken());
        if($response['status'] == 200){
            $this->deleteToken($request->bearerToken());
        }
        return response()->json($response);
       
    }

      public function Teacherlogout(Request $request)
    {
        // Assuming you receive the token to delete in the request
        
        $response = $this->sendHttpRequest('POST', "$this->ServiceUrl/teacher/logout", $request->bearerToken());
        if($response['status'] == 200){
            $this->deleteToken($request->bearerToken());
        }
        return response()->json($response);
       
    }

      public function Stafflogout(Request $request)
    {
        // Assuming you receive the token to delete in the request
        
        $response = $this->sendHttpRequest('POST', "$this->ServiceUrl/staff/logout", $request->bearerToken());
        if($response['status'] == 200){
            $this->deleteToken($request->bearerToken());
        }
        return response()->json($response);
       
    }

      public function Adminlogout(Request $request)
    {
        // Assuming you receive the token to delete in the request
        
        $response = $this->sendHttpRequest('POST', "$this->ServiceUrl/admin/logout", $request->bearerToken());
        if($response['status'] == 200){
            $this->deleteToken($request->bearerToken());
        }
        return response()->json($response);
       
    }

      public function SupedAdminlogout(Request $request)
    {
        // Assuming you receive the token to delete in the request
        
        $response = $this->sendHttpRequest('POST', "$this->ServiceUrl/super-admin/logout", $request->bearerToken());
        if($response['status'] == 200){
            $this->deleteToken($request->bearerToken());
        }
        return response()->json($response);
       
    }

   

    private function callAuthService($data,$tag)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client;
        $response = $http->post("$this->ServiceUrl/$tag", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                    'form_params' => [
                        'email' => $data['username'],
                        'password' => $data['password'],
                    ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    private function saveToken($accessToken, $expiresAt)
    {
        
        // Save the token in your database
        AuthToken::create([
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
        ]);
    }

    private function deleteToken($accessToken)
    {
        // Delete the token from your database
        AuthToken::where('access_token', $accessToken)->delete();

    }
}
