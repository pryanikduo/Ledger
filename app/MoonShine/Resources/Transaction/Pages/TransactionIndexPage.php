<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use App\MoonShine\Resources\Transaction\TransactionResource;
use App\MoonShine\Resources\JournalEntry\JournalEntryResource;
use App\MoonShine\Resources\Account\AccountResource;
use MoonShine\Support\ListOf;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
// use MoonShine\Laravel\Fields\Relationships\RelationRepeater;

use App\Models\Account;
use Throwable;


/**
 * @extends IndexPage<TransactionResource>
 */
class TransactionIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(column: 'transaction_id'),
            Date::make('Дата транзакции', 'date')->withTime(),
            Textarea::make('Описание', 'description'),
            Switcher::make('Провдена', 'is_posted'),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            DateRange::make('Дата транзакции', 'date'),
            Select::make('Счёт', 'account_id')
                ->options(
                    Account::where('is_active', true)
                        ->pluck('name', 'account_id')
                        ->toArray()
                )
                ->nullable()
                ->onApply(function ($query, $value) {
                    if (blank($value)) {
                        return $query;
                    }

                    return $query->whereHas('journal_entries', function ($q) use ($value) {
                        $q->where('account_id', $value);
                    });
                }),
        ];
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
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
