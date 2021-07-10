<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Resources\GameCollection;
use Illuminate\Http\Request;

class GameController extends Controller
{
	public function index()
	{
		return new GameCollection(Game::all());
	}
}
