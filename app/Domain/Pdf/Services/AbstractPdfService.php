<?php

namespace App\Domain\Pdf\Services;

use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Enums\PdfOrientation;
use App\Domain\Pdf\Exceptions\CannotGeneratePdfException;
use App\Domain\Pdf\Factories\PdfRendererFactory;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;

abstract class AbstractPdfService
{
    public function __construct(protected PdfRendererFactory $pdfRendererFactory, private Filesystem $filesystem)
    {
    }

    protected function render(string $filename, string $bladeView, array $data = [], string $size = 'a4', PdfOrientation $orientation = PdfOrientation::PORTRAIT): GeneratedPdf
    {
        try {
            $pdfEngine = $this->pdfRendererFactory->pdfEngine();
            $loaded = $pdfEngine->loadView($bladeView, $data)->setPaper($size, $orientation->value);
        } catch (\Throwable $e) {
            report($e);
            throw new CannotGeneratePdfException('Pdf generation failed', previous: $e);
        }

        return new GeneratedPdf($loaded, $filename, $this->filesystem);
    }
}
