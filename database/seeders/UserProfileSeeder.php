<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class UserProfileSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $users = User::doesntHave('profile')->get();

        // Create profiles for 60% of users randomly
        $usersToGetProfiles = $users->random((int) ($users->count() * 0.6));

        if ($usersToGetProfiles->isEmpty()) {
            return;
        }

        $profiles = [];
        $factory = UserProfile::factory();

        foreach ($usersToGetProfiles as $user) {
            $profileData = $factory->definition();
            $profileData['user_id'] = $user->id;
            $profileData['created_at'] = now();
            $profileData['updated_at'] = now();
            $profiles[] = $profileData;
        }

        DB::table('user_profiles')->insert($profiles);
    }
}
