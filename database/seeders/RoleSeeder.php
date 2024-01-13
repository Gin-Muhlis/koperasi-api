<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['super-admin', 'member'];

        foreach($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role,
                'guard_name' => 'api'
            ]);
        }

        $user = User::where('email', 'fubuki@gmail.com')->first();

        if (isset($user)) {
            $user->assignRole('super-admin');
        }

    }
}
