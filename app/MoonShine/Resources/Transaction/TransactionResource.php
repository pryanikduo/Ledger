<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\MoonShine\Resources\Transaction\Pages\TransactionIndexPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionFormPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionDetailPage;

use MoonShine\Crud\Attributes\DestroyHandler;
use MoonShine\Crud\Attributes\MassDestroyHandler;
use MoonShine\Crud\Attributes\SaveHandler;

use App\Services\LedgerService;

use Illuminate\Support\Facades\Log;

use Illuminate\Validation\ValidationException;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Transaction, TransactionIndexPage, TransactionFormPage, TransactionDetailPage>
 */
// #[DestroyHandler(MoonShineUserRoleHandlers::class, 'destroy')]
// #[MassDestroyHandler(MoonShineUserRoleHandlers::class, 'massDestroy')]
#[SaveHandler(TransactionHandlers::class, 'save')]
class TransactionResource extends ModelResource
{
    protected string $model = Transaction::class;

    protected string $title = 'Транзакции';

    protected string $column = 'date';

    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            TransactionIndexPage::class,
            TransactionFormPage::class,
            TransactionDetailPage::class,
        ];
    }
}

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