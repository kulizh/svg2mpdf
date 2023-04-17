<?php
namespace Svg4Mpdf\Utils;

class File
{
	private $filename;

	public function __construct($filename)
	{
		if (!is_readable($filename))
		{
			throw new \Exception('Cannot read file ' . $filename);
		}

		$this->filename = $filename;
	}

	public function read()
	{
		$filename = $this->filename;

		$contents = file_get_contents($filename);

		if ((!$contents) || empty($contents))
		{
			throw new \Exception('File ' . $filename . ' is empty');
		}

		return $contents;
	}

	public function write($contents, $append = false)
	{
		$filename = $this->filename;

		if ($append)
		{
			$fpc = file_put_contents($filename, $contents, FILE_APPEND);

			return $fpc;
		}
		
		$fpc = file_put_contents($filename, $contents);

		return $fpc;
	}

	public function clear()
	{
		$filename = $this->filename;

		file_put_contents($filename, '');
	}
}