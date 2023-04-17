<?php
namespace Svg4Mpdf\Modules;

use Svg4Mpdf\Utils\RegExpBuilder;

class Eraser
{
	public static function comments(&$contents)
	{
		$pattern = '\<\!.+\-\-\>';
		RegExpBuilder::replace($contents, $pattern);
	}
}