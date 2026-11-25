<?php

namespace App\Domain\Pdf\Services;

use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Enums\PdfOrientation;
use App\Domain\Pdf\Factories\PdfRendererFactory;

abstract class AbstractPdfService
{
    public function __construct(protected PdfRendererFactory $pdfRendererFactory)
    {
    }

    protected function render(string $filename, string $bladeView, array $data = [], string $size = 'a4', PdfOrientation $orientation = PdfOrientation::PORTRAIT): GeneratedPdf
    {
        $pdfEngine = $this->pdfRendererFactory->pdfEngine();
        $loaded = $pdfEngine->loadView($bladeView, $data)->setPaper($size, $orientation->value);
        return new GeneratedPdf($loaded, $filename);
    }
}
