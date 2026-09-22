<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Catálogo de permisos base agrupados por módulo
        $permissionsByModule = [
            'Dashboard y Seguimiento' => [
                'dashboard.view' => 'Ver Dashboard Corporativo',
                'seguimiento.view' => 'Ver Seguimiento Global por Áreas',
            ],
            'KPIs de Áreas' => [
                'kpis.view' => 'Ver Indicadores y KPIs de Áreas',
                'kpis.manage' => 'Registrar y Gestionar Valores de KPIs',
            ],
            'Gestión de Acuerdos' => [
                'acuerdos.view' => 'Ver Listado de Acuerdos',
                'acuerdos.create' => 'Crear Nuevos Acuerdos',
                'acuerdos.edit' => 'Editar y Registrar Avances en Acuerdos',
                'acuerdos.delete' => 'Eliminar Acuerdos',
                'acuerdos.export' => 'Exportar Acuerdos a Excel',
                'acuerdos.historico.view' => 'Consultar Histórico de Acuerdos',
            ],
            'Planeador' => [
                'planeador.view' => 'Ver Calendario del Planeador',
                'planeador.manage' => 'Crear, Editar e Importar Eventos en Planeador',
            ],
            'Seguridad y Accesos' => [
                'users.view' => 'Ver Lista de Usuarios y Estado de Acceso',
                'users.manage' => 'Dar, Editar y Revocar Accesos a Usuarios de Auth Center',
                'roles.manage' => 'Crear y Gestionar Roles y Permisos del Sistema',
            ],
        ];

        // 1. Crear permisos si no existen
        $allPermissions = [];
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $name => $description) {
                $permission = Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web']
                );
                $allPermissions[$name] = $permission;
            }
        }

        // 2. Crear o asegurar los roles predeterminados
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $directorRole = Role::firstOrCreate(['name' => 'Director General', 'guard_name' => 'web']);
        $usuarioRole = Role::firstOrCreate(['name' => 'Usuario', 'guard_name' => 'web']);

        // 3. Asignar permisos al rol Administrador (todos los permisos)
        $adminRole->syncPermissions(array_keys($allPermissions));

        // 4. Asignar permisos al rol Director General (lectura global)
        $directorRole->syncPermissions([
            'dashboard.view',
            'seguimiento.view',
            'kpis.view',
            'acuerdos.view',
            'acuerdos.historico.view',
            'acuerdos.export',
            'planeador.view',
        ]);

        // 5. Asignar permisos al rol Usuario (operación estándar)
        $usuarioRole->syncPermissions([
            'acuerdos.view',
            'acuerdos.create',
            'acuerdos.edit',
            'kpis.view',
            'planeador.view',
        ]);

        // Limpiar caché nuevamente
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
