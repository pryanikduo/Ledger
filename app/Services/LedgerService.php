<?php 

namespace App\Services;

use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

class LedgerService {
    public function createTransaction(array $data) {
        $result = DB::transaction(function () use ($data) {
            $sumDebit = 0;
            $sumCredit = 0;
            if (count($data['entries']) < 2) {
                throw new \Exception('Должно быть минимум 2 проводки');
            }
            if(!empty($data)) {
                foreach($data['entries'] as $i) {
                    if($i['type'] == 'debit'){
                        $sumDebit += $i['amount'];
                    }
                }
                foreach($data['entries'] as $i) {
                    if($i['type'] == 'credit'){
                        $sumCredit += $i['amount'];
                    }
                }
                if(abs($sumDebit - $sumCredit) > 0.0001) {
                    throw new \Exception('Суммы не совпадают');
                } else {
                    $transaction = new Transaction;
                    $transaction->date = $data['date'];
                    $transaction->description = $data['description'];
                    $transaction->save();
                    $transactionId = $transaction->transaction_id;
                    foreach($data['entries'] as $i) {
                        $entries = new JournalEntry;
                        $entries->transaction_id = $transactionId;
                        $entries->account_id = $i['account_id'];
                        $entries->amount = $i['amount'];
                        $entries->type = $i['type'];
                        $entries->save();
                    }   
                }
            }
            return $transaction;
        });
        return $result;
    }

    public function updateTransaction(Transaction $transaction, array $data) {
        return DB::transaction(function () use ($transaction, $data) {
            if (count($data['entries']) < 2) {
                throw new \Exception('Должно быть минимум 2 проводки');
            }
            $sumDebit = 0;
            $sumCredit = 0;
            if(!empty($transaction) && !empty($data)) {
                foreach($data['entries'] as $i){
                    if($i['type'] === 'debit') {
                        $sumDebit += $i['amount'];
                    } else {
                        $sumCredit += $i['amount'];
                    }
                }
                if(abs($sumDebit - $sumCredit) > 0.0001) {
                    throw new \Exception('Суммы не совпадают');
                } else {
                    unset($transaction->journal_entries);
                
                    $transaction->date = \Carbon\Carbon::parse($data['date'])->format('Y-m-d H:i:s');
                    $transaction->description = $data['description'];
                    $transaction->save();

                    $transaction->journal_entries()->delete();

                    foreach($data['entries'] as $entry) {
                        $transaction->journal_entries()->create([
                            'account_id' => $entry['account_id'],
                            'amount' => $entry['amount'],
                            'type' => $entry['type'],
                        ]);
                    }
                }
            }
            return $transaction;
        });
    }

    public function getTurnoverBalance(string $startDate, string $endDate): array {
        // Получаем сумму дебета и сумму кредита до начала периода
        $openingBalances = JournalEntry::query()
            ->join('transactions', 'transactions.transaction_id', '=', 'journal_entries.transaction_id')
            ->where('transactions.date', '<', $startDate)
            ->groupBy('account_id')
            ->selectRaw('journal_entries.account_id,
                        SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) as sum_debit,
                        SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) as sum_credit',
                        ['debit', 'credit']
                    )
            ->get()
            ->keyBy('account_id');

        // Получаем сумму дебета и сумму кредита в выбранном периоде
        $periodTurnovers = JournalEntry::query()
            ->join('transactions', 'transactions.transaction_id', '=', 'journal_entries.transaction_id')
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->groupBy('account_id')
            ->selectRaw('journal_entries.account_id,
                        SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) as sum_debit,
                        SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) as sum_credit',
                        ['debit', 'credit']
                    )
            ->get()
            ->keyBy('account_id');
        
        // Получаем все активные аккаунты
        $activeAccounts = Account::where('is_active', true)->get();

        $result = [];
        
        foreach($activeAccounts as $account) {
            // Проверяем, есть ли айди аккаунта в коллекции
            $openingID = $openingBalances->get($account->account_id); 
            $periodID = $periodTurnovers->get($account->account_id);

            $openingBalance = 0;
            $periodDebit = 0;
            $periodCredit = 0;
            $periodTurnover = 0;

            if($openingID !== null) {
                $openingBalance = $this->calculateBalance($openingID->sum_debit, $openingID->sum_credit, $account->type);
            } 
            if($periodID !== null) {
                $periodDebit = (float) $periodID->sum_debit;
                $periodCredit = (float) $periodID->sum_credit;
                $periodTurnover = $this->calculateBalance($periodDebit, $periodCredit, $account->type);
            } 

            $closingBalance = $periodTurnover + $openingBalance;

            $result[$account->account_id] = [
                'opening_balance' => $openingBalance,
                'debit_turnover' => $periodDebit,
                'credit_turnover' => $periodCredit,
                'closing_balance' => $closingBalance 
            ];
        }

        return $result;
    }
    // Приватный метод для вычисления нормального сальдо
    public function calculateBalance(float $sumDebit, float $sumCredit, string $accountType) {
        if($accountType == 'asset' || $accountType == 'expense') {
            return $sumDebit - $sumCredit;
        } else {
            return $sumCredit - $sumDebit;
        }
    }
}
