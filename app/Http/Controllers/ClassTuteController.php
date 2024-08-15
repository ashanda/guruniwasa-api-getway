<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassTuteController extends Controller
{
    use HandlesHTTPRequests;
    use S3UploadTrait;
    private $ServiceUrl;
    private $CoreServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
         $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }

    public function ClassTuteBooks(Request $request) {
       

        try {
    // Send HTTP requests
    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson", $request->bearerToken());
    $response_userGrade = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/grades/{$request->grade}", $request->bearerToken());
    $response_serviceCall = $this->callService($request->all());
    
    // Decode the subject_ids from userSubjects
    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

    if (isset($response_serviceCall['data']['class_tutes']) && is_array($response_serviceCall['data']['class_tutes'])) {
    // Filter the lessons based on subject_ids
    $filteredTute = array_filter($response_serviceCall['data']['class_tutes'], function ($lesson) use ($subject_ids) {
        // Ensure 'sid' key exists in each lesson
        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
    });
     $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Get the month name
    $monthName = isset($request->month) ? $months[$request->month] : null;
    } else {
    // Handle the case where 'lessons' key is not set or not an array
    return response()->json([
        'status' => 400,
        'message' => 'Class Tute is not available or not in the expected format',
        'data' => []
    ]);
}
    // Filter the lessons based on subject_ids
   

    // Prepare the response
    $response = [
        'status' => 200,
        'message' => 'Filtered Class Tute retrieved successfully',
        'data' => [
            'class_tutes' => array_values($filteredTute),
            'month' => $monthName,
            'grade' => $response_userGrade['data']['gname'],
            'sids' => $subject_ids,
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

     private function callService($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/class-tute-books", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'grade' => $data['grade'],
                        'month' => $data['month'],
                ],
        ]);
       
        $responseData = json_decode((string) $response->getBody(), true);
         // Check if the response data is valid
        if (isset($responseData['data']['class_tutes'])) {
        foreach ($responseData['data']['class_tutes'] as &$record) {
            $teacherId = $record['teacher_id'];
            $gradeIDd = $record['grade_id'];
            // Make a request to get the teacher's name using the teacher ID
            $responseTeacher = $this->sendHttpRequest('GET', "$this->ServiceUrl/teacher/data/$teacherId", $this->apiKey);
            $responseGrade = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/grades/$gradeIDd", $this->apiKey);
          
            // Assuming sendHttpRequest returns an array, no need to call getBody()
            // If it already returns decoded JSON data
            if (isset($responseTeacher['data']['name'])) {
                // Bind the teacher's name to the video record
                $record['teacher_name'] = $responseTeacher['data']['name'];
            } else {
                // If the name is not found, bind a default value
                $record['teacher_name'] = 'Unknown';
            }

            if (isset($responseGrade['data']['gname'])) {
                // Bind the teacher's name to the video record
                $record['grade'] = $responseGrade['data']['gname'];
            } else {
                // If the name is not found, bind a default value
                $record['grade'] = 'Unknown';
            }
        }
    }

    return $responseData;
    }


    public function classTuteTeacher(Request $request)
    {
        
        try {
    // Send HTTP requests
    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson-teacher", $request->bearerToken());
    $response_serviceCall = $this->callServiceclassTute($request->all());

    // Decode the subject_ids from userSubjects
    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

    if (isset($response_serviceCall['data']['class_tutes']) && is_array($response_serviceCall['data']['class_tutes'])) {
    // Filter the lessons based on subject_ids
    $filteredVideo = array_filter($response_serviceCall['data']['class_tutes'], function ($lesson) use ($subject_ids) {
        // Ensure 'sid' key exists in each lesson
        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
    });
     $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Get the month name
    $monthName = isset($request->month) ? $months[$request->month] : null;
    } else {
    // Handle the case where 'lessons' key is not set or not an array
    return response()->json([
        'status' => 400,
        'message' => 'Video Records is not available or not in the expected format',
        'data' => []
    ]);
}
    // Filter the lessons based on subject_ids
   

    // Prepare the response
    $response = [
        'status' => 200,
        'message' => 'Filtered Video Records retrieved successfully',
        'data' => [
            'class_tutes' => array_values($filteredVideo),
            'month' => $monthName,
            'subject' => $response_serviceCall,
            'sids' => $subject_ids,
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


    private function callServiceclassTute($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/class-tutes-teacher", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data['teacher_id'],
                        'month' => $data['month'],
                        'subject_id' => $data['subjects'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    


   public function classTuteTeacherStore(Request $request){
            Log::info($request->all());
            // First, upload the file using the uploadservice method
            $foldername = 'class_tutes'; // Example folder name where files will be stored
            $filePathResponse = $this->uploadservice($request, $foldername);
           
           
            // Check if the file upload was successful
            

            // Get the uploaded file path from the response
            $filePath = json_decode($filePathResponse->getContent(), true)['path'];
            
            try {
            // Send HTTP requests
                $response_serviceCall = $this->classTuteStore($request, $filePath);
                
                return response()->json($response_serviceCall, 200);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
   }


   private function classTuteStore($data, $filePath)
     {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
            $response = $http->post("$this->CoreServiceUrl/class-tutes-store", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'form_params' => [
                    'teacher_id' => isset($data['teacher_id']) ,
                    'subject_id' => isset($data['subject_id']),
                    'lesson_title' => $data['lesson_title'],
                    'tute_url' => $filePath,
                ],    
            ]);
            Log::debug($response->getBody());
        return json_decode((string) $response->getBody(), true);
    } 

    public function classTuteTeacherDestroy(Request $request){

        $http = new Client();
            $response = $http->delete("$this->CoreServiceUrl/class-tutes-destroy/{$request->id}", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                    
            ]);
            Log::debug($response->getBody());
        return json_decode((string) $response->getBody(), true);
        
    }





   private function uploadservice($data, $foldername)
{
    
    // Use the Validator facade to perform validation
    $validator = Validator::make($data->all(), [
        'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        'old_file_path' => 'nullable|string',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Proceed with file handling if validation passes
    $file = $data->file('document');
    $oldFilePath = $data->input('old_file_path');

    // Use the trait method to upload the new file and delete the old one
    $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);

    if ($filePath) {
        return response()->json(['path' => $filePath], 200);
    }

    return response()->json(['error' => 'File upload failed.'], 500);
}

}
