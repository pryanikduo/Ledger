<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Transaction\TransactionResource;
use App\MoonShine\Resources\JournalEntry\JournalEntryResource;
use App\MoonShine\Resources\Account\AccountResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\Layout\Box;
use Throwable;


/**
 * @extends FormPage<TransactionResource>
 */
class TransactionFormPage extends FormPage
{
    protected string $title = 'Редактирование';
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(column: 'transaction_id'),
                Date::make('Дата создания', 'date'),
                Textarea::make('Описание', 'description'),
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
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
