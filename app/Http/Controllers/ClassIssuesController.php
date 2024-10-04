<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassIssuesController extends Controller
{
    use HandlesHTTPRequests;
    use S3UploadTrait;
    private $UserServiceUrl;
    private $CoreServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->UserServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
         $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }

public function index(Request $request){
    try {
        $http = new Client();
        
        // Step 1: Get class issues data
        $response = $http->get("$this->CoreServiceUrl/class_issues", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'query' => [
                'month' => $request->month,
            ],
        ]);
        
        $classIssues = json_decode((string) $response->getBody(), true);

        // Step 2: Loop through each class issue and lesson to fetch teacher data (by reference)
        foreach ($classIssues['data'] as &$issue) { // Use & to modify the array by reference
            
            if (isset($issue['lessons']['teacher_id'])) {
                
                $teacherId = $issue['lessons']['teacher_id'];
                
                // Step 3: Fetch teacher data using callServiceSingleTeacher for each lesson
                $teacherData = $this->callServiceSingleTeacher($teacherId);
                

                // Step 4: Add the fetched teacher data to the corresponding lesson
                $issue['lessons']['teacher'] = $teacherData; // This will update the teacher field in lessons
            }
        }

        // Return the modified class issues data with teacher information
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $classIssues,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => [],
        ], 400);
    }
}

// Helper function to call the single-teacher API with teacher_id
private function callServiceSingleTeacher($teacherId, $access_token = null)
{
    try {
        // Initiate HTTP client
        $http = new Client();

        // Make the API call to fetch teacher data
        $response = $http->get("$this->UserServiceUrl/single-teacher", [
            'headers' => [
                'API-Key' => $this->apiKey,
                'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
            ],
            'query' => [ // Send the correct parameter
                'teacher_id' => $teacherId,
            ],
        ]);
        
        // Decode the API response and return the data
        return json_decode((string) $response->getBody(), true);

    } catch (\Exception $e) {
        // Handle the error and return a meaningful message
        return [
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => [],
        ];
    }
}

public function Videoindex(Request $request){
     try {
        $http = new Client();
        
        // Step 1: Get class issues data
        $response = $http->get("$this->CoreServiceUrl/video_rec_issues", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'query' => [
                'month' => $request->month,
            ],
        ]);
        
        $classIssues = json_decode((string) $response->getBody(), true);
        
        // Step 2: Loop through each class issue and lesson to fetch teacher data (by reference)
        foreach ($classIssues['data'] as &$issue) { // Use & to modify the array by reference
            
            if (isset($issue['lessons']['teacher_id'])) {
                
                $teacherId = $issue['lessons']['teacher_id'];
                
                // Step 3: Fetch teacher data using callServiceSingleTeacher for each lesson
                $teacherData = $this->callServiceSingleTeacher($teacherId);
                

                // Step 4: Add the fetched teacher data to the corresponding lesson
                $issue['lessons']['teacher'] = $teacherData; // This will update the teacher field in lessons
            }
        }

        // Return the modified class issues data with teacher information
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $classIssues,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => [],
        ], 400);
    }
}

public function remark(Request $request){
try {
        // Initiate HTTP client
        
    $http = new Client();

        // Make the API call to fetch teacher data
        $response = $http->post("$this->CoreServiceUrl/update_store_class_issues", [
            'headers' => [
                'API-Key' => $this->apiKey,
                
            ],
            'json' => [
                'issue_id' => $request->issue_id,
                'remark' => $request->remark,
                'user_id' => $request->user_id
            ]
        ]);
        
        // Decode the API response and return the data
        return json_decode((string) $response->getBody(), true);

    } catch (\Exception $e) {
        // Handle the error and return a meaningful message
        return [
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => [],
        ];
    }


}

public function Videoremark(Request $request){
try {
        // Initiate HTTP client
        
    $http = new Client();

        // Make the API call to fetch teacher data
        $response = $http->post("$this->CoreServiceUrl/update_video_rec_issues", [
            'headers' => [
                'API-Key' => $this->apiKey,
                
            ],
            'json' => [
                'issue_id' => $request->issue_id,
                'remark' => $request->remark,
                'user_id' => $request->user_id
            ]
        ]);
        
        // Decode the API response and return the data
        return json_decode((string) $response->getBody(), true);

    } catch (\Exception $e) {
        // Handle the error and return a meaningful message
        return [
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => [],
        ];
    }


}






}
