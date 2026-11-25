<?php

namespace Tests;

use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Factories\PdfRendererFactory;
use App\Domain\Pdf\Services\AbstractPdfService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class TmpPdfProbeTest extends TestCase
{
    private function pdf(string $filename): GeneratedPdf
    {
        View::addLocation('/tmp/claude-1000/-home-amarco-laravel-projects-nutipita-admin/8647713b-f04d-468c-8364-612258ce4bbb/scratchpad/views');
        $service = new class($this->app->make(PdfRendererFactory::class)) extends AbstractPdfService {
            public function make(string $filename): GeneratedPdf { return $this->render($filename, 'probe', ['label' => 'X']); }
        };
        return $service->make($filename);
    }

    public function test_probe(): void
    {
        Storage::fake('local');
        $inputs = ['INV-0001', 'INV-0001.pdf', 'INV.PDF', 'A/B Cafe_Week_3_Order_Summary', 'A\\B', '../../etc/passwd', '..', '.hidden', '', '   ', 'Café "Zoé": Week 3?', "line\nbreak\ttab", "bad\xB1utf8", str_repeat('x', 300), '///', 'Al zayt Restaurant_Week_12_Order_Summary'];
        foreach ($inputs as $in) {
            $label = str_pad(substr(json_encode($in, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE), 0, 40), 42);
            try {
                $d = $this->pdf($in)->download()->headers->get('Content-Disposition');
                $s = $this->pdf($in)->stream()->headers->get('Content-Disposition');
                Storage::fake('local');
                $this->pdf($in)->saveToLocal('invoices', true);
                $saved = Storage::disk('local')->allFiles();
                fwrite(STDERR, "\n$label download: " . substr($d, 0, 95) . "\n" . str_repeat(' ', 43) . 'saved as: ' . json_encode(array_map(fn($f) => substr($f, 0, 60) . (strlen($f) > 60 ? '…(' . strlen($f) . ' chars)' : ''), $saved)));
            } catch (\Throwable $e) {
                fwrite(STDERR, "\n$label EXCEPTION " . class_basename($e) . ': ' . substr($e->getMessage(), 0, 80));
            }
        }
        fwrite(STDERR, "\n");
        $this->assertTrue(true);
    }
}
