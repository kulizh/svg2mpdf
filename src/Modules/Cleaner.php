<?php
namespace Svg4Mpdf\Modules;

use Svg4Mpdf\Utils\RegExpBuilder;

class Cleaner
{
	private function findAndReplace($svg, $styles, $style_str)
	{
		foreach ($styles as $class => $style)
		{
			$search = ' class="' . $class . '"';
			$svg = str_replace($search, $style['style'], $svg);
		}

		RegExpBuilder::replace($svg, '(<!--(.+)-->)', '', 'm');
		RegExpBuilder::replace($svg, '(<g i:(.+)>)', '<g>', 'm');
		$svg = str_replace('	', '', $svg);
		RegExpBuilder::replace($svg, '<foreignObject(.+\n.+\n.+\n.+)', '', 'm');


		//Если отстутствует тег <?xml, то картинка не будет отображаться
		if (strripos($svg, '<?xml') === false)
		{
			$svg = '<?xml version="1.0" encoding="utf-8"?>' . chr(13) . $svg;
		}

		$svg = str_replace('<style>' . $style_str . '</style>', '', $svg);

		return $svg;
	}

	private function getStyles($svg_contents)
	{
		$svg = new \SimpleXMLElement($svg_contents);

		if (empty($svg->style))
		{
			return '';
		}

		$dnode = dom_import_simplexml($svg->style);
		$style = $dnode->textContent;

		$style = str_replace(chr(13),'',$style);
		$style = str_replace(chr(10),'',$style);
		$style = str_replace('	','',$style);

		return $style;
	}

	private function xmlClean($svg_contents)
	{
		$svg = new \SimpleXMLElement($svg_contents);

		foreach ($svg->g as $k => $v)
		{
			foreach ($v->path as $key => $object)
			{
				$dnode = dom_import_simplexml($object->attributes()->d);
				$d = $dnode->textContent;
				$new_d = $d;

				for ($i = 0; $i < strlen($d); $i++)
				{
					if (ctype_alpha($d[$i]))
					{
						$new_d = str_replace($d[$i], $d[$i] . ' ', $new_d);
					}
				}

				$new_d = $this->regexpReplace($new_d);
				$object->attributes()->d = $new_d;
			}
		}

		return $svg->asXML();
	}

	private function optimizeViewbox(&$svg_contents)
	{
		// Обрезать до десятых долей ширину и высоту
		RegExpBuilder::replace($svg_contents, '(width\=\"\d+\.\d)\d+(\D+)\"', '$1$2"', 'U'); //width
		RegExpBuilder::replace($svg_contents, '(height\=\"\d+\.\d)\d+(\D+)\"', '$1$2"', 'U'); //height

		// Обрезать все до десятых долей
		RegExpBuilder::replace($svg_contents, '(\d+\.\d)(\d+){2,}', '$1');
	}

	private function regexpReplace($new_d)
	{
		RegExpBuilder::replace($new_d, '[  ]+', ' ');
		RegExpBuilder::replace($new_d, ',', ', ');
		RegExpBuilder::replace($new_d, '(\d+\.\d{2})(\d+)', '$1');
		RegExpBuilder::replace($new_d, '(\S)\-', '$1 -');
		/*$new_d = str_replace(',', ' ', $new_d);
		$new_d = str_replace(' .', ' 0.', $new_d);
		$new_d = str_replace('-.', '-0.', $new_d);
		$new_d = str_replace('-', ' -', $new_d);

		RegExpBuilder::replace($new_d, '[A-Za-z]', ' ${0} ');
		RegExpBuilder::replace($new_d, '| +|', ' ');
		RegExpBuilder::replace($new_d, '\d+\.\d\d(?!\d)', '${0} ');
		RegExpBuilder::replace($new_d, '\d+\.\d(?!\d)', '${0} ');
		RegExpBuilder::replace($new_d, '\D([.]\d{2})', ' rep ${0}');

		$new_d = str_replace('rep  ', '0', $new_d);

		RegExpBuilder::replace($new_d, '(\d+[.]\d\d)', '$0 ');
		RegExpBuilder::replace($new_d, '([^0-9][.]\d\d)', ' rep$0 ');

		$new_d = str_replace('rep ', '0', $new_d);*/

		return $new_d;
	}

	public function do($svg_content)
	{
		$styles = [];

		$styles_str = $this->getStyles($svg_content);
		$styles_cls = explode('}', $styles_str);

		foreach ($styles_cls as $key => $item)
		{
			$styles_items_array = explode('{', $item);
			$classes = $styles_items_array[0];
			@$style = $styles_items_array[1];
			if (strripos($classes, ',') >= 0)
			{
				$classes_array = explode(',', $classes);

				foreach ($classes_array as $k => $class)
				{
					$styles[substr($class, 1)][] = ' ' . $style;
				}
			}
			else
			{
				$styles[substr($classes, 1)][] = ' ' . $style;
			}
		}

		foreach ($styles as $class => $style)
		{
			$style_tag = ' style="';
			foreach ($style as $key => $item)
			{
				$style_tag .= $item;
				$styles[$class]['style'] =	$style_tag;
			}

			$styles[$class]['style'] .= '"';
		}

		$styles = array_slice($styles, 0, count($styles) - 1);

		$new_svg = $this->findAndReplace($svg_content, $styles, $styles_str);
		$new_svg = $this->xmlClean($new_svg);

		if (strripos($new_svg, 'xmlns:') !== false)
		{
			$new_svg = preg_replace('/(xmlns:.+" )/mU', '', $new_svg);
		}

		$this->optimizeViewbox($new_svg);

		return $new_svg;
	}
}