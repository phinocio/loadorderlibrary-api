<?php

namespace App\Helpers;

use App\Models\LoadOrder;
use Illuminate\Support\Str;

class CreateSlug
{

	static function new($name): string
	{
		$slug = Str::slug($name, '-');

		$exists = Self::exists($slug);

		if (count($exists) > 0) {
			$slug = $slug . '-' . count($exists);
		}

		return $slug;
	}

	protected static function exists($slug)
	{
		return LoadOrder::where('slug', 'like', $slug . "%")->get();
	}
}
