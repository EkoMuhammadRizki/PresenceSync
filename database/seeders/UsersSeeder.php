<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $demoUser = User::updateOrCreate(
            ['email' => 'admin@sman1ciparay.com'],
            [
                'first_name'        => 'Admin',
                'last_name'         => 'SIAP',
                'password'          => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $this->addDummyInfo($faker, $demoUser);
        if (!$demoUser->hasRole('admin')) {
            $demoUser->assignRole('admin');
        }
    }

    private function addDummyInfo(Generator $faker, User $user)
    {
        $dummyInfo = [
            'company'  => $faker->company,
            'phone'    => $faker->phoneNumber,
            'website'  => $faker->url,
            'language' => $faker->languageCode,
            'country'  => $faker->countryCode,
        ];

        $info = new UserInfo();
        foreach ($dummyInfo as $key => $value) {
            $info->$key = $value;
        }
        $info->user()->associate($user);
        $info->save();
    }
}
