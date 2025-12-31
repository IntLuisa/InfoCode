<?php

namespace App\Utils;

use Mpdf\QrCode\QrCode as QR;
use Mpdf\QrCode\Output;

class QRCode
{
    static function render($string, $backgroundColor = [255, 255, 255])
    {
        return '<img src="data:image/png;base64,' . self::toUrl($string, $backgroundColor) . '"/>';
    }

    static function toUrl($string, $backgroundColor = [255, 255, 255])
    {
        if (empty($string)) {
            return ''; // ou algum QR padrão, ou um placeholder
        }

        $qrCode = new QR($string);
        $qrCode->disableBorder();
        $output = new Output\Png();
        return base64_encode($output->output($qrCode, 200, $backgroundColor, [0, 0, 0]));
    }

}
