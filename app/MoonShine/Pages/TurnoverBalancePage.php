<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Account;
use App\Services\LedgerService;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Text;
use MoonShine\Support\Enums\FormMethod;

class TurnoverBalancePage extends Page
{
    public function getTitle(): string
    {
        return $this->title ?: 'Оборотно-сальдовая ведомость';
    }

    public function getBreadcrumbs(): array
    {
        return ['#' => $this->getTitle()];
    }

    protected function components(): array
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $rows = app(LedgerService::class)->getTurnoverBalance($startDate, $endDate);

        $accounts = Account::whereIn('account_id', array_keys($rows))
            ->get()
            ->keyBy('account_id');

        $items = [];
        foreach ($rows as $accountId => $row) {
            $account = $accounts->get($accountId);

            $items[] = [
                'account' => $account
                    ? sprintf('%s (%s)', $account->name, $account->code)
                    : "Счёт #{$accountId}",
                'opening_balance' => number_format((float) $row['opening_balance'], 2, '.', ' '),
                'debit_turnover' => number_format((float) $row['debit_turnover'], 2, '.', ' '),
                'credit_turnover' => number_format((float) $row['credit_turnover'], 2, '.', ' '),
                'closing_balance' => number_format((float) $row['closing_balance'], 2, '.', ' '),
            ];
        }

        return [
            Box::make([
                FormBuilder::make(method: FormMethod::GET)
                    ->fields([
                        Date::make('Дата начала', 'start_date')->default($startDate),
                        Date::make('Дата конца', 'end_date')->default($endDate),
                    ])
                    ->submit('Показать'),
            ]),

            Box::make('Ведомость', [
                TableBuilder::make(
                    fields: [
                        Text::make('Счёт', 'account'),
                        Text::make('Сальдо на начало', 'opening_balance'),
                        Text::make('Оборот Дебет', 'debit_turnover'),
                        Text::make('Оборот Кредит', 'credit_turnover'),
                        Text::make('Сальдо на конец', 'closing_balance'),
                    ],
                    items: $items
                )->simple(),
            ]),
        ];
    }
}