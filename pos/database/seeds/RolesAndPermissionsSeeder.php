<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $role = Role::create(['name' => '1. Owner']);
        Permission::create(['name' => 'Manage store']);
        Permission::create(['name' => 'Manage user']);
        $role->givePermissionTo(['Manage store', 'Manage user']);
        
        $role = Role::create(['name' => '2. Admin']);
        Permission::create(['name' => 'Allow refund']);
        $role->givePermissionTo(['Allow refund']);
        
        $role = Role::create(['name' => '3. Cashier']);
        Permission::create(['name' => 'Show cashier']);
        $role->givePermissionTo(['Show cashier']);
        
        $role = Role::create(['name' => '4. Academy']);
        Permission::create(['name' => 'Show academy']);
        $role->givePermissionTo(['Show academy']);

        $role = Role::create(['name' => '5. Gate']);
        Permission::create(['name' => 'Show gate']);
        $role->givePermissionTo(['Show gate']);

        $role = Role::create(['name' => '6. Skate rental']);
        Permission::create(['name' => 'Show skate rental']);
        $role->givePermissionTo(['Show skate rental']);


        // assign super admin into itadmin user
        $user = User::findOrFail(1);
        $user->givePermissionTo(Permission::all());
    }
}
