<?php

namespace App\Services;

use Illuminate\View\Factory as ViewFactory;
use App\Utils\QRCode;

class PdfService
{
	public function __construct(private ViewFactory $view) {}

	public function generatePdf(string $file, array $data, array $config = [], string $header = '', bool $hasFooter = true, array $covers = [], $type = 'krenke')
	{
		if (count($covers)) {
			$pdf = new PdfGenerator([
				'margin_top' => 0,
				'margin_bottom' => -1,
				'margin_right' => 0,
				'margin_left' => 0,
			]);

			foreach ($covers as $cover) {
				$pdf->loadHTML('<img src="' . $cover . '" />', false);
			}

			$pdf->setHeader($header);
			$pdf->addPage($config);
		} else {
			$pdf = new PdfGenerator($config);
			$pdf->setHeader($header);
		}

		$html = $this->view->make("pdf.$file", $data)->render();
		$pdf->loadHTML($html, $hasFooter, $type);

		if (count($covers)) {
			$pdf->addPage([
				'margin_top' => 0,
				'margin_bottom' => -1,
				'margin_right' => 0,
				'margin_left' => 0,
			]);
			$pdf->loadHTML('<img src="assets/images/covers/end.png" />', false);
		}

		return $pdf;
	}

	public function getHtmlHeader(?string $type = 'krenke', ?string $id = ''): string
	{
		$data = match ($type) {
			'krenke' => (object) [
				'logo' => 'assets/images/template/logo.png',
				'title' => 'REVENDEDORA KRENKE PERNAMBUCO LTDA',
				'cnpj' => '54.294.136/0001-80'
			],
			'topotec' => (object) [
				'logo' => 'assets/images/template/Topotec.png',
				'title' => 'TOPOTEC - TOPOGRAFIA, TECNOLOGIA, ENGENHARIA E CONSTRUÇÃO LTDA',
				'cnpj' => '70.070.131/0001-20'
			],
			default => (object) [
				'logo' => 'assets/images/template/logo.png',
				'title' => 'REVENDEDORA KRENKE PERNAMBUCO LTDA',
				'cnpj' => '54.294.136/0001-80'
			],
		};

		return "<table width='100%' style='border:none; border-collapse: collapse'>
            <tr>
                <td style='width: 120px;border:none'>
                    <img src='{$data->logo}' style='height: 90px;margin-top: 0' />
                </td>
                <td valign='bottom' align='center' style='font-size: 9px;padding-bottom: 30px; border:none;'>
                    <p style='font-weight: bold; font-size: 12px'>{$data->title}</p>
                    <p style='font-weight: bold; font-size: 12px'>{$data->cnpj}</p>
                </td>
                <td align='right' style='width: 70px;border:none'>
                    <img style='width: 70px' src='data:image/png;base64," . QRCode::toUrl($id) . "'/>
                </td>
            </tr> 
        </table>
        <div style='text-align: center; position: relative; margin-top: 150px; opacity: 0.1;'>
            <img src='{$data->logo}' style='width: 600px; opacity: 0.1' />
        </div>";
	}

	public static function base64ToTempFile(?string $base64 = '', string $prefix = 'img_'): array
	{
		if (preg_match('/^data:(image\/\w+);base64,/', $base64, $type)) {
			$data = substr($base64, strpos($base64, ',') + 1);
			$ext = explode('/', $type[1])[1];
		} else {
			return [];
		}

		$temp = tempnam(sys_get_temp_dir(), $prefix);
		$tempWithExt = $temp . '.' . $ext;
		rename($temp, $tempWithExt);

		file_put_contents($tempWithExt, base64_decode($data));

		return [$tempWithExt];
	}
}
