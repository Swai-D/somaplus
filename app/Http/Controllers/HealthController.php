<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Health check used by Railway (and uptime monitors). Verifies Laravel boots
 * and can reach the database.
 */
final class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'database' => 'unreachable',
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'database' => 'connected',
        ]);
    }
}
