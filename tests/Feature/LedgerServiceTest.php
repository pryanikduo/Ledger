<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Services\LedgerService;

class LedgerServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_creates_transaction_with_valid_entries(): void {
        $accountsId1 = Account::create([
            'name' => 'Unit-Test доход', 
            'code' => '1234', 
            'type' => 'revenue', 
            'is_active' => true,
        ])->account_id;

        $accountsId2 = Account::create([
            'name' => 'Unit-Test расход', 'code' => '4321', 'type' => 'expense', 'is_active' => true,
        ])->account_id;
            
        $transactionData = [
            'date' => now()->subDays(3),
            'description' => 'Валидный Unit-тест',
            'entries' => [
                ['account_id' => $accountsId2, 'amount' => 5000, 'type' => 'debit'],
                ['account_id' => $accountsId1, 'amount' => 5000, 'type' => 'credit']
            ],
        ];
        
        $transaction = app(LedgerService::class)->createTransaction($transactionData);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->transaction_id,
            'description' => 'Валидный Unit-тест'
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->transaction_id,
            'type'           => 'debit',
            'amount'         => 5000,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->transaction_id,
            'type'           => 'credit',
            'amount'         => 5000,
        ]);

        $this->assertEquals(2, $transaction->journal_entries()->count());
    }

    public function test_throws_exception_when_less_than_two_entries(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Должно быть минимум 2 проводки');

        $accountsId = Account::create([
            'name' => 'Unit-Test2 доход', 
            'code' => '2345', 
            'type' => 'revenue', 
            'is_active' => true,
        ])->account_id;
            
        $transactionData = [
            'date' => now()->subDays(3),
            'description' => 'Тест с одной проводкой',
            'entries' => [
                ['account_id' => $accountsId, 'amount' => 5000, 'type' => 'debit'],
            ],
        ];
        
        app(LedgerService::class)->createTransaction($transactionData);
    }

    public function test_throws_exception_when_debit_not_equal_credit(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Суммы не совпадают');

        $accountsId1 = Account::create([
            'name' => 'Unit-Test3 доход', 
            'code' => '3456', 
            'type' => 'revenue', 
            'is_active' => true,
        ])->account_id;

        $accountsId2 = Account::create([
            'name' => 'Unit-Test3 расход', 'code' => '6543', 'type' => 'expense', 'is_active' => true,
        ])->account_id;
            
        $transactionData = [
            'date' => now()->subDays(3),
            'description' => 'Несбалансированная транзакция',
            'entries' => [
                ['account_id' => $accountsId2, 'amount' => 5000, 'type' => 'debit'],
                ['account_id' => $accountsId1, 'amount' => 6000, 'type' => 'credit'],
            ],
        ];
        
        app(LedgerService::class)->createTransaction($transactionData);
    }
}
