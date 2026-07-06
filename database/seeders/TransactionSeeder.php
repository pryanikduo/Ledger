<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\LedgerService;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = app(LedgerService::class);

        $transactionData = [
            [
                'date' => now()->subDays(5),
                'description' => 'Поступление выручки в кассу',
                'entries' => [
                    ['account_id' => 7, 'amount' => 10000, 'type' => 'debit'],
                    ['account_id' => 10, 'amount' => 10000, 'type' => 'credit']
                ],
            ],
            [
                'date' => now()->subDays(3),
                'description' => 'Оплата материалов поставщику',
                'entries' => [
                    ['account_id' => 11, 'amount' => 5000, 'type' => 'debit'],
                    ['account_id' => 8, 'amount' => 5000, 'type' => 'credit']
                ],
            ],
        ];

        foreach($transactionData as $data) {
            $service->createTransaction($data);
        }
    }
}
