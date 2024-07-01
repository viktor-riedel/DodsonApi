<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public const ROLES = [
        [
            'name' => 'ADMIN',
            'description' => 'Admin has access to all modules across entire system'
        ],
        [
            'name' => 'OPERATOR',
            'description' => 'Limited access to admin parts of the application',
        ],
        [
            'name' => 'CATALOG',
            'description' => 'Limited access to catalog parts of the application',
        ],
        [
            'name' => 'USER',
            'description' => 'Has user access to the application'
        ]
    ];

    public const PERMISSIONS = [
            // CARS
            [
                'name' => 'CARS ACCESS',
                'description' => 'Can see cars created in the admin panel',
            ],
            [
                'name' => 'CREATE CAR',
                'description' => 'Can create a new car',
            ],
            // IMPORT
            [
                'name' => 'IMPORT CARS FROM CAPARTS',
                'description' => 'Can import cars from Caparts',
            ],
            // PARTS
            [
                'name' => 'PARTS CARS',
                'description' => 'Can access created cars list',
            ],
            // CATALOG
            [
                'name' => 'CARS LIST',
                'description' => 'Can access cars list',
            ],
            [
                'name' => 'ADD NEW CAR',
                'description' => 'Can add new car to catalog',
            ],
            [
                'name' => 'DEFAULT PARTS',
                'description' => 'Can edit default parts list',
            ],
            // SETTINGS
            [
                'name' => 'USERS',
                'description' => 'Can see registered users',
            ],
            [
                'name' => 'CREATE ROLE',
                'description' => 'Can create a new role',
            ],
            [
                'name' => 'SET PERMISSIONS',
                'description' => 'Can change users permissions',
            ],
            [
                'name' => 'ACCESS SETTINGS',
                'description' => 'Can access settings',
            ],
            //

    ];

    public function run(): void
    {
        foreach(self::ROLES as $role) {
            $dbRole = Role::where('name', $role['name'])->first();
            if (!$dbRole) {
                Role::create([
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'guard_name' => 'api',
                ]);
            }
        }
        foreach(self::PERMISSIONS as $permission) {
            $dbPermission = Permission::where('name', $permission['name'])->first();
            if (!$dbPermission) {
                Permission::create([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'guard_name' => 'api',
                ]);
            }
        }

        $this->setAllPermissionsToAdmin();
        $this->setPermissionsToCatalog();
    }

    private function setAllPermissionsToAdmin(): void
    {
        $admin = Role::where('name', 'ADMIN')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
        }
    }

    private function setPermissionsToOperator(): void
    {
        $admin = Role::where('name', 'USER')->first();
        $permission = Permission::where('NAME', '');
    }

    private function setPermissionsToCatalog(): void
    {
        $catalog = Role::where('name', 'CATALOG')->first();
        $permissions = Permission::whereIn('NAME', [
            'CARS LIST', 'ADD NEW CAR', 'DEFAULT PARTS',
        ])->get();
        $catalog->syncPermissions($permissions);
    }

}
