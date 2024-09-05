<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

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

 public function studentSubject(Request $request)
{
    try {
        // Validate request
        $subjectWithStudents = []; // Initialize as an array to store results
        
        if (!is_array($request->subject_id)) {
            return response()->json(['error' => 'Invalid subject_id format'], 400);
        }

        // Iterate over each subject in the response
        foreach ($request->subject_id as $subject) {
            $result = $this->callStudentSubjects($subject);

            // Check if 'data', 'student_subjects', and 'tid' exist in the result
            if (isset($result['data']['student_subjects']['tid'])) {
                
                // Fetch the teacher data only if 'tid' is valid
                $teacher = $this->callTeahcerdata($result['data']['student_subjects']['tid'], $request->bearerToken());

                // If the result exists and is valid, append the teacher data to the 'student_subjects'
                if ($result && $teacher) {
                    $result['data']['student_subjects']['teacher'] = $teacher;
                    $subjectWithStudents[] = $result;
                }
            } else {
                Log::error('Invalid response structure or null value detected:', $result);
            }
        }

        // Prepare the final response
        $response = [
            'status' => 200,
            'message' => 'Current teacher subject retrieved successfully',
            'data' => [
                'student_subject' => $subjectWithStudents
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


     private function callStudentSubjects($data)
    {
        
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/student-subject", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'subject_id' => $data,
                ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }


     private function callTeahcerdata($data,$access_token)
    {
        
         $http = new Client();
        $response = $http->get("$this->ServiceUrl/get-teacher", [
                'headers' => [
                    'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data,
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }


    

    public function studentSubjectTerm(Request $request)
{
    try {
        // Validate request
        $subjectWithStudents = []; // Initialize as an array to store results
        
        if (!is_array($request->subject_id)) {
            return response()->json(['error' => 'Invalid subject_id format'], 400);
        }

        // Iterate over each subject in the response
        foreach ($request->subject_id as $subject) {
            $result = $this->callStudentSubjectsterm($subject);

            // Check if 'data', 'student_subjects', and 'tid' exist in the result
            if (isset($result['data']['student_subjects']['tid'])) {
                
                // Fetch the teacher data only if 'tid' is valid
                $teacher = $this->callTeahcerdataterm($result['data']['student_subjects']['tid'], $request->bearerToken());

                // If the result exists and is valid, append the teacher data to the 'student_subjects'
                if ($result && $teacher) {
                    $result['data']['student_subjects']['teacher'] = $teacher;
                    $subjectWithStudents[] = $result;
                }
            } else {
                Log::error('Invalid response structure or null value detected:', $result);
            }
        }

        // Prepare the final response
        $response = [
            'status' => 200,
            'message' => 'Current teacher subject retrieved successfully',
            'data' => [
                'student_subject' => $subjectWithStudents
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


     private function callStudentSubjectsterm($data)
    {
        
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/student-subject-term", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'subject_id' => $data,
                ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }


     private function callTeahcerdataterm($data,$access_token)
    {
        
         $http = new Client();
        $response = $http->get("$this->ServiceUrl/get-teacher", [
                'headers' => [
                    'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data,
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    
}
