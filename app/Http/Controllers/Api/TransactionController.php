<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\LedgerService;
use App\Http\Requests\StoreTransactionRequest;

class TransactionController extends Controller
{
    public function store(StoreTransactionRequest $request) {
        $validationData = $request->validated();
        try {
            $transaction = app(LedgerService::class)->createTransaction($validationData);

            $transaction->load('journal_entries.account');

            return response()->json([
                'transaction' => [
                    'success' => true,
                    'transaction_id' => $transaction->transaction_id,
                    'date' => $transaction->date,
                    'description' => $transaction->description,
                    'entries' => $transaction->journal_entries->map(function ($entry) {
                        return [
                            'account_id' => $entry->account_id,
                            'account_name' => $entry->account->name,
                            'account_code' => $entry->account->code,
                            'type' => $entry->type,
                            'amount' => $entry->amount,
                        ];
                    }),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
