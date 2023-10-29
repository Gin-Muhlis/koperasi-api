<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view categories']);
        Permission::create(['name' => 'create categories']);
        Permission::create(['name' => 'update categories']);
        Permission::create(['name' => 'delete categories']);

        Permission::create(['name' => 'view installments']);
        Permission::create(['name' => 'create installments']);
        Permission::create(['name' => 'update installments']);
        Permission::create(['name' => 'delete installments']);

        Permission::create(['name' => 'view loans']);
        Permission::create(['name' => 'create loans']);
        Permission::create(['name' => 'update loans']);
        Permission::create(['name' => 'delete loans']);

        Permission::create(['name' => 'view members']);
        Permission::create(['name' => 'create members']);
        Permission::create(['name' => 'update members']);
        Permission::create(['name' => 'delete members']);

        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'update products']);
        Permission::create(['name' => 'delete products']);

        Permission::create(['name' => 'view purchases']);
        Permission::create(['name' => 'create purchases']);
        Permission::create(['name' => 'update purchases']);
        Permission::create(['name' => 'delete purchases']);

        Permission::create(['name' => 'view sales']);
        Permission::create(['name' => 'create sales']);
        Permission::create(['name' => 'update sales']);
        Permission::create(['name' => 'delete sales']);

         Permission::create(['name' => 'view salesdetails']);
        Permission::create(['name' => 'create salesdetails']);
        Permission::create(['name' => 'update salesdetails']);
        Permission::create(['name' => 'delete salesdetails']);

        Permission::create(['name' => 'view savings']);
        Permission::create(['name' => 'create savings']);
        Permission::create(['name' => 'update savings']);
        Permission::create(['name' => 'delete savings']);

        Permission::create(['name' => 'view stuffs']);
        Permission::create(['name' => 'create stuffs']);
        Permission::create(['name' => 'update stuffs']);
        Permission::create(['name' => 'delete stuffs']);

        Permission::create(['name' => 'view subcategories']);
        Permission::create(['name' => 'create subcategories']);
        Permission::create(['name' => 'update subcategories']);
        Permission::create(['name' => 'delete subcategories']);

        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'update roles']);
        Permission::create(['name' => 'delete roles']);

        Permission::create(['name' => 'view permissions']);
        Permission::create(['name' => 'create permissions']);
        Permission::create(['name' => 'update permissions']);
        Permission::create(['name' => 'delete permissions']);

        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        // Membuat role admin dan meng assign semua permission
        $allPermissions = Permission::all();
        $adminRole = Role::create(['name' => 'super-admin']);
        $adminRole->givePermissionTo($allPermissions);

        $user = User::whereEmail('fubuki17@gmail.com')->first();

        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
