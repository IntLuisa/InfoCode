<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clients')->insert([
            [
                'name' => 'Cliente Teste 1',
                'document' => '12345678901234',
                'origin' => 'Site',
                'stateRegistration' => 'ISENTO',
                'codename' => 'CLI-001',
                'email' => 'cliente1@email.com',
                'phone' => '11999999999',
                'address' => 'Rua Exemplo',
                'number' => '100',
                'complement' => 'Sala 01',
                'district' => 'Centro',
                'state' => 'SP',
                'city' => 'São Paulo',
                'zip_code' => '01001000',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Cliente Teste 2',
                'document' => '98765432100011',
                'origin' => 'Indicação',
                'stateRegistration' => null,
                'codename' => 'CLI-002',
                'email' => 'cliente2@email.com',
                'phone' => '21988888888',
                'address' => 'Av Brasil',
                'number' => '2000',
                'complement' => null,
                'district' => 'Copacabana',
                'state' => 'RJ',
                'city' => 'Rio de Janeiro',
                'zip_code' => '22040001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
