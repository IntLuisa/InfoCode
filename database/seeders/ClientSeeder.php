<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR'); // Localizado para o Brasil

        for ($i = 1; $i <= 50; $i++) {
            DB::table('clients')->insert([
                'name' => $faker->name,
                'document' => $faker->cnpj(false), // Gera CNPJ sem máscara
                'origin' => $faker->randomElement(['Site', 'Indicação', 'Instagram', 'Google']),
                'stateRegistration' => $faker->boolean(80) ? $faker->numerify('#########') : 'ISENTO',
                'codename' => 'CLI-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->cellphone(false),
                'address' => $faker->streetName,
                'number' => $faker->buildingNumber,
                'complement' => $faker->boolean(50) ? 'Sala ' . $faker->randomDigit : null,
                'district' => $faker->citySuffix,
                'state' => $faker->stateAbbr,
                'city' => $faker->city,
                'zip_code' => str_replace('-', '', $faker->postcode),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}