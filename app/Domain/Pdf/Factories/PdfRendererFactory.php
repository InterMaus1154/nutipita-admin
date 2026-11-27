<?php

namespace App\Domain\Pdf\Factories;

use Barryvdh\DomPDF\PDF;
use Illuminate\Contracts\Container\Container;
class PdfRendererFactory
{
    public function __construct(private Container $container)
    {
    }

    public function pdfEngine(): PDF
    {
        return $this->container->make('dompdf.wrapper');
    }
}
