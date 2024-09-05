<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
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

    public function index(Request $request)
    {
        try {
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/student-certificate", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                    'student_id' => $request->student_id,
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }


    public function studentCertificateUpload(Request $request){
            
            // First, upload the file using the uploadservice method
            $foldername = 'student_certificate'; // Example folder name where files will be stored
            $filePathResponse = $this->uploadservice($request, $foldername);
            
           
            // Check if the file upload was successful
            

            // Get the uploaded file path from the response
            $filePath = json_decode($filePathResponse->getContent(), true)['path'];
            
            try {
            // Send HTTP requests
                $response_serviceCall = $this->classCertificateUpload($request, $filePath);
                
                return response()->json($response_serviceCall, 200);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
    }

        private function classCertificateUpload($data, $filePath)
        {
            // Make a request to auth-service to authenticate and get token

            $http = new Client();
                $response = $http->post("$this->CoreServiceUrl/student-certificate-upload", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => [
                        'student_id' => $data['student_id'] ,
                        'directory' => $filePath,
                    ],    
                ]);
                
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
            Log::info($foldername);
            // Use the trait method to upload the new file and delete the old one
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);
            
            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }
}
