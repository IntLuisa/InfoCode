<?php

namespace App\Utils;

class Crypt
{
	const CIPHER = '2a';
	const COST = 10;
	const SECRET_KEY = 'NOI8230&^*$dks';

	public static function encrypt($value, $separator = '_'): string
	{
		return $value . $separator . preg_replace('/^(.{8})(.{4})(.{4})(.{4})(.{12})$/', '$1-$2-$3-$4-$5', MD5(base64_encode(self::SECRET_KEY . $value)));
	}

	public static function decrypt(string $cipher, $separator = '_'): ?int
	{
		return self::valid($cipher, $separator) ? self::getValue($cipher, $separator) : null;
	}

	public static function valid(string $cipher, string $separator = '_'): bool
	{
		return self::encrypt(self::getValue($cipher, $separator), $separator) === $cipher;
	}

	public static function getValue(string $cipher, string $separator = '_'): ?string
	{
		return explode($separator, $cipher)[0];
	}
}
