<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\LoadOrderResource;
use App\Models\LoadOrder;

class LoadOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Since this is the Admin route, we include private lists.
     */
    public function index()
    {
        return LoadOrderResource::collection(LoadOrder::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(LoadOrder $loadOrder)
    {
        return new LoadOrderResource($loadOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoadOrder $loadOrder)
    {
        $loadOrder->delete();
        return response()->json(null, 204);
    }
}
