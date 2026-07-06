<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\MoonShine\Resources\Transaction\Pages\TransactionIndexPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionFormPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionDetailPage;

use MoonShine\Crud\Attributes\SaveHandler;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;  

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;
// use MoonShine\Laravel\Handlers\Handler;

use App\MoonShine\Handlers\CsvExportHandler;
use App\MoonShine\Handlers\XlsxExportHandler;

use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Transaction, TransactionIndexPage, TransactionFormPage, TransactionDetailPage>
 */
// #[DestroyHandler(MoonShineUserRoleHandlers::class, 'destroy')]
// #[MassDestroyHandler(MoonShineUserRoleHandlers::class, 'massDestroy')]
#[SaveHandler(TransactionHandlers::class, 'save')]
class TransactionResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

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

    protected function importFields(): iterable
    {
        return [
            Date::make('Дата транзакции', 'date')->withTime(),
            Textarea::make('Описание', 'description'),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            Date::make('Дата транзакции', 'date')->withTime(),
            Textarea::make('Описание', 'description'),
        ];
    }

    // protected function export(): ?Handler
    // {
    //     return ExportHandler::make(__('moonshine::ui.export'))
    //         ->disk('public')
    //         ->filename(sprintf('export_%s', date('Ymd-His')))
    //         ->dir('/exports');
    // }
    
    protected function handlers(): ListOf
    {
        return parent::handlers()->add(
            CsvExportHandler::make('Экпорт CSV'),
            XlsxExportHandler::make('Экпорт XLSX'),
            ImportHandler::make(__('moonshine::ui.import'))
        );
        // return parent::handlers()->add(
        //     ExportHandler::make('Экспорт CSV')
        //         ->csv()
        //         ->delimiter(',')
        //         ->disk('public')
        //         ->filename(sprintf('export_%s', date('Ymd-His')))
        //         ->dir('/exports'),
        //     ActionButton::make('Экспорт в XLSX', url: '')
        //         ->method('exportXlsx')           // вызовём свой метод
        //         ->icon('heroicons.outline.table-cells'),
        //     // ExportHandler::make('Экспорт в XLSX')
        //     //     ->disk('public')
        //     //     ->filename(sprintf('export_%s', date('Ymd-His')))
        //     //     ->dir('/exports'),
        //     ImportHandler::make(__('moonshine::ui.import'))
        // );
    }
}
