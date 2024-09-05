<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    //
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

    public function studentBankPayment(Request $request){
            $foldername = 'bank_slips'; // Example folder name where files will be stored
            $filePathResponse = $this->uploadservice($request, $foldername);
            
           
            // Check if the file upload was successful
            

            // Get the uploaded file path from the response
            $filePath = json_decode($filePathResponse->getContent(), true)['path'];
            
            try {
            // Send HTTP requests
                $response_serviceCall = $this->bankSlipUpload($request, $filePath);
                
                return response()->json($response_serviceCall, 200);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
    }

        private function bankSlipUpload($data, $filePath)
        {
            // Make a request to auth-service to authenticate and get token
            $payment_id = uniqid('gnu_', true);
            $http = new Client();
                $response = $http->post("$this->PaymentServiceUrl/student-bank-payment", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => [
                        'student_id' => $data['student_id'] ,
                        'dateTime' => $data['dateTime'] ,
                        'cartData' =>  $data['cartData'] ,
                        'bank' => $data['bank'] ,
                        'transferSlip' =>  $filePath ,
                        'pay_month' => $data['pay_month'],
                        'payment_type' => 'Bank',
                        'payment_id'=>$payment_id,


                    ],    
                ]);
                
            return json_decode((string) $response->getBody(), true);
        } 

       private function uploadservice($data, $foldername)
        {
            
            // Use the Validator facade to perform validation
            $validator = Validator::make($data->all(), [
                'transferSlip' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'old_file_path' => 'nullable|string',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Proceed with file handling if validation passes
            $file = $data->file('transferSlip');
            
            $oldFilePath = $data->input('old_file_path');
            Log::info($foldername);
            // Use the trait method to upload the new file and delete the old one
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);
            
            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }

        public function StudentpendingPayment(Request $request){

            try {
                // Send HTTP requests
                $response_serviceCall = $this->callPendingBankService($request, $request->bearerToken());
                $data = collect($response_serviceCall['data']);
                $groupedPayments = $data->groupBy('payment_id');

                $groupedPayments->transform(function ($payments) {
                    // Assume student data is the same for each group, so fetch it only once
                    $student_id = $payments->first()['student_id'];
                    
                    $studentData = $this->getStudentData($student_id); // Call user-service to get student data
                    
                    return $payments->map(function ($payment) use ($studentData) {
                        // Fetch subject, grade, and teacher details for each payment
                        
                        $subjectData = $this->getSubjectData($payment['subject_id']);
                        
                        $gradeData = $this->getGradeData($payment['grade_id']);
                       
                        $teacherData = $this->getTeacherData($payment['teacher_id']); // Call user-service for teacher data
                       
                        
                        // Append additional data to each payment
                        return array_merge($payment, [
                            'student' => $studentData,
                            'subject' => $subjectData,
                            'grade' => $gradeData,
                            'teacher' => $teacherData
                        ]);
                    });
                });

                $response = [
                    'status' => 200,
                    'message' => 'Pending payment retrieved successfully',
                    'data' => $groupedPayments
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


        protected function getStudentData($studentId)
        {
            
            // Call to user-service to get student data by ID
            $http = new Client();
            $response = $http->get("$this->UserServiceUrl/single-student", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                        
                    ],
                    'query' => [    
                        'student_id' => $studentId,
                    ],
                    
            ]);
        
            return json_decode((string) $response->getBody(), true);
        }

        // Function to get subject data
        protected function getSubjectData($subjectId)
        {
            // Call to core-service to get subject data by ID
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/single-subject", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                        
                    ],
                    'query' => [    
                        'subject_id' => $subjectId,
                    ],
                    
            ]);
        
            return json_decode((string) $response->getBody(), true);
        }

        // Function to get grade data
        protected function getGradeData($gradeId)
        {
            // Call to core-service to get grade data by ID
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/single-grade", [
                    'headers' => [
                        'API-Key' => $this->apiKey,      
                    ],
                    'query' => [    
                        'grade_id' => $gradeId,
                    ],
                    
            ]);
        
            return json_decode((string) $response->getBody(), true);
        }

        // Function to get teacher data
        protected function getTeacherData($teacherId)
        {
            // Call to user-service to get teacher data by ID
            $http = new Client();
            $response = $http->get("$this->UserServiceUrl/single-teacher", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                         
                    ],
                    'query' => [    
                        'teacher_id' => $teacherId,
                    ],
                    
            ]);
        
            return json_decode((string) $response->getBody(), true);
        }

        private function callPendingBankService($data,$accessToken)
        {
            // Make a request to auth-service to authenticate and get token
        
            $http = new Client();
            $response = $http->get("$this->PaymentServiceUrl/student-pending-payment", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                        'Authorization' => $accessToken ? 'Bearer ' . $accessToken : null,  
                    ],
                    
            ]);
        
            return json_decode((string) $response->getBody(), true);
        }


       public function approvePayment(Request $request){
            try {
                
                $response_serviceCall = $this->callPaymentService($request, $request->bearerToken());
                return response()->json($response_serviceCall, 200);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
       }

       public function rejectPayment(Request $request){
            try {
                
                $response_serviceCall = $this->callPaymentService($request, $request->bearerToken());
                return response()->json($response_serviceCall, 200);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
       }
       private function callPaymentService($data,$accessToken)
        {
            // Make a request to auth-service to authenticate and get token
        
            $http = new Client();
            $response = $http->post("$this->PaymentServiceUrl/student-payment", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                        'Authorization' => $accessToken ? 'Bearer ' . $accessToken : null,  
                    ],
                    'json' => [
                        'staff_member' => $data['staff_member'] ,
                        'payment_id' => $data['payment_id'] ,
                        'status' => $data['status'] ,  

                    ],
                    
            ]);
            Log::info($response->getBody());
            return json_decode((string) $response->getBody(), true);
        }


        public function StudentPaymentHistory(Request $request){
                try {
                // Send HTTP requests
                $response_serviceCall = $this->callHistoryPayment($request, $request->bearerToken());
                $data = collect($response_serviceCall['data']);
                $groupedPayments = $data->groupBy('payment_id');

                $groupedPayments->transform(function ($payments) {
                    // Assume student data is the same for each group, so fetch it only once
                    $student_id = $payments->first()['student_id'];
                    
                    $studentData = $this->getStudentData($student_id); // Call user-service to get student data
                    
                    return $payments->map(function ($payment) use ($studentData) {
                        // Fetch subject, grade, and teacher details for each payment
                        
                        $subjectData = $this->getSubjectData($payment['subject_id']);
                        
                        $gradeData = $this->getGradeData($payment['grade_id']);
                       
                        $teacherData = $this->getTeacherData($payment['teacher_id']); // Call user-service for teacher data
                       
                        
                        // Append additional data to each payment
                        return array_merge($payment, [
                            'student' => $studentData,
                            'subject' => $subjectData,
                            'grade' => $gradeData,
                            'teacher' => $teacherData
                        ]);
                    });
                });

                $response = [
                    'status' => 200,
                    'message' => 'Pending payment retrieved successfully',
                    'data' => $groupedPayments
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
        
        private function callHistoryPayment($data,$accessToken)
        {
            // Make a request to auth-service to authenticate and get token
        
            $http = new Client();
            $response = $http->get("$this->PaymentServiceUrl/student-payment-history", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                        'Authorization' => $accessToken ? 'Bearer ' . $accessToken : null,  
                    ],
                    
            ]);
        
            return json_decode((string) $response->getBody(), true);
        }


       public function studentBankPaymentHistory(Request $request){

        try {
                // Send HTTP requests
                
                $response_serviceCall = $this->callAllPayment($request, $request->bearerToken());
                $data = collect($response_serviceCall['data']);
                $groupedPayments = $data->groupBy('payment_id');

                $groupedPayments->transform(function ($payments) {
                    // Assume student data is the same for each group, so fetch it only once
                    $student_id = $payments->first()['student_id'];
                    
                    $studentData = $this->getStudentData($student_id); // Call user-service to get student data
                    
                    return $payments->map(function ($payment) use ($studentData) {
                        // Fetch subject, grade, and teacher details for each payment
                        
                        $subjectData = $this->getSubjectData($payment['subject_id']);
                        
                        $gradeData = $this->getGradeData($payment['grade_id']);
                       
                        $teacherData = $this->getTeacherData($payment['teacher_id']); // Call user-service for teacher data
                       
                        
                        // Append additional data to each payment
                        return array_merge($payment, [
                            'student' => $studentData,
                            'subject' => $subjectData,
                            'grade' => $gradeData,
                            'teacher' => $teacherData
                        ]);
                    });
                });

                $response = [
                    'status' => 200,
                    'message' => 'Pending payment retrieved successfully',
                    'data' => $groupedPayments
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
        
        
        private function callAllPayment($data,$accessToken)
        {
            // Make a request to auth-service to authenticate and get token
            
            $http = new Client();
            $response = $http->get("$this->PaymentServiceUrl/student-payment-history/$data->payment_type", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                        'Authorization' => $accessToken ? 'Bearer ' . $accessToken : null,  
                    ],
                    'json' => [
                        'month' => $data['month'] ,
                        'payment_type' => $data['payment_type'] , 
                    ],
                    
            ]);
        Log::info($response->getBody());
            return json_decode((string) $response->getBody(), true);
        }

}
