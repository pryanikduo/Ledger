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
}
