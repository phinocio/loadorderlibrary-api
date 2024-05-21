<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\LoadOrderResource;
use App\Models\LoadOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;

class ComparisonController extends ApiController
{
    // Since we want to also have a user's private lists present in the comparison selector
    // We need a separate route to GET lists from than just /lists
    public function index()
    {
        $lists = LoadOrder::where('is_private', '=', 'false')->when(auth()->check(), function (Builder $query) {
            $query->orWhere('user_id', '=', auth()->user()->id);
        })->latest()->get();

        return LoadOrderResource::collection($lists);
    }

    public function show($loadOrder1, $loadOrder2)
    {
        $loadOrder1 = LoadOrder::where('slug', $loadOrder1)->with('files')->first();
        $loadOrder2 = LoadOrder::where('slug', $loadOrder2)->with('files')->first();

        if (! $loadOrder1 || ! $loadOrder2) {
            return response()->json(['message' => 'Load order not found.'], 404);
        }

        // Check for clean file names that exist in both lists
        $loadOrder1Files = $loadOrder1->files->pluck('clean_name')->toArray();
        $loadOrder2Files = $loadOrder2->files->pluck('clean_name')->toArray();
        $inBoth = array_intersect($loadOrder1Files, $loadOrder2Files);

        $compare = [];
        foreach ($inBoth as $file) {
            $file1 = $loadOrder1->files->where('clean_name', $file)->first();
            $file2 = $loadOrder2->files->where('clean_name', $file)->first();

            if ($file1->name === $file2->name) {
                continue;
            }

            // The files are not the same, so diff them.
            $builder = new StrictUnifiedDiffOutputBuilder([
                'collapseRanges' => true, // ranges of length one are rendered with the trailing `,1`
                'commonLineThreshold' => 6,    // number of same lines before ending a new hunk and creating a new one (if needed)
                'contextLines' => 3,    // like `diff:  -u, -U NUM, --unified[=NUM]`, for patch/git apply compatibility best to keep at least @ 3
                'fromFile' => $file1->clean_name,
                'fromFileDate' => null,
                'toFile' => $file2->clean_name,
                'toFileDate' => null,
            ]);

            $differ = new Differ($builder);
            $compare[] = $differ->diff(Storage::disk('uploads')->get($file1->name), Storage::disk('uploads')->get($file2->name));
        }

        return response()->json([
            'data' => [
                'list1' => new LoadOrderResource($loadOrder1),
                'list2' => new LoadOrderResource($loadOrder2),
                'diffs' => $compare,
            ],
        ]);
    }
}
