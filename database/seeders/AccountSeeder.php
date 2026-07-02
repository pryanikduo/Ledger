<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::query()->delete();

        $accounts = [
            ['name' => 'Касса', 'code' => '1010', 'type' => 'asset', 'is_active' => true],
            ['name' => 'Расчётный счёт', 'code' => '1020', 'type' => 'asset', 'is_active' => true],
            ['name' => 'Уставный капитал', 'code' => '2010', 'type' => 'equity', 'is_active' => true],
            ['name' => 'Выручка', 'code' => '3010', 'type' => 'revenue', 'is_active' => true],
            ['name' => 'Расходы на материалы', 'code' => '4010', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Задолженность перед поставщиками', 'code' => '5010', 'type' => 'liability', 'is_active' => true],
        ];

        foreach ($accounts as $data) {
            Account::create($data);
        }   
    }
}
