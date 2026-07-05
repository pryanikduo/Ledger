<?php

declare(strict_types=1);

namespace App\MoonShine\Handlers;

use MoonShine\UI\Exceptions\ActionButtonException;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;
use Symfony\Component\HttpFoundation\Response;

use MoonShine\ImportExport\ExportHandler;

use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\DateRange;

class CsvExportHandler extends ExportHandler
{
    /**
     * @throws ActionButtonException
     */
    public function __construct()
    {
        parent::__construct('Экспорт CSV');

        $this->csv()
             ->delimiter(',')
             ->disk('public')
             ->filename(sprintf('transactions_csv_%s', date('Ymd-His')))
             ->dir('/exports');
    }
    
    // public function handle(): BinaryFileResponse
    // {
    //     if (! $this->hasResource()) {
    //         throw ActionButtonException::resourceRequired();
    //     }

    //     self::process();

    //     return back();
    // }

    // public static function process()
    // {

    // }

    public function getButton(): ActionButtonContract
    {
        return ActionButton::make($this->getLabel(), $this->getUrl());
    }
}
