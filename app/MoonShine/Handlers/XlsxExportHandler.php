<?php

declare(strict_types=1);

namespace App\MoonShine\Handlers;

use MoonShine\UI\Exceptions\ActionButtonException;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;
use Symfony\Component\HttpFoundation\Response;

class XlsxExportHandler extends ExportHandler
{
    /**
     * @throws ActionButtonException
     */
    public function __construct()
    {
        parent::__construct('Экспорт в XLSX');

        $this->disk('public')
             ->filename(sprintf('transactions_xlsx_%s', date('Ymd-His')))
             ->dir('/exports');
        // Без ->csv() — должен быть XLSX
    }
    // public function handle(): Response
    // {
    //     if (! $this->hasResource()) {
    //         throw ActionButtonException::resourceRequired();
    //     }

    //     if ($this->isQueue()) {
    //         // Job here

    //         toast(
    //             __('moonshine::ui.resource.queued')
    //         );

    //         return back();
    //     }

    //     self::process();

    //     return back();
    // }

    // public static function process()
    // {
    //     // Logic here
    // }

    public function getButton(): ActionButtonContract
    {
        return ActionButton::make($this->getLabel(), $this->getUrl());
    }
}
