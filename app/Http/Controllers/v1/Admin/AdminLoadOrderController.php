<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Admin;

use App\Actions\v1\LoadOrder\DeleteLoadOrder;
use App\Http\Controllers\ApiController;
use App\Models\LoadOrder;
use Illuminate\Http\JsonResponse;

final class AdminLoadOrderController extends ApiController
{
    // No policy because this entire controller is protected by middleware

    public function destroy(string $slug, DeleteLoadOrder $deleteLoadOrder): JsonResponse
    {
        $loadOrder = LoadOrder::query()->where('slug', $slug)->firstOrFail();
        $deleteLoadOrder->execute($loadOrder);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
