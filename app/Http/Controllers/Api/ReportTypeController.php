<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\ReportType;
use Illuminate\Http\JsonResponse;

class ReportTypeController extends Controller
{
    public function getReportTypes(): JsonResponse
    {
        $types = ReportType::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'code', 'name', 'consecutivo_prefix', 'icon', 'color', 'pdf_view', 'display_order', 'settings')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($types);
    }
}
