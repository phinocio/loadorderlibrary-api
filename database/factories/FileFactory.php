<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
final class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $fileTypes = [
            'modlist.txt',
            'plugins.txt',
            'starfield.ini',
            'starfieldprefs.ini',
            'starfieldcustom.ini',
            'skyrim.ini',
            'skyrimprefs.ini',
            'skyrimcustom.ini',
            'loadorder.txt',
        ];

        $cleanName = $this->faker->randomElement($fileTypes);

        return [
            'name' => md5($this->faker->unique()->uuid()).'-'.$cleanName,
            'clean_name' => $cleanName,
            'size_in_bytes' => $this->faker->numberBetween(1000, 20000),
        ];
    }
}
