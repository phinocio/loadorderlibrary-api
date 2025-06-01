<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Admin;

use App\Actions\v1\LoadOrder\DeleteLoadOrder;
use App\Actions\v1\LoadOrder\GetLoadOrders;
use App\Enums\v1\CacheKey;
use App\Http\Resources\v1\LoadOrder\LoadOrderResource;
use App\Models\LoadOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

final class AdminLoadOrderController
{
    // No policy because this entire controller is protected by middleware

    public function index(Request $request, GetLoadOrders $getLoadOrders): AnonymousResourceCollection
    {

        $cacheKey = CacheKey::LOAD_ORDERS->value;
        if ($request->getQueryString()) {
            $cacheKey = CacheKey::LOAD_ORDERS->with(md5(mb_strtolower($request->getQueryString())), 'with-private');
        }

        $loadOrders = Cache::tags([CacheKey::LOAD_ORDERS->value])->rememberForever($cacheKey, fn () => $getLoadOrders->execute($request, true));

        return LoadOrderResource::collection(
            $loadOrders,
        );
    }

    public function destroy(string $slug, DeleteLoadOrder $deleteLoadOrder): JsonResponse
    {
        $loadOrder = LoadOrder::query()->where('slug', $slug)->firstOrFail();
        $deleteLoadOrder->execute($loadOrder);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
