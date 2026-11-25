<?php

namespace App\Domain\Pdf\Entities;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;

class GeneratedPdf
{
    public function __construct(private PDF $loadedPdf, private string $filename)
    {
    }

    public function download(): Response
    {
        $filename = $this->getFilename();
        return $this->loadedPdf->download($filename);
    }

    public function stream(): Response
    {
        $filename = $this->getFilename();
        return $this->loadedPdf->stream($filename);
    }

    /**
     *
     * @param string $finalPath
     * @param bool $bindWithFilename - Whether to append the filename to the given path
     * @return void
     */
    public function saveToLocal(string $finalPath, bool $bindWithFilename = false): void
    {
        $filename = $this->getFilename();
        if ($bindWithFilename) {
            if (str_ends_with($finalPath, '/') || str_starts_with($filename, '/')) {
                $resolvedPath = sprintf('%s%s', $finalPath, $filename);
            } else {
                $resolvedPath = sprintf('%s/%s', $finalPath, $filename);
            }
        } else {
            $resolvedPath = $finalPath;
        }
        $this->loadedPdf->save($resolvedPath, 'local');
    }

    private function getFilename(): string
    {
        $finalFilename = '';
        if (str_ends_with($this->filename, '.pdf')) {
            $finalFilename = $this->filename;
        } else {
            $finalFilename = sprintf('%s.pdf', $this->filename);
        }
        if ($finalFilename === '.pdf') {
            return 'document.pdf';
        }
        return $finalFilename;
    }

}
