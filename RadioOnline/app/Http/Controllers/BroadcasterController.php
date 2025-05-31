<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\BroadcasterRequest;
use App\Services\Interfaces\IcecastInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BroadcasterController extends Controller
{
    protected ?IcecastInterface $icecastService = null;

    public function __construct(IcecastInterface $icecastService)
    {
        $this->icecastService = $icecastService;
    }

    public function listener(BroadcasterRequest $request): JsonResponse
    {
        try {
            $listeners = $this->icecastService->getListeners($request->channel);
            return ApiResponse::success($listeners, "broadcaster::listeners");
        } catch (\Exception $e) {
            return ApiResponse::error("broadcaster::listeners {$e->getMessage()}");
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->icecastService->getStats();
            return ApiResponse::success($stats, "broadcaster::stats");
        } catch (\Exception $e) {
            return ApiResponse::error("broadcaster::stats {$e->getMessage()}");
        }
    }

}
