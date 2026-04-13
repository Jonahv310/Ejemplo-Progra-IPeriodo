<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos antes de iniciar  
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Generamos permisos
        $permissions = [
            'especialidades.ver', 'especialidades.crear', 'especialidades.editar', 'especialidades.eliminar',
            'doctores.ver', 'doctores.crear', 'doctores.editar', 'doctores.eliminar',
            'pacientes.ver', 'pacientes.crear', 'pacientes.editar', 'pacientes.eliminar',
            'citas.ver', 'citas.crear', 'citas.editar', 'citas.eliminar',
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'usuarios.gestionar_roles','usuarios.gestionar_permisos',
            'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',
            'roles.gestionar_permisos', 
            'permisos.ver',
        ];

        //Crear cada permiso si no existe
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        //Crear el rol administrador con todos los permisos
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);
    }
}
