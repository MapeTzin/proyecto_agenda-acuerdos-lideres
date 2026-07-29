<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'area' => 'ALMACEN',
                'name' => 'MARCO ANTONIO MERCADO',
                'email' => 'm.mercado@mapetzin.com',
                'password' => '1234',
                'must_change_password' => true
            ],
            [
                'area' => 'VENTAS',
                'name' => 'CESAR OSBALDO NOGUEIRA ESPINOZA',
                'email' => 'c.nogueira@mapetzin.com',
                'password' => '1234',
                'must_change_password' => false
            ],
            [
                'area' => 'COMERCIAL',
                'name' => 'MARLON LOPEZ ARELLANO',
                'email' => 'produccion@mapetzin.com',
                'password' => 'Mapetzin2022***',
                'must_change_password' => false
            ],
            [
                'area' => 'RECURSOS HUMANOS',
                'name' => 'Juan Carlos Alvarez López',
                'email' => 'j.lopez@mapetzin.com',
                'password' => '1234',
                'must_change_password' => true
            ],
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'name' => 'MARIA GABRIELA DOMINGUEZ LOZADA',
                'email' => 'asuntosregulatorios@mapetzin.com',
                'password' => '1234',
                'must_change_password' => true
            ],
            [
                'area' => 'CUENTAS POR COBRAR',
                'name' => 'MARIA GUADALUPE GOMEZ PEÑALOZA',
                'email' => 'm.gomez@mapetzin.com',
                'password' => '1234',
                'must_change_password' => true
            ],
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'name' => 'GEORGINA WENDTI GONZÁLEZ OROZCO',
                'email' => 'gerencia_serv_gobierno@lesli.com.mx',
                'password' => '87654321',
                'must_change_password' => false
            ],
            [
                'area' => 'ASIS ADM',
                'name' => 'GEMA IVON SERVIN CRUZ',
                'email' => 's.gema@mapetzin.com',
                'password' => 'mapetzin23',
                'must_change_password' => false
            ],
            [
                'area' => 'ADQUISICIONES',
                'name' => 'JOSE CARLOS REYES SOTO',
                'email' => 'j.reyes@mapetzin.com',
                'password' => '1234',
                'must_change_password' => true
            ],
            [
                'area' => 'SISTEMAS',
                'name' => 'VICTOR MANUEL AROCHI DIAZ',
                'email' => 'v.arochi@mapetzin.com',
                'password' => 'Esanoes120282',
                'must_change_password' => false
            ],
            [
                'area' => 'CONTABILIDAD',
                'name' => 'OMAR AGUILAR VAZQUEZ',
                'email' => 'o.aguilar@mapetzin.com',
                'password' => '1234',
                'must_change_password' => true
            ],
            [
                'area' => 'CULTURA ORGANIZACIONAL',
                'name' => 'DIRECCION GENERAL',
                'email' => 'direccion@mapetzin.com',
                'password' => 'Mapetzin2026',
                'must_change_password' => false
            ],
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'name' => 'Alejandro Mendoza Chavarría',
                'email' => 'a.mendoza@mapetzin.com',
                'password' => '123456',
                'must_change_password' => false
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'area' => $userData['area'],
                    'password' => Hash::make($userData['password']),
                    'must_change_password' => $userData['must_change_password']
                ]
            );
        }
    }
}
