<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Traits\HandlesHTTPRequests;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use HandlesHTTPRequests;
    private $ServiceUrl;
    private $CoreServiceUrl;
    private $PaymentServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
         $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }

  //Student login
    public function Studentlogin(Request $request)
    {
        // Call auth-service to authenticate user and obtain access token
        // Example: Assuming the token is returned in the response data
        $clientKey = $request->header('CLIENT-KEY');

        if ($clientKey !== $this->apiKey) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Invalid API key.',
            ], 401);
        }

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
         $clientKey = $request->header('CLIENT-KEY');

        if ($clientKey !== $this->apiKey) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Invalid API key.',
            ], 401);
        }
        
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

    public function Studentregister(Request $request){
        $tag = 'student-register';
        $response = $this->callRegisterService($request->all(), $tag);

        if($response['status'] == 200){
            // Save the token in the gateway's database
             $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }
    
   
    private function callRegisterService($data,$tag)
    {
        // Make a request to auth-service to authenticate and get token
        Log::info($data);
        $http = new Client;
        $response = $http->post("$this->ServiceUrl/$tag", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                    'form_params' => [
                        'username' => $data['username'],
                        'subject' => $data['subject_list'],
                        'password' => $data['password'],
                        'full_name' => $data['full_name'],
                        'student_code' => $data['student_code'],
                        'birthday' => $data['birthday'],
                        'gender' => $data['gender'],
                        'address' => $data['address'],
                        'school' => $data['school'],
                        'district' => $data['district'],
                        'city' => $data['city'],
                        'parent_phone' => $data['parent_phone'],
                        'grade' => $data['grade'],
                    ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
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



    //check auth token
    public function checkAuthStudent(Request $request)
{
    $response_user = $this->sendHttpRequest('GET', "$this->ServiceUrl/student/check-auth", $request->bearerToken());

    if ($response_user['status'] === 200) {
        $grade = $response_user['data']['grade'];
        
        // Make GET request to another endpoint with API-Key header
        $http = new Client();
        $grades_response = $http->get("$this->CoreServiceUrl/grades/{$grade}", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
        ]);

        // Decode the grades response
        $grades_data = json_decode($grades_response->getBody(), true);

        // Merge grades data into the response_user data array
        $response_user['data']['grades'] = $grades_data['data'];
    } else {
        // Handle unauthorized or other status codes
    }

    return response()->json($response_user);
}

    public function checkAuthTeacher(Request $request)
    {
        $response = $this->sendHttpRequest('GET', "$this->ServiceUrl/teacher/check-auth", $request->bearerToken());
        return response()->json($response);
    }

    public function checkAuthStaff(Request $request)
    {
        $response = $this->sendHttpRequest('GET', "$this->ServiceUrl/staff/check-auth", $request->bearerToken());
        return response()->json($response);
    }

    public function checkAuthAdmin(Request $request)
    {
        $response = $this->sendHttpRequest('GET', "$this->ServiceUrl/admin/check-auth", $request->bearerToken());
        return response()->json($response);
    }

    public function checkAuthSuperAdmin(Request $request)
    {
        $response = $this->sendHttpRequest('GET', "$this->ServiceUrl/super-admin/check-auth", $request->bearerToken());
        return response()->json($response);
    }

    
}
