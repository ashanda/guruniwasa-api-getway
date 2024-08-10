<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Traits\HandlesHTTPRequests;
use Illuminate\Http\Request;
use Exception;

class ReceiptCategoryController extends Controller
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

    public function index()
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/receipt-category", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Receipt Categories Listed.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->CoreServiceUrl/receipt-category", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Receipt Category API End Point Calling Success', 'data' => $response], 201);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/receipt-category/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Receipt Category API End Point Calling Success', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function edit($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/receipt-category/$id/edit", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Receipt Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function update(Request $request, $id)
    {
        
        try {
            $response = $this->sendHttpRequest('PUT', "$this->CoreServiceUrl/receipt-category/$id", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Receipt Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->sendHttpRequest('DELETE', "$this->CoreServiceUrl/receipt-category/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Receipt Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function restore($id)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->CoreServiceUrl/receipt-category/$id/restore", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Receipt Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }
}
