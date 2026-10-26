<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProfessionalArea;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'areas' => ProfessionalArea::options(),
            'privacy_policy_version' => config('lumia.privacy_policy_version'),
            'max_upload_kilobytes' => config('lumia.max_upload_kilobytes'),
            'status_poll_seconds' => config('lumia.status_poll_seconds'),
        ]);
    }
}
