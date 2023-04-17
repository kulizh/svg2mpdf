<?php
namespace Svg2Mpdf\Utils;

/**
 * Класс-обработчик регулярных выражений
 *
 * Class RegExpBuilder
 * @package Svg\Service
 */
class RegExpBuilder
{
	/**
	 * Замена по регулярному выражению
	 *
	 * @param string &$subject Строка или массив для поиска и замены
	 * @param string $pattern Искомый шаблон
	 * @param string $replacement Строка или массив строк для замены
	 * @param string $pcre Модификаторы
	 * @param integer $limit Максимально возможное количество замен для строки (по умолчанию без ограничений)
	 * @param null|integer &$count Количество замен подстроки
	 *
	 * @return void
	 */
	public static function replace(&$subject, $pattern, $replacement = '', $pcre = '', $limit = -1, &$count = null)
	{
		self::composePattern($pattern, $pcre);

		$subject = preg_replace($pattern, $replacement, $subject, $limit, $count);
	}

	/**
	 * Выполняет глобальный поиск в строке по заданному шаблону
	 *
	 * @param string $subject Строка для поиска
	 * @param string $pattern Искомый шаблон
	 * @param string $pcre Модификаторы
	 * @param int $flag Флаг, определяющий порядок возвращаемых результатов
	 * @param int $offset Позиция начала поиска
	 * @return array
	 */
	public static function matchAll($subject, $pattern, $pcre = '', $flag = PREG_SET_ORDER, $offset = 0)
	{
		self::composePattern($pattern, $pcre);

		preg_match_all($pattern, $subject, $matches, $flag, $offset);

		return $matches;
	}

	/**
	 * Составляет шаблон регулярного выражения
	 *
	 * @param string &$pattern Переданный шаблон
	 * @param string $pcre Модификаторы
	 * @param boolean $quote Экранировать шаблон?
	 *
	 * @return void
	 */
	private static function composePattern(&$pattern, $pcre, $quote = false)
	{
		if ($quote)
		{
			$pattern = preg_quote($pattern);
		}

		if ($pattern[0] !== '/')
		{
			$pattern = '/' . $pattern;
		}

		if (substr($pattern, -1) !== '/')
		{
			$pattern = $pattern . '/';
		}

		$pattern .= $pcre;
	}
}