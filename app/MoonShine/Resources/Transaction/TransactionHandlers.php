<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Services\LedgerService;
use Illuminate\Support\Facades\Log;

use Illuminate\Validation\ValidationException;

final readonly class TransactionHandlers
{
    public function save(?Transaction $model, array $data): Transaction
    {
        $entries = request()->input('journal_entries', []);
        $data['entries'] = array_values($entries);
        Log::info('Данные формы:', $data);
        
        $service = app(LedgerService::class);
        try {
            if(!$model->exists) {
                return $service->createTransaction($data);
            }
            return $service->updateTransaction($model, $data);
        } catch(\Exception $e) {
            throw ValidationException::withMessages([
                'entries' => $e->getMessage()
            ]);
        }
    }
}