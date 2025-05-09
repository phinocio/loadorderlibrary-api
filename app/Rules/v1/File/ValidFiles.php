<?php

declare(strict_types=1);

namespace App\Rules\v1\File;

final class ValidFiles
{
    /**
     * List of valid files.
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            'enblocal.ini',
            'enbseries.ini',
            'fallout.ini',
            'fallout4.ini',
            'fallout4custom.ini',
            'fallout4prefs.ini',
            'falloutcustom.ini',
            'falloutprefs.ini',
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
            'settings.ini',
            'settings.txt',
            'skyrim.ini',
            'skyrimcustom.ini',
            'skyrimprefs.ini',
            'skyrimvr.ini',
            'starfield.ini',
            'starfieldcustom.ini',
            'starfieldprefs.ini',
        ];
    }
}
