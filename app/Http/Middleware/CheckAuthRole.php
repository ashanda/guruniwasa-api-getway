<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckAuthRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        $serviceUrl = env('USER_SERVICE');
        $apiKey = env('API_KEY');

        $urls = [
            'teacher' => "$serviceUrl/teacher/check-auth",
            'staff' => "$serviceUrl/staff/check-auth",
            'admin' => "$serviceUrl/admin/check-auth",
            'super-admin' => "$serviceUrl/super-admin/check-auth",
            'student' => "$serviceUrl/student/check-auth",
        ];

        $authenticated = false;

        foreach ($roles as $role) {
            if (isset($urls[$role])) {
                try {
                    $client = new Client();
                    $response = $client->request('GET', $urls[$role], [
                        'headers' => [
                            'Authorization' => "Bearer " . $request->bearerToken(),
                            'API-Key' => $apiKey,
                        ]
                    ]);

                    $result = json_decode((string) $response->getBody(), true);

                    if ($result['status'] === 200 && $result['message'] === 'Authorized') {
                        $authenticated = true;
                        break;
                    }
                } catch (Exception $exception) {
                    Log::error("Authentication Error for role {$role}: ", ['exception' => $exception]);
                }
            }
        }

        if (!$authenticated) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
