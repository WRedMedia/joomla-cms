<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Filter
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Filter\OutputFilter;

/**
 * JFilterOutput
 *
 * @since  11.1
 */
class JFilterOutput extends OutputFilter
{
	/**
	 * This method processes a string and replaces all instances of & with &amp; in links only.
	 *
	 * @param   string  $input  String to process
	 *
	 * @return  string  Processed string
	 *
	 * @since   11.1
	 */
	public static function linkXHTMLSafe($input)
	{
		$regex = 'href="([^"]*(&(amp;){0})[^"]*)*?"';

		return preg_replace_callback("#$regex#i", array('JFilterOutput', '_ampReplaceCallback'), $input);
	}

	/**
	 * This method processes a string and escapes it for use in JavaScript
	 *
	 * @param   string  $string  String to process
	 *
	 * @return  string  Processed text
	 */
	public static function stringJSSafe($string)
	{
		for ($i = 0, $l = strlen($string), $new_str = ''; $i < $l; $i++)
		{
			$new_str .= (ord(substr($string, $i, 1)) < 16 ? '\\x0' : '\\x') . dechex(ord(substr($string, $i, 1)));
		}
		return $new_str;
	}

	/**
	 * This method processes a string and replaces all accented UTF-8 characters by unaccented
	 * ASCII-7 "equivalents", whitespaces are replaced by hyphens and the string is lowercase.
	 *
	 * @param   string  $string    String to process
	 * @param   string  $language  Language to transilterate to
	 *
	 * @return  string  Processed string
	 *
	 * @since   11.1
	 */
	public static function stringURLSafe($string, $language = '')
	{
		// Remove any '-' from the string since they will be used as concatenaters
		$str = str_replace('-', ' ', $string);

		// Transliterate on the language requested (fallback to current language if not specified)
		$lang = $language == '' || $language == '*' ? JFactory::getLanguage() : JLanguage::getInstance($language);

		//hard fix Armenian transliterate
		$replace = array(
		    "Ա"   => "A", "ա"    => "a",
		    "Բ"   => "B", "բ"    => "b",
		    "Գ"   => "G", "գ"    => "g",
		    "Դ"   => "D", "դ"    => "d",
		    "Ե"   => "E", "ե"    => "e",
		    "Զ"   => "Z", "զ"    => "z",
		    "Է"   => "E", "է"    => "e",
		    "Ը"   => "Y", "ը"    => "y",
		    "Թ"   => "T", "թ"    => "t",
		    "Ժ"   => "Zh", "ժ"   => "zh",
		    "Ի"   => "I", "ի"    => "i",
		    "Լ"   => "L", "լ"    => "l",
		    "Խ"   => "Kh", "խ"   => "kh",
		    "Ծ"   => "Ts", "ծ"   => "ts",
		    "Կ"   => "K", "կ"    => "k",
		    "Հ"   => "H", "հ"    => "h",
		    "Ձ"   => "Dz", "ձ"   => "dz",
		    "Ղ"   => "X", "ղ"    => "x",
		    "Ճ"   => "Ch", "ճ"   => "ch",
		    "Մ"   => "M", "մ"    => "m",
		    "Յ"   => "Y", "յ"    => "y",
		    "Ն"   => "N", "ն"    => "n",
		    "Շ"   => "Sh", "շ"   => "sh",
		    "Ո"   => "O", "ո"    => "o",
		    "Չ"   => "Ch", "չ"   => "ch",
		    "Պ"   => "P", "պ"    => "p",
		    "Ջ"   => "J", "ջ"    => "j",
		    "Ռ"   => "R", "ռ"    => "r",
		    "Ս"   => "S", "ս"    => "s",
		    "Վ"   => "V", "վ"    => "v",
		    "Տ"   => "T", "տ"    => "t",
		    "Ր"   => "R", "ր"    => "r",
		    "Ց"   => "Ts", "ց"   => "ts",
		    "ՈՒ" => "U", "ու"  => "u",
		    "Փ"   => "P", "փ"    => "p",
		    "Ք"   => "Q", "ք"    => "q",
		    "Օ"   => "O", "օ"    => "o",
		    "Ֆ"   => "F", "ֆ"    => "f",
		    "և"   => "ev",
		);
		$str = iconv("UTF-8", "UTF-8//IGNORE", strtr($str, $replace));
		$str = $lang->transliterate($str);

		// Trim white spaces at beginning and end of alias and make lowercase
		$str = trim(JString::strtolower($str));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

		// Trim dashes at beginning and end of alias
		$str = trim($str, '-');

		return $str;
	}

	/**
	 * Callback method for replacing & with &amp; in a string
	 *
	 * @param   string  $m  String to process
	 *
	 * @return  string  Replaced string
	 *
	 * @since   3.5
	 */
	public static function ampReplaceCallback($m)
	{
		$rx = '&(?!amp;)';

		return preg_replace('#' . $rx . '#', '&amp;', $m[0]);
	}

	/**
	 * Callback method for replacing & with &amp; in a string
	 *
	 * @param   string  $m  String to process
	 *
	 * @return  string  Replaced string
	 *
	 * @since       11.1
	 * @deprecated  4.0 Use JFilterOutput::ampReplaceCallback() instead
	 */
	public static function _ampReplaceCallback($m)
	{
		return static::ampReplaceCallback($m);
	}
}
