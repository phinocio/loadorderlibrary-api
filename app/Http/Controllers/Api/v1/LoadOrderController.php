<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\v1\StoreLoadOrderRequest;
use App\Http\Requests\v1\UpdateLoadOrderRequest;
use App\Http\Resources\v1\LoadOrderResource;
use App\Models\LoadOrder;
use App\Policies\v1\LoadOrderPolicy;
use App\Services\LoadOrderService;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class LoadOrderController extends ApiController
{
    protected string $policyClass = LoadOrderPolicy::class;
    protected LoadOrderService $loadOrderService;

    public function __construct(LoadOrderService $loadOrderService)
    {
        parent::__construct();
        $this->loadOrderService = $loadOrderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @noinspection DuplicatedCode
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', LoadOrder::class);

        $lists = $this->loadOrderService->getLoadOrders(request());

        return LoadOrderResource::collection($lists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoadOrderRequest $request): LoadOrderResource|JsonResponse
    {
        Gate::authorize('create', LoadOrder::class);

        // TODO: Surely there's a better solution to allow guest uploads than this?
        // Return with a 401 (or maybe 422?) if there is no user associated with a token so a list is
        // not accidentally uploaded anonymously if the token was typo'd.
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
            if (! request()->user()->tokenCan('create')) {
                return response()->json(
                    ['message' => "This action is forbidden. (Token doesn't have permission for this action.)"],
                    403
                );
            }
        } elseif (request()->bearerToken() && ! Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated. (Make sure the token is correct.)'], 401);
        }

        $validated = $request->validated();
        $fileNames = UploadService::uploadFiles($validated['files']);

        $loadOrder = $this->loadOrderService->createLoadOrder($validated, $fileNames);

        return new LoadOrderResource($loadOrder);
    }

    /**
     * Display the specified resource.
     */
    public function show(LoadOrder $loadOrder)
    {
        Gate::authorize('view', $loadOrder);

        $loadOrder = $this->loadOrderService->getLoadOrder($loadOrder, request());

        return new LoadOrderResource($loadOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoadOrderRequest $request, LoadOrder $loadOrder): LoadOrderResource
    {
        Gate::authorize('update', $loadOrder);

        // Unlike the store() method, auth is done on the route level

        $validated = $request->validated();
        $fileNames = [];

        if (isset($validated['files'])) {
            $fileNames = UploadService::uploadFiles($validated['files']);
        }

        $loadOrder = $this->loadOrderService->updateLoadOrder($loadOrder, $validated, $fileNames, $request);

        return new LoadOrderResource($loadOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoadOrder $loadOrder)
    {
        Gate::authorize('delete', $loadOrder);

        try {
            $this->loadOrderService->deleteLoadOrder($loadOrder);

            return response()->json(null, 204);
        } catch (Throwable $th) {
            return response()->json([
                'message' => 'something went wrong deleting the load order. Please let Phinocio know.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
