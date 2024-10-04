<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
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


public function StudentSearch(Request $request)
{
    try {
        // First service call to get student data
        $response_serviceCall = $this->callServiceStudent($request->bearerToken());
        
        $studentsWithRelatedData = [];
        $status = $response_serviceCall['status'] ?? 400;
        // Validate if 'data' field exists and is an array
        if (isset($response_serviceCall['data']) && is_array($response_serviceCall['data'])) {
            foreach ($response_serviceCall['data'] as $student) {
                if (isset($student['id'])) {
                    $student_id = $student['id'];

                    // Make a second service call to get related data using the extracted ID
                    $relatedData = $this->callServiceRelatedData($student_id);
                    
                    // Combine student data with related data
                    $student['relatedData'] = $relatedData;

                    // Add to the results array
                    $studentsWithRelatedData[] = $student;
                }
            }
            
            return response()->json([
                'status' => $status,
                'students' => $studentsWithRelatedData,
            ], 200);
        } else {
            return response()->json(['error' => 'No students found in response'], 400);
        }
    } catch (Exception $exception) {
        // Log the exception message for debugging
        Log::error('Error in StudentSearch: ' . $exception->getMessage());
        return response()->json(['error' => $exception->getMessage()], 400);
    }
}

    private function callServiceStudent($access_token = null)
    {
        $http = new Client();
        $response = $http->get("$this->UserServiceUrl/student-search", [
            'headers' => [
                'API-Key' => $this->apiKey,
                'Authorization' => $access_token ? 'Bearer ' . $access_token : null, 
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
    private function callServiceAllTeacher($access_token = null)
    {
        $http = new Client();
        $response = $http->get("$this->UserServiceUrl/teacher-search", [
            'headers' => [
                'API-Key' => $this->apiKey,
                'Authorization' => $access_token ? 'Bearer ' . $access_token : null, 
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    private function callServiceRelatedData($grade_id)
    {
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/single-grade", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'json' => [
                'grade_id' => $grade_id,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
}



public function SingleStudent(Request $request)
{
        try {
        // First service call to get student data
        $response_serviceCall = $this->callServiceSingleStudent($request, $request->bearerToken());
        
        // Log the response for debugging
       

        // Extract the subjects data
        if (isset($response_serviceCall['data']['subjects'][0]['subject_ids'])) {
            // Decode the subject_ids JSON string
            $subject_ids = json_decode($response_serviceCall['data']['subjects'][0]['subject_ids'], true);
            
            // Initialize an array to store the subject details
            $subject_details = [];

            // Loop through each subject ID and get the subject details
            foreach ($subject_ids as $subject_id) {
                $subject_data = $this->callServiceSubjects($subject_id);
                
                // Call the teacher service for this subject
                $subject_teacher = $this->callServiceTeacher($subject_data['data']['tid'], $request->bearerToken());
                
                // Add the teacher information to the subject data
                $subject_data['teacher'] = $subject_teacher;

                // Collect the subject data
                $subject_details[] = $subject_data;
            }
            //subject related teacher
            
            // Call the related data service
            $relatedData = $this->callServiceRelatedData($response_serviceCall['data']['grade']);
            // Add the subject details to the response
            $response_serviceCall['data']['subject_details'] = $subject_details;
            $response_serviceCall['data']['relatedData'] = $relatedData;
        }
        
        return response()->json($response_serviceCall);
        
    } catch (Exception $exception) {
        // Log the exception message for debugging
        Log::error('Error in SingleStudent: ' . $exception->getMessage());
        return response()->json(['error' => $exception->getMessage()], 400);
    }
}

private function callServiceSingleStudent($data, $access_token = null)
{
    $http = new Client();
    $response = $http->get("$this->UserServiceUrl/single-student", [
        'headers' => [
            'API-Key' => $this->apiKey,
            'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
        ],
        'json' => [
            'student_id' => $data['student_id'],
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
}

private function callServiceTeacher($data, $access_token = null)
{
   
    $http = new Client();
    $response = $http->get("$this->UserServiceUrl/single-teacher", [
        'headers' => [
            'API-Key' => $this->apiKey,
            'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
        ],
        'json' => [
            'teacher_id' => $data,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
}

private function callServiceSubjects($subject_id)
{
    
    $http = new Client();
    $response = $http->get("$this->CoreServiceUrl/single-subject", [ // Adjust the endpoint if necessary
        'headers' => [
            'API-Key' => $this->apiKey,
        ],
        'json' => [
            'subject_id' => $subject_id,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
}


public function AllTeacher(Request $request)
{

    
      try {
        // First service call to get student data
        $response_serviceCall = $this->callServiceAllTeacher($request->bearerToken());
        
        $studentsWithRelatedData = [];
        $status = $response_serviceCall['status'] ?? 400;
        // Validate if 'data' field exists and is an array
        if (isset($response_serviceCall['data']) && is_array($response_serviceCall['data'])) {
            foreach ($response_serviceCall['data'] as $student) {
                if (isset($student['id'])) {
                    $student_id = $student['id'];
                    
                    
                    // Initialize an array to store the subject details
                    $subject_details = [];

                    // Check if 'subjects' array is not empty and has the required element
                    if (!empty($student['subjects']) && isset($student['subjects'][0]['subject_ids'])) {
                        $subject_ids = json_decode($student['subjects'][0]['subject_ids'], true);

                        // Loop through each subject ID and get the subject details
                        foreach ($subject_ids as $subject_id) {
                            $subject_data = $this->callServiceSubjects($subject_id);
                                                        
                            // Collect the subject data
                            $subject_details[] = $subject_data;
                        }
                    }

                    // Make a second service call to get related data using the extracted ID
                    $relatedData = $this->callServiceRelatedData($student_id);
                    $intro = $this->callServiceTeacherIntro($student_id);
                            
                            // Add the teacher information to the subject data
                    $student['intro'] = $intro;

                    $student['subject_details'] = $subject_details;
                    // Combine student data with related data
                    $student['relatedData'] = $relatedData;

                    // Add to the results array
                    $studentsWithRelatedData[] = $student;
                }
            }
            
            return response()->json([
                'status' => $status,
                'teachers' => $studentsWithRelatedData,
            ], 200);
        } else {
            return response()->json(['error' => 'No teacher found in response'], 400);
        }
    } catch (Exception $exception) {
        // Log the exception message for debugging
        //Log::error('Error in StudentSearch: ' . $exception->getMessage());
        return response()->json(['error' => $exception->getMessage()], 400);
    }
}

private function callServiceTeacherIntro($teacher_id){
    $http = new Client();
    $response = $http->get("$this->CoreServiceUrl/teacher-intro", [
        'headers' => [
            'API-Key' => $this->apiKey,
        ],
        'json' => [
            'teacher_id' => $teacher_id,
        ],
    ]);
    return json_decode((string) $response->getBody(), true);

}

public function AllStudent(Request $request)
{

    
      try {
        // First service call to get student data
        $response_serviceCall = $this->callServiceStudent($request->bearerToken());
        
        $studentsWithRelatedData = [];
        $status = $response_serviceCall['status'] ?? 400;
        // Validate if 'data' field exists and is an array
        if (isset($response_serviceCall['data']) && is_array($response_serviceCall['data'])) {
            foreach ($response_serviceCall['data'] as $student) {
                if (isset($student['id'])) {
                    $student_id = $student['id'];
                    Log::info($student);
                    
                    // Initialize an array to store the subject details
                    $subject_details = [];

                    // Check if 'subjects' array is not empty and has the required element
                    if (!empty($student['subjects']) && isset($student['subjects'][0]['subject_ids'])) {
                        $subject_ids = json_decode($student['subjects'][0]['subject_ids'], true);

                        // Loop through each subject ID and get the subject details
                        foreach ($subject_ids as $subject_id) {
                            $subject_data = $this->callServiceSubjects($subject_id);
                            
                            // Call the teacher service for this subject
                            $subject_teacher = $this->callServiceTeacher($subject_data['data']['tid'], $request->bearerToken());
                            
                            // Add the teacher information to the subject data
                            $subject_data['teacher'] = $subject_teacher;

                            // Collect the subject data
                            $subject_details[] = $subject_data;
                        }
                    }

                    // Make a second service call to get related data using the extracted ID
                    $relatedData = $this->callServiceRelatedData($student_id);
                    $student['subject_details'] = $subject_details;
                    // Combine student data with related data
                    $student['relatedData'] = $relatedData;

                    // Add to the results array
                    $studentsWithRelatedData[] = $student;
                }
            }
            
            return response()->json([
                'status' => $status,
                'students' => $studentsWithRelatedData,
            ], 200);
        } else {
            return response()->json(['error' => 'No students found in response'], 400);
        }
    } catch (Exception $exception) {
        // Log the exception message for debugging
        //Log::error('Error in StudentSearch: ' . $exception->getMessage());
        return response()->json(['error' => $exception->getMessage()], 400);
    }
}




public function SingleTeacher(Request $request)
{

    $request->validate([
        'teacher_id' => 'required',
    ]);

    try {
        $response_serviceCall = $this->callServiceSingleTeacher($request->teacher_id, $request->bearerToken());
        return response()->json($response_serviceCall, 200);
    } catch (Exception $exception) {
        return response()->json(['error' => $exception->getMessage()], 400);
    }

}

private function callServiceSingleTeacher($data, $access_token = null)
{
   
    $http = new Client();
    $response = $http->get("$this->UserServiceUrl/single-teacher", [
        'headers' => [
            'API-Key' => $this->apiKey,
            'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
        ],
        'json' => [
            'teacher_id' => $data,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);    

}

}
