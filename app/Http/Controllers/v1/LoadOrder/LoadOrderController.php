<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\LoadOrder;

use App\Actions\v1\LoadOrder\CreateLoadOrder;
use App\Actions\v1\LoadOrder\DeleteLoadOrder;
use App\Actions\v1\LoadOrder\GetLoadOrders;
use App\Actions\v1\LoadOrder\UpdateLoadOrder;
use App\Enums\v1\CacheKey;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\LoadOrder\StoreLoadOrderRequest;
use App\Http\Requests\v1\LoadOrder\UpdateLoadOrderRequest;
use App\Http\Resources\v1\LoadOrder\LoadOrderResource;
use App\Models\LoadOrder;
use App\Policies\v1\LoadOrderPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
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

    public function store(StoreLoadOrderRequest $request, CreateLoadOrder $createLoadOrder): LoadOrderResource|JsonResponse
    {
        // TODO: Surely there's a better solution to allow guest uploads than this?
        // Return with a 401 (or maybe 422?) if there is no user associated with a token so a list is
        // not accidentally uploaded anonymously if the token was typo'd.
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
            if (! $user->tokenCan('create')) {
                return response()->json(
                    ['message' => "This action is forbidden. (Token doesn't have permission for this action.)"],
                    403
                );
            }
        } elseif (request()->bearerToken() && ! Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated. (Make sure the token is correct.)'], 401);
        }

        Gate::authorize('create', LoadOrder::class);

        /**  @var array{
         *     name: string,
         *     description?: ?string,
         *     version?: ?string,
         *     website?: ?string,
         *     discord?: ?string,
         *     readme?: ?string,
         *     private?: bool,
         *     expires?: ?string,
         *     game: int,
         *     files: array<UploadedFile>,
         * } $data
         */
        $data = $request->validated();
        $loadOrder = $createLoadOrder->execute($data);

        return new LoadOrderResource($loadOrder);
    }

    public function show(string $slug): LoadOrderResource
    {
        $loadOrder = Cache::rememberForever(
            CacheKey::LOAD_ORDER->with($slug),
            fn () => LoadOrder::query()->where('slug', $slug)->with(['files'])->firstOrFail()
        );

        Gate::authorize('view', $loadOrder);

        return new LoadOrderResource($loadOrder);
    }

    public function update(UpdateLoadOrderRequest $request, LoadOrder $loadOrder, UpdateLoadOrder $updateLoadOrder): LoadOrderResource
    {
        Gate::authorize('update', $loadOrder);

        /** @var array{
         *     name?: string,
         *     description?: ?string,
         *     version?: ?string,
         *     website?: ?string,
         *     discord?: ?string,
         *     readme?: ?string,
         *     private?: bool,
         *     expires?: ?string,
         *     game?: int,
         *     files?: array<UploadedFile>
         * } $data
         */
        $data = $request->validated();
        $loadOrder = $updateLoadOrder->execute($loadOrder, $data);

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
