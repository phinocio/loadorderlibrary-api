<?php

namespace App\Helpers;

use App\LoadOrder;
use Illuminate\Support\Str;

class ValidFiles
{

	static function all()
	{
		return [
			'enblocal.ini',
			'enbseries.ini',
			'fallout.ini',
			'falloutprefs.ini',
			'fallout4.ini',
			'fallout4custom.ini',
			'fallout4prefs.ini',
			'geckcustom.ini',
			'geckprefs.ini',
			'loadorder.txt',
			'mge.ini',
			'modlist.txt',
			'morrowind.ini',
			'mwse-version.ini',
			'oblivion.ini',
			'oblivionprefs.ini',
			'plugins.txt',
			'settings.txt',
			'skyrim.ini',
			'skyrimcustom.ini',
			'skyrimprefs.ini'
		];
	}
}
