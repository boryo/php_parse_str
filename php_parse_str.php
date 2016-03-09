<?php

/**
 * Class MyParseStr
 *
 *
 * @author boryo at https://github.com/boryo/php_parse_str
 */
class ParseStr
{
	/**
	 * Defines the max recursion depth while parsing the query string parameters
	 */
	const MAX_QUERY_DEPTH = 10;

	/**
	 * Do the same as parse_str without max_input_vars limitation
	 *
	 * @param string $string String to parse
	 *
	 * @param array $result Variables are stored in this variable as array elements
	 *
	 **/
	public static function parse($string, &$result)
	{
		if ($string === '') return;

		$vars = explode('&', urldecode($string));

		if (false === is_array($result))
		{
			$result = [];
		}

		foreach ($vars as $var)
		{
			if (false === ($eqPos = strpos($var, '='))) {
				continue;
			}

			$key = substr($var, 0, $eqPos);
			$value = substr($var, $eqPos+1);

			static::setQueryArrayValue($key, $result, $value);
		}
	}

	/**
	 * Sets array value by query string path
	 *
	 * Example: var[key][] is set to $array['var']['key'][]
	 *
	 * @param string $path The current path that is parsed
	 * @param array $array The array to save da data to
	 * @param string|int|bool $value The value to set in the array
	 * @param int $depth Internal parameter used to measure the depth of the recursion
	 */
	private static function setQueryArrayValue($path, &$array, $value, $depth = 0)
	{
		if ($depth > static::MAX_QUERY_DEPTH) return;

		if (false === ($arraySignPos = strpos($path, '[')))
		{
			$array[$path] = $value;
			return;
		}

		$key = substr($path, 0, $arraySignPos);

		$arrayESignPos = strpos($path, ']', $arraySignPos);

		if (false === $arrayESignPos) return;

		$subkey = substr($path, $arraySignPos + 1, $arrayESignPos - $arraySignPos - 1);

		if (empty($array[$key]) || !is_array($array[$key])) $array[$key] = [];

		if ($subkey != '')
		{
			$right = substr($path, $arrayESignPos + 1);

			if ('[' !== substr($right, 0, 1)) $right = '';

			static::setQueryArrayValue($subkey.$right, $array[$key], $value, $depth + 1);
			return;
		}

		$array[$key][] = $value;

	}
}
