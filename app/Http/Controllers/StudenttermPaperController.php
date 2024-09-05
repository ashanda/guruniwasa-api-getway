<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudenttermPaperController extends Controller
{
    //studentTermPaperUpload

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



    public function studentTermPaperUpload(Request $request){

            $filePath1 = null;
            $filePath2 = null;
            $filePath3 = null;
            // First, upload the file using the uploadservice method
            $foldername = 'student-term-test'; // Example folder name where files will be stored
            if($request->hasFile('first_document')){
                 $filePathResponse1 = $this->uploadservice1($request, $foldername);
                 $filePath1 = json_decode($filePathResponse1->getContent(), true)['path'];
            }

            if($request->hasFile('second_document')){
                $filePathResponse2 = $this->uploadservice2($request, $foldername);
                $filePath2 = json_decode($filePathResponse2->getContent(), true)['path'];
            }

            if($request->hasFile('third_document')){
                 $filePathResponse3 = $this->uploadservice3($request, $foldername);
                  $filePath3 = json_decode($filePathResponse3->getContent(), true)['path'];
            }
           
    
            
            try {
            // Send HTTP requests
                $response_serviceCall = $this->TermTestUpload($request, $filePath1, $filePath2, $filePath3);
                $response = [
                    'status' => 200,
                    'message' =>'Term test uploaded successfully', 
                    'data' => $response_serviceCall
                ];

                return response()->json($response, 200);
                return response()->json(['status' => 200, 'body' => $response_serviceCall]);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
    }

        private function TermTestUpload($data, $filePath1, $filePath2, $filePath3)
        {
            // Make a request to auth-service to authenticate and get token

            $http = new Client();
                $response = $http->post("$this->CoreServiceUrl/term-test-upload", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => [
                        'student_id' => $data['student_id'] ,
                        'teacher_id' => $data['teacher_id'],
                        'subject_id' => $data['subject_id'],
                        'grade_id' => $data['grade_id'],
                        'first_term' => $filePath1,
                        'first_marks' => $data['first_marks'],
                        'second_term' => $filePath2,
                        'second_marks' => $data['second_marks'],
                        'third_term' => $filePath3,
                        'third_marks' => $data['third_marks'], 
                    ],    
                ]);
                
            return json_decode((string) $response->getBody(), true);
        } 

       private function uploadservice1($data, $foldername)
        {
            
            // Use the Validator facade to perform validation
            $validator = Validator::make($data->all(), [
                'first_document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'first_document_old' => 'nullable|string',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Proceed with file handling if validation passes
            $file = $data->file('first_document');
            $oldFilePath = $data->input('old_file_path');

            // Use the trait method to upload the new file and delete the old one
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);

            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }
        private function uploadservice2($data, $foldername)
        {
            
            // Use the Validator facade to perform validation
            $validator = Validator::make($data->all(), [
                'second_document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'second_document_old' => 'nullable|string',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Proceed with file handling if validation passes
            $file = $data->file('second_document');
            $oldFilePath = $data->input('old_file_path');

            // Use the trait method to upload the new file and delete the old one
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);

            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }
        private function uploadservice3($data, $foldername)
        {
            
            // Use the Validator facade to perform validation
            $validator = Validator::make($data->all(), [
                'third_document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'third_document_old' => 'nullable|string',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Proceed with file handling if validation passes
            $file = $data->file('third_document');
            $oldFilePath = $data->input('old_file_path');

            // Use the trait method to upload the new file and delete the old one
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);

            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }
}
