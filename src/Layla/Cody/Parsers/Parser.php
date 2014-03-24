<?php namespace Layla\Cody\Parsers;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Parser {

	public function parseDir($path)
	{
		$extension = $this->extension;

		$path = realpath($path);

		$contents = array();
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)
		{
			if(in_array(basename($filename), array('.', '..')))
			{
				continue;
			}

			$parts = explode('/', str_replace($path.'/', '', $filename));
			$className = substr(array_pop($parts), 0, - strlen('.'.$extension));

			$package = array_shift($parts).'.'.array_shift($parts);
			$namespace = implode('.', $parts);

			$resourceIdentifier = $namespace.'.'.$className;

			$resource = array(
				$resourceIdentifier => $this->parseFile($filename)
			);

			$contents[$package] = array_key_exists($package, $contents) ? array_merge($contents[$package], $resource) : $resource;
		}

		return $contents;
	}

	public function parseFile($file)
	{
		$content = file_get_contents($file);

		return $this->parse($content);
	}

}
