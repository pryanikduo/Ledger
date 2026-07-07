<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class TransactionObserver
{
    public function updating(Transaction $transaction) {
        $original = $transaction->getOriginal();

        $oldIsPosted = $original['is_posted'] ?? null;

        if($oldIsPosted === true) {
            throw ValidationException::withMessages([
                'transactions' => ['Транзакция уже проведена. Проведённые транзакции невозможно изменить.'],
            ]);
        }
    }

    public function deleting(Transaction $transaction) {
        if ($transaction->is_posted === true) {
            throw ValidationException::withMessages ([
                'transactions' => ['Транзакция уже была проведена. Проведённые транзакции невозможно удалить.'],
            ]);
        }
    }
}
