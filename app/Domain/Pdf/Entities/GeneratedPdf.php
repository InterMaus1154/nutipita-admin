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
        $name = preg_replace('/\.pdf$/i', '', trim($this->filename)) ?? '';
        $name = preg_replace('/[\x00-\x1F\x7F\/\\\\:*?"<>|]+/u', '-', $name) ?? '';
        $name = preg_replace('/\s+/u', '_', $name) ?? '';
        $name = preg_replace('/[-_]{2,}/', '_', $name) ?? '';
        $name = trim($name, ' .-_');
        $name = mb_substr($name, 0, 120);

        return ($name === '' ? 'document' : $name) . '.pdf';
    }

}
