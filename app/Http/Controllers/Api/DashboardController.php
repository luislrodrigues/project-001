<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\Company;
use App\Models\Equipment;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Obtener estadísticas del dashboard
     */
    public function index(): JsonResponse
    {
        $stats = [
            'companies' => Company::whereNull('deleted_at')->where('status', 'active')->count(),
            'equipment' => Equipment::whereNull('deleted_at')->where('status', 'active')->count(),
            'reports' => Report::whereNull('deleted_at')->count(),
            'reports_this_month' => Report::whereNull('deleted_at')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return ApiResponse::success($stats);
    }
}
