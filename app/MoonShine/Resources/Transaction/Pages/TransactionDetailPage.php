<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Transaction\TransactionResource;
use App\MoonShine\Resources\JournalEntry\JournalEntryResource;
use App\MoonShine\Resources\Account\AccountResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use Throwable;


/**
 * @extends DetailPage<TransactionResource>
 */
class TransactionDetailPage extends DetailPage
{
    protected string $title = 'Подробный просмотр';
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(column: 'transaction_id'),
            Text::make('Дата проведения транзакции', 'date'),
            Textarea::make('Описание', 'description'),
            Text::make('Дата создания транзакции', 'created_at'),
            HasMany::make(
                'Проводки',
                'journal_entries', 
                resource: JournalEntryResource::class
            )->fields([
                BelongsTo::make(
                    'Счет', 
                    'account',
                    'name',
                    resource: AccountResource::class
                ),
                Text::make('Сумма', 'amount'),
                Select::make('Тип операции', 'type')
                    ->options([
                        'debit' => 'Дебет',
                        'credit' => 'Кредит'
                    ]),
            ])
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
