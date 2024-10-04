<?php

namespace App\Http\Controllers\Globle;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
        private $ServiceUrl;
        private $UserServiceUrl;
        private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->UserServiceUrl = env('USER_SERVICE');
        $this->apiKey = env('API_KEY');
    }    
    public function index(Request $request)
    {
         $response = $this->callService($request->all());
         return response()->json($response);
    }


    public function show($id){
        $http = new Client();
        $response = $http->get("$this->ServiceUrl/subjects/$id", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ], 
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    private function callService($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->ServiceUrl/subjects", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'grade' => $data['gid'],
                        'classType'=> $data['classType'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

 public function GradeWiseSubjects(Request $request)
{
    $http = new Client();

    try {
        $response = $http->get("$this->ServiceUrl/grade-wise-subject", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'json' => [
                'grade_id' => $request->grade_id,
            ]
        ]);

        // Check for a 200 status code
        if ($response->getStatusCode() === 200) {
            $data = json_decode((string) $response->getBody(), true);

            // Array to hold consolidated teacher data
            $consolidatedData = [];
            $allTeacherData = $this->allTeacher($request->bearerToken());
            foreach ($data['data'] as $subject) {
                // Fetch single teacher data for each tid
                $teacherData = $this->SingleTeacher($subject['tid'], $request->bearerToken());
                

                // Add subject and teacher data to consolidated array
                $consolidatedData[] = [
                    'subject' => $subject,
                    'teacher' => $teacherData,
                ];
            }
            return response()->json([
                    'status' => 200,
                    'message' => 'Grade wise subjects retrieved successfully',
                    'data' => [
                    'subjects' => $consolidatedData, // Consolidated data (subject + teacher)
                    'all_teachers' => $allTeacherData, // All teacher data (separate)
                ],
                ], 200);
        }

        // Handle other status codes if necessary
    } catch (\Exception $e) {
        // Handle exceptions
        return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage(),
                ], 400);
       
    }
}

private function SingleTeacher($teacherId, $access_token)
{
    $http = new Client();
    
    $response = $http->get("$this->UserServiceUrl/single-teacher", [
        'headers' => [
            'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
            'API-Key' => $this->apiKey,
        ],
        'json' => [
            'teacher_id' => $teacherId, // Pass the teacher ID here
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
}




private function allTeacher($access_token)
{
    $http = new Client();
    
    $response = $http->get("$this->UserServiceUrl/all-teacher", [
        'headers' => [
            'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
            'API-Key' => $this->apiKey,
        ],
        
    ]);
    Log::info($response->getBody());
    return json_decode((string) $response->getBody(), true);
}


public function create(Request $request)
{
    Log::info($request->all());
    $http = new Client();

    try {
        $response = $http->post("$this->ServiceUrl/add-subjects", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'json' => [    
                'gid' => $request->gid,
                'tid' => $request->tid,
                'sname' => $request->sname,
                'fee' => $request->fee,
                'whats_app' => $request->whats_app,
                'retention' => $request->retention,
                'class_type' => $request->class_type,
                'day_normal' => $request->day_normal,
                'start_normal' => $request->start_normal,
                'end_normal' => $request->end_normal,
                'day_extra1' => $request->day_extra1,
                'start_extra1' => $request->start_extra1,
                'end_extra1' => $request->end_extra1,
                'start_extra2' => $request->start_extra2,
                'end_extra2' => $request->end_extra2,
            ],
        ]);

        // Check if the response is successful (status code 200)
        if ($response->getStatusCode() === 200) {
            // Handle successful response
            $data = json_decode((string) $response->getBody(), true);
            $updateSubjects = $this->TeacherSubject($request->bearerToken(), $request->tid,$data['related_subject_ids']);
            return response()->json([
                'status' => $response->getStatusCode(),
                'message' => 'Subject added successfully',
                'data' => json_decode((string) $response->getBody(), true),
            ], $response->getStatusCode());

            

        } else {
            // Handle non-200 responses
            return response()->json([
                'status' => $response->getStatusCode(),
                'message' => 'Error in API response',
                'details' => json_decode((string) $response->getBody(), true),
            ], $response->getStatusCode());
        }

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle exceptions related to the request, like connectivity issues
        return response()->json([
            'status' => 400,
            'message' => 'Request error: ' . $e->getMessage(),
        ], 400);

    } catch (\Exception $e) {
        // Catch any other exceptions
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}

public function update(Request $request)
{
    
    $http = new Client();

    try {
        $response = $http->post("$this->ServiceUrl/update-subjects", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'json' => [ 
                'sid' => $request->sid,    
                'sname' => $request->sname,
                'fee' => $request->fee,
                'whats_app' => $request->whats_app,
                'retention' => $request->retention,
                'class_type' => $request->class_type,
                'day_normal' => $request->day_normal,
                'start_normal' => $request->start_normal,
                'end_normal' => $request->end_normal,
                'day_extra1' => $request->day_extra1,
                'start_extra1' => $request->start_extra1,
                'end_extra1' => $request->end_extra1,
                'start_extra2' => $request->start_extra2,
                'end_extra2' => $request->end_extra2,
            ],
        ]);

        // Check if the response is successful (status code 200)
        if ($response->getStatusCode() === 200) {
            // Handle successful response
            //Log::info(json_decode((string) $response->getBody(), true));
            $data = json_decode((string) $response->getBody(), true);
            $updateSubjects = $this->TeacherSubject($request->bearerToken(), $request->tid,$data['related_subject_ids']);
            return response()->json([
                'status' => $response->getStatusCode(),
                'message' => 'Subject updated successfully',
                'data' => json_decode((string) $response->getBody(), true),
            ], $response->getStatusCode());

            

        } else {
            // Handle non-200 responses
            return response()->json([
                'status' => $response->getStatusCode(),
                'message' => 'Error in API response',
                'details' => json_decode((string) $response->getBody(), true),
            ], $response->getStatusCode());
        }

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle exceptions related to the request, like connectivity issues
        return response()->json([
            'status' => 400,
            'message' => 'Request error: ' . $e->getMessage(),
        ], 400);

    } catch (\Exception $e) {
        // Catch any other exceptions
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}


public function destroy(Request $request){
    $http = new Client();
    try {
        $response = $http->post("$this->ServiceUrl/delete-subjects", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'json' => [
                'sid' => $request->sid,
            ],
        ]);
        // Check if the response is successful (status code 200)
        if ($response->getStatusCode() === 200) {
            // Handle successful response
            return response()->json([
                'status' => $response->getStatusCode(),
                'message' => 'Subject deleted successfully',
                'data' => json_decode((string) $response->getBody(), true),
            ], $response->getStatusCode());
        } else {
            // Handle non-200 responses
            return response()->json([
                'status' => $response->getStatusCode(),
                'message' => 'Error in API response',
                'details' => json_decode((string) $response->getBody(), true),
            ], $response->getStatusCode());
        }

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle exceptions related to the request, like connectivity issues
        return response()->json([
            'status' => 400,
            'message' => 'Request error: ' . $e->getMessage(),
        ], 400);

    } catch (\Exception $e) {
        // Catch any other exceptions
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}

private function TeacherSubject($access_token, $tid,$data)
{
    $http = new Client();
    
    $response = $http->post("$this->UserServiceUrl/teacher-related-subject", [
        'headers' => [
            'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
            'API-Key' => $this->apiKey,
        ],
        'json' => [ 
            'tid' => $tid,
            'related_subject_ids' => $data,
        ],
        
    ]);
    Log::info($response->getBody());
    return json_decode((string) $response->getBody(), true);
}

}
