<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\LoadOrder;

class ComparisonController extends Controller
{
    //
    public function show($loadOrder1, $loadOrder2)
    {
        $loadOrder1 = LoadOrder::where('slug', $loadOrder1)->with('files')->first();
        $loadOrder2 = LoadOrder::where('slug', $loadOrder2)->with('files')->first();
        return response()->json([
            'data' => [
                'load_order1' => $loadOrder1->files,
                'load_order2' => $loadOrder2->files
            ]
        ]);
    }
}
