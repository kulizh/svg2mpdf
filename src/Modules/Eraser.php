<?php
namespace Svg2Mpdf\Modules;

use Svg2Mpdf\Utils\RegExpBuilder;

class Eraser
{
	public static function comments(&$contents)
	{
		$pattern = '\<\!.+\-\-\>';
		RegExpBuilder::replace($contents, $pattern);
	}
}