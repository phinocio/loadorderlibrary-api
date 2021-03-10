<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoadOrder;

class LoadOrderController extends Controller
{

	public function index()
	{
		$lists = LoadOrder::all();
		return response()->json($lists, 200);
	}

	public function store(Request $request)
	{
		// validate
		request()->validate([
			'name' => 'required',
			'game_id' => 'required'
		]);

		// persist
		$list = LoadOrder::create(request(['user_id', 'game_id', 'slug', 'name', 'description', 'files', 'is_private']));

		// return
		return response()->json($list);
	}
}
