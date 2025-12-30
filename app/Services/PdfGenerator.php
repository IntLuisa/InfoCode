<?php

namespace App\Services;

use Mpdf\Mpdf;

class PdfGenerator
{
	protected Mpdf $mpdf;

	public function __construct(array $config = [])
	{
		$defaultConfig = [
			'mode' => 'utf-8',
			'format' => 'A4',
			'orientation' => 'P',
			'margin_top' => 10,
			'margin_bottom' => 5,
			'margin_right' => 20,
			'margin_left' => 25,
			'margin_footer' => 5,
			'tempDir' => storage_path('app/mpdf'),
		];

		$config = array_merge($defaultConfig, $config);
		$this->mpdf = new Mpdf($config);
	}

	public function addPage($config = [])
	{
		$this->mpdf->AddPage(
			'',
			'',
			'',
			'',
			'',
			$config['margin_left'] ?? '',
			$config['margin_right'] ?? '',
			$config['margin_top'] ?? '',
			$config['margin_bottom'] ?? '',
		);
	}

	public function setColumns($number = 1)
	{
		$this->mpdf->SetColumns($number, "", 0);
	}

	public function setHeader($html = '')
	{
		$this->mpdf->SetHTMLHeader($html);
	}

	public function loadHTML(string $html, ?bool $hasFooter = true, ?string $type = 'krenke'): self
	{
		if ($hasFooter) {
			$this->mpdf->SetHTMLFooter($type === 'krenke' ? '<hr><p style="text-align:center; text-weight: bold;">REVENDEDORA KRENKE PERNAMBUCO LTDA - (81) 2161-2388<p>'
				: '<hr><p style="text-align:center; text-weight: bold;">TOPOTEC - TOPOGRAFIA, TECNOLOGIA, ENGENHARIA E CONSTRUCAO LTDA - (81) 9981-5374<p>');
		}
		$this->mpdf->WriteHTML($html);
		return $this;
	}

	public function stream(string $filename = 'document')
	{
		$this->mpdf->Output("$filename.pdf", 'I');
	}
}
