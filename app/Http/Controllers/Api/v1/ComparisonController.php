<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\LoadOrderResource;
use App\Models\LoadOrder;
use Illuminate\Support\Facades\Storage;

class ComparisonController extends Controller
{
    //
    public function show($loadOrder1, $loadOrder2)
    {
        $loadOrder1 = LoadOrder::where('slug', $loadOrder1)->with('files')->first();
        $loadOrder2 = LoadOrder::where('slug', $loadOrder2)->with('files')->first();

        if (!$loadOrder1 || !$loadOrder2) {
            return response()->json(['message' => 'Load order not found.'], 404);
        }

        // Check for clean file names that exist in both lists
        $loadOrder1Files = $loadOrder1->files->pluck('clean_name')->toArray();
        $loadOrder2Files = $loadOrder2->files->pluck('clean_name')->toArray();
        $inBoth = array_intersect($loadOrder1Files, $loadOrder2Files);

        // Compare the files
        $compare = [];
        foreach ($inBoth as $file) {
            $file1 = $loadOrder1->files->where('clean_name', $file)->first()?->name;
            $file2 = $loadOrder2->files->where('clean_name', $file)->first()?->name;

            if ($file1 === $file2) {
                continue;
            }

            // The files are not the same, so diff them.
            //            dd(shell_exec('diff -u' . Storage::disk('uploads')->path($file1) . ' ' . Storage::disk('uploads')->path($file2)));
            $diff = shell_exec("/usr/bin/diff -u --label $file --label $file " . Storage::disk('uploads')->path($file1) . ' ' . Storage::disk('uploads')->path($file2));
            array_push($compare, $diff);
        }
        //        dd($loadOrder1Files, $loadOrder2Files, $inBoth, $compare);


        return response()->json([
            'data' => [
                'list1' =>  new LoadOrderResource($loadOrder1),
                'list2' => new LoadOrderResource($loadOrder2),
                'diffs' => $compare
            ]
        ]);
    }
}
