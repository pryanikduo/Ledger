<?php 

namespace App\Services;

use App\Models\Transaction;
use App\Models\JournalEntry;
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
}
