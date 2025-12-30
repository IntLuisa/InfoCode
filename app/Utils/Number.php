<?php

namespace App\Utils;

class Number
{
	const SINGLE = ["", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"],
		PLURAL =  ["", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"],
		HUNDREDS = [
			'male' => ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"],
			'female' => ["", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas"]
		],
		DOZENS = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"],
		TEN_TO_NINETEEN = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"],
		DECIMALS = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];

	public static function toExtension($amount = 0, bool $isCurrency = true, bool $isFemale = false): string
	{
		$single = self::SINGLE;
		$plural = self::PLURAL;
		$decimals = self::DECIMALS;

		if ($isCurrency) {
			$single[0] = "centavo";
			$single[1] = "real";
			$plural[0] = "centavos";
			$plural[1] = "reais";
		}

		if ($isFemale) {
			if ($amount == 1)
				$decimals[1] = "uma";

			$decimals[2] = "duas";
		};

		$counterZerosGroups = 0;
		$amount = abs(number_format($amount, 2, ".", "")) . "";
		$integers = explode(".", $amount);

		for ($i = 0; $i < count($integers); $i++) {
			for ($ii = mb_strlen($integers[$i]); $ii < 3; $ii++)
				$integers[$i] = "0" . $integers[$i];
		};

		$finalName = false;
		$end = count($integers) - ($integers[count($integers) - 1] > 0 ? 1 : 2);

		for ($i = 0; $i < count($integers); $i++) {
			$amount = $integers[$i];
			$hundredName = (($amount > 100) && ($amount < 200)) ? "cento" : self::HUNDREDS[$isFemale ? 'female' : 'male'][$amount[0]];
			$dozensName = ($amount[1] < 2) ? "" : self::DOZENS[$amount[1]];
			$tenToNineteenName = ($amount > 0) ? ($amount[1] == 1 ? self::TEN_TO_NINETEEN[$amount[2]] : $decimals[$amount[2]]) : "";
			$name = $hundredName . (($hundredName && ($dozensName || $tenToNineteenName)) ? " e " : "") . $dozensName . (($dozensName && $tenToNineteenName) ? " e " : "") . $tenToNineteenName;
			$pos = count($integers) - 1 - $i;
			$name .= $name ? " " . ($amount > 1 ? $plural[$pos] : $single[$pos]) : "";
			if ($amount == "000")
				$counterZerosGroups++;
			elseif ($counterZerosGroups > 0)
				$counterZerosGroups--;

			if (($pos == 1) && ($counterZerosGroups > 0) && ($integers[0] > 0))
				$name .= (($counterZerosGroups > 1) ? " de " : "") . $plural[$pos];

			if ($name)
				$finalName = $finalName . ((($i > 0) && ($i <= $end) && ($integers[0] > 0) && ($counterZerosGroups < 1)) ? (($i < $end) ? ", " : " e ") : " ") . $name;
		};

		$finalName = mb_substr($finalName, 1);
		return ($finalName ? trim($finalName) : "zero" . ($isCurrency ? " reais" : ""));
	}

	static function toString($amount): string
	{
		return number_format($amount, 2, ',', '.');
	}

	static function toCurrency($amount, bool $withCurrency = true): string
	{
		return ($withCurrency ? 'R$&nbsp;' : '') . number_format($amount, 2, ',', '.');
	}

	static function pad(string $string, int $length, string $caracter = "0", $position = STR_PAD_LEFT): string
	{
		return str_pad($string, $length, $caracter, $position);
	}

	static function padLeft(string $string, int $length, string $caracter = "0"): string
	{
		return self::pad($string, $length, $caracter, STR_PAD_LEFT);
	}

	static function padRight(string $string, int $length, string $caracter = "0"): string
	{
		return self::pad($string, $length, $caracter, STR_PAD_RIGHT);
	}

	static function toFloat(string $string): float
	{
		return floatval(str_replace(",", ".", str_replace(".", "", str_replace("R$", "", $string))));
	}
}
