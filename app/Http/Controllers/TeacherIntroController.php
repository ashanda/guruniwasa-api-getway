<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TeacherIntroController extends Controller
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


    public function teacherIntro(Request $request)
    {
        try {
            $response_serviceCall = $this->callServiceTeacherIntro($request->all());
            $response = [
                'status' => 200,
                'message' => 'Teacher intro update successfully',
                'data' => [
                    'intro' => $response_serviceCall,
                ],
            ];
            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 400,
                'message' => $exception->getMessage(),
                'data' => [],
            ], 400);
        }
    }       


    private function callServiceTeacherIntro($data)
    {
        // Make a request to auth-service to authenticate and get token
        $http = new Client();
        $response = $http->post("$this->CoreServiceUrl/update_teacher_intro", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'query' => [
                'teacher_id' => $data['teacher_id'],
                'video_url' => $data['video_url'],
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
    }   

}
