<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\LoadOrder;

use App\Actions\v1\LoadOrder\DeleteLoadOrder;
use App\Actions\v1\LoadOrder\GetLoadOrders;
use App\Enums\v1\CacheKey;
use App\Http\Controllers\ApiController;
use App\Http\Resources\v1\LoadOrder\LoadOrderResource;
use App\Models\LoadOrder;
use App\Policies\v1\LoadOrderPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

final class LoadOrderController extends ApiController
{
    protected string $policyClass = LoadOrderPolicy::class;

    public function index(Request $request, GetLoadOrders $getLoadOrders): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', LoadOrder::class);

        $cacheKey = CacheKey::LOAD_ORDERS->value;
        if ($request->getQueryString()) {
            $cacheKey = CacheKey::LOAD_ORDERS->with(md5(mb_strtolower($request->getQueryString())));
        }

        $loadOrders = Cache::tags([CacheKey::LOAD_ORDERS->value])->rememberForever($cacheKey, fn () => $getLoadOrders->execute($request));

        return LoadOrderResource::collection(
            $loadOrders,
        );
    }

    public function show(string $slug): LoadOrderResource
    {
        $loadOrder = Cache::rememberForever(
            CacheKey::LOAD_ORDER->with($slug),
            fn () => LoadOrder::query()->where('slug', $slug)->with(['game', 'author', 'files'])->firstOrFail()
        );

        Gate::authorize('view', $loadOrder);

        return new LoadOrderResource($loadOrder);
    }

    public function destroy(string $slug, DeleteLoadOrder $deleteLoadOrder): JsonResponse
    {
        $loadOrder = LoadOrder::query()->where('slug', $slug)->firstOrFail();

        Gate::authorize('delete', $loadOrder);

        $deleteLoadOrder->execute($loadOrder);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
