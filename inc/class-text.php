<?php

/**
 * Text class.
 */
class Text {

	/**
	 * Normalize a text string.
	 * 
	 * @param	string	$text	Text string to normalize.
	 * @param	string	$type	Type of text to normalize. Defaults to 'text'.
	 * @return	string
	 */
	public static function normalize_text(string $text, string $type = 'text'): string {
		if (empty($text) || !is_string($text)) {
			return $text;
		}

		// If text is a URL, replace spaces with dashes – otherwise, do the opposite.
		return $type === 'url'
			? self::replace_spaces(
				self::replace_accented_characters(
					strtolower($text)
				)
			)
			: self::replace_dashes(
				self::replace_accented_characters(
					strtolower($text)
				)
			);
	}

	/**
	 * Replace accented characters in a text string.
	 * 
	 * @param	string	$text	Text string on which to execute replacement.
	 * @return	string
	 */
	public static function replace_accented_characters(string $text): string {
		return str_ireplace(
			[
				'à', 'á', 'â', 'ä', 'æ', 'ã', 'å', 'ā', 'À', 'Á', 'Â', 'Ä', 'Æ', 'Ã', 'Å', 'Ā', 'ç', 'ć', 'č', 'Ç', 'Ć', 'Č', 'è', 'é', 'ê', 'ë', 'ē', 'ė', 'ę', 'È', 'É', 'Ê', 'Ë', 'Ē', 'Ė', 'Ę', 'î', 'ï', 'í', 'ī', 'į', 'ì', 'Î', 'Ï', 'Í', 'Ī', 'Į', 'Ì', 'ł', 'Ł', 'ñ', 'ń', 'Ñ', 'Ń', 'ô', 'ö', 'ò', 'ó', 'œ', 'ø', 'ō', 'õ', 'Ô', 'Ö', 'Ò', 'Ó', 'Œ', 'Ø', 'Ō', 'Õ', 'ß', 'ś', 'š', 'Ś', 'Š', 'û', 'ü', 'ù', 'ú', 'ū', 'Û', 'Ü', 'Ù', 'Ú', 'Ū', 'ÿ', 'Ÿ', 'ž', 'ź', 'ż', 'Ž', 'Ź', 'Ż',
			],
			[
				'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'c', 'c', 'c', 'c', 'c', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 's', 's', 's', 's', 's', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'z', 'z', 'z', 'z', 'z', 'z',
			],
			$text,
		);
	}

	/**
	 * Replace spaces with dashes in a text string.
	 * 
	 * @param	string	$text	Text string on which to execute replacement.
	 * @return	string
	 */
	public static function replace_spaces(string $text): string {
		return str_replace(' ', '-', $text);
	}
	
	 /**
	 * Replace dashes with spaces in a text string.
	 * 
	 * @param	string	$text	Text string on which to execute replacement.
	 * @return	string
	 */
	public static function replace_dashes(string $text): string {
		return str_replace('-', ' ', $text);
	}
}