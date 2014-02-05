<?php namespace Layla\Cody\Parsers;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Parser {

	public function parseDir($path)
	{
		$format = $this->format;

		$path = realpath($path);

		$contents = array();

		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)
		{
			if(in_array(basename($filename), array('.', '..')))
			{
				continue;
			}

			$name = str_replace($path.'/', '', $filename);
			$name = substr($name, 0, - strlen('.'.$format));
			$parts = explode('/', $name);
			$namespace = array_shift($parts).'.'.array_shift($parts);
			$name = implode('.', $parts);

			$contents['package'] = $namespace;
			$contents['resources'][$name] = $this->parseFile($filename);
		}

		return $contents;
	}

	public function parseFile($file)
	{
		$content = file_get_contents($file);

		return $this->parse($content);
	}

}
