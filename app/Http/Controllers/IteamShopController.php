<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IteamShopController extends Controller
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


        public function Index(Request $request) {
            try {
                // Initiate HTTP client  
                $http = new Client();
                // Make the API call to fetch teacher data
                $response = $http->get("$this->CoreServiceUrl/item_index", [
                    'headers' => [
                        'API-Key' => $this->apiKey,                
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

        public function store(Request $request)
        {
            try {
                $foldername = 'items-shop';
                
                // Call upload service to handle file upload
                $filePathResponse = $this->uploadservice($request, $foldername);
                
                $filePath = json_decode($filePathResponse->getContent(), true)['path'] ?? null;
                
                // Initiate HTTP client
                $http = new Client();

                // Make the API call to store data
                $response = $http->post("$this->CoreServiceUrl/item_store", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => array_merge($request->all(), ['item_image' => $filePath]),
                ]);

                // Decode the API response and return the data
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                // Return meaningful error message
                return [
                    'status' => 400,
                    'message' => $e->getMessage(),
                    'data' => [],
                ];
            }
        }

        public function update(Request $request)
        {
            try {
                $foldername = 'items-shop';
                $filePath = null;

                // Check if a file is being uploaded
                if ($request->hasFile('iteamImage')) {
                    // Handle file upload
                    $filePathResponse = $this->uploadservice($request, $foldername);
                    $filePath = json_decode($filePathResponse->getContent(), true)['path'] ?? null;
                }

                // Validate the input data
                $request->validate([
                    'item_id' => 'required|integer|exists:item_shops,id',
                    'item_name' => 'required|string|max:255',
                    'commission_account' => 'required|string|max:255',
                    'commission_rate' => 'required|numeric',
                    'commission_id' => 'required|integer',
                    'item_code' => 'required|string|max:100|unique:item_shops,item_code,' . $request->item_id,
                    'item_price' => 'required|numeric',
                    'item_description' => 'nullable|string',
                    'weight' => 'required|numeric',
                ]);

                // Prepare payload
                $payload = array_merge($request->all(), ['item_image' => $filePath]);

                // Make the API call to update the item
                $http = new Client();
                $response = $http->post("$this->CoreServiceUrl/item_update", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => $payload,
                ]);

                // Decode and return the response
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                // Return meaningful error message
                return [
                    'status' => 400,
                    'message' => $e->getMessage(),
                    'data' => [],
                ];
            }
        }

        public function destroy(Request $request)
        {
            try {
                // Validate the request data
                $request->validate([
                    'item_id' => 'required|integer|exists:item_shops,id',
                ]);

                // Make the API call to delete the item
                $http = new Client();
                $response = $http->post("$this->CoreServiceUrl/item_delete", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => ['item_id' => $request->item_id],
                ]);

                // Decode and return the response
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                // Return meaningful error message
                return [
                    'status' => 400,
                    'message' => $e->getMessage(),
                    'data' => [],
                ];
            }
        }


       private function uploadservice($data, $foldername)
        {
            // Validate the uploaded file
            $validator = Validator::make($data->all(), [
                'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'old_file_path' => 'nullable|string',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Proceed with file handling
            $file = $data->file('image');
            $oldFilePath = $data->input('old_file_path');

            // Upload to S3 (or other storage service)
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);

            // Return the file path on successful upload
            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }


        public function itemCategoriesIndex(Request $request) {

    try {
        // Initiate HTTP client  
        $http = new Client();
        // Make the API call to fetch teacher data
        $response = $http->post("$this->CoreServiceUrl/item_categories_index", [
            'headers' => [
                'API-Key' => $this->apiKey,                
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


    public function itemCategoriesStore(Request $request) {

    try {
        // Initiate HTTP client  
        $http = new Client();
        // Make the API call to fetch teacher data
        $response = $http->post("$this->CoreServiceUrl/item_categories_store", [
            'headers' => [
                'API-Key' => $this->apiKey,                
            ],
            'json' => [
                'category_name' => $request->category_name,
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

 public function itemCategoriesUpdate(Request $request) {

    try {
        // Initiate HTTP client  
        $http = new Client();
        // Make the API call to fetch teacher data
        $response = $http->post("$this->CoreServiceUrl/item_categories_update", [
            'headers' => [
                'API-Key' => $this->apiKey,                
            ],
            'json' => [
                'category_id' => $request->category_id,
                'category_name' => $request->category_name,
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


 public function itemCategoriesDelete(Request $request) {

    try {
        // Initiate HTTP client  
        $http = new Client();
        // Make the API call to fetch teacher data
        $response = $http->post("$this->CoreServiceUrl/item_categories_delete", [
            'headers' => [
                'API-Key' => $this->apiKey,                
            ],
            'json' => [
                'category_id' => $request->category_id,
                
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
