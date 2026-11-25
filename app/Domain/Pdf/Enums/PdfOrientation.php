<?php

namespace App\Domain\Pdf\Enums;

enum PdfOrientation: string
{
    case LANDSCAPE = 'landscape';
    case PORTRAIT = 'portrait';
}
