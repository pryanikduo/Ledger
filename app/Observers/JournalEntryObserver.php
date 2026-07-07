<?php

namespace App\Observers;

use App\Models\JournalEntry;
use App\Models\Account;

use App\Services\LedgerService;

class JournalEntryObserver
{
    public function created(JournalEntry $entry): void {
        $this->recalculateBalance($entry->account_id);
    }

    public function updated(JournalEntry $entry): void {
        $original = $entry->getOriginal();

        $oldAccountId = $original['account_id'] ?? null;
        $newAccountId = $entry->account_id;
        $oldAmount = $original['amount'] ?? 0;
        $newAmount = $entry->amount;
        $oldType = $original['type'] ?? null;
        $newType = $entry->type;

        if($oldAccountId == $newAccountId 
            && $oldAmount == $newAmount
            && $oldType == $newType) {
            return;
        }

        $accountIds = array_unique(array_filter([$oldAccountId, $newAccountId]));

        foreach($accountIds as $accountId) {
            $this->recalculateBalance($accountId);
        }
        
    }

    public function deleted(JournalEntry $entry): void {
        $this->recalculateBalance($entry->account_id);
    }

    private function recalculateBalance(int $accountId): void {
        $totals = JournalEntry::query()
            ->where('account_id', $accountId)
            ->selectRaw('SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) as sum_debit,
                        SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) as sum_credit',
                        ['debit', 'credit']
                    )
            ->first();

        $service = app(LedgerService::class);

        $balanceDebit = (float) $totals->sum_debit;
        $balanceCredit = (float) $totals->sum_credit;

        $account = Account::find($accountId);
        if(!$account) {
            return;
        }

        $balance = $service->calculateBalance($balanceDebit, $balanceCredit, $account->type);

        $account->balance = $balance;
        $account->save();
        
    }
}
