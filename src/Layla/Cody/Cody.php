<?php namespace Layla\Cody;

use Exception;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use Symfony\Component\Yaml\Yaml;

class Cody {

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function setInput($input)
	{
		$this->input = $input;
	}

	public function compileResource($type, $package, $name, $configuration, $compilers)
	{
		$files = array();
		foreach($compilers as $compiler)
		{
			$compiler = $this->app->make('compiler.'.$compiler, array($package, $name, $configuration));

			list($path, $content) = $compiler->compile($type);

			$files[$path] = $content;
		}

		return $files;
	}

	public function compileInput($input, $format = null)
	{
		if( ! is_null($format))
		{
			$parser = $this->app->make('parsers.'.$format);

			$input = $parser->parse($input);
		}

		if( ! isset($input['package']))
		{
			throw new Exception("Syntax error: 'package' key not present in input. Given input is: ".json_encode($input, JSON_PRETTY_PRINT));
		}

		if( ! isset($input['resources']))
		{
			throw new Exception("Syntax error: 'resources' key not present in input. Given input is: ".json_encode($input, JSON_PRETTY_PRINT));
		}

		$package = $input['package'];
		$resources = $input['resources'];

		$files = array();
		foreach($resources as $name => $resource)
		{
			if( ! isset($resource['compilers']))
			{
				throw new Exception("Syntax error: 'compilers' key not present in resource configuration. Given resource configuration is: ".json_encode($resource, JSON_PRETTY_PRINT));
			}

			$compilers = $resource['compilers'];
			unset($resource['compilers']);

			$type = key($resource);
			$configuration = $resource[$type];

			$files = array_merge($files, $this->compileResource($type, $package, $name, $configuration, $compilers));
		}

		return $files;
	}

	public function json()
	{
		return json_encode($this->compileInput($this->input));
	}

	public function save($path)
	{
		foreach($this->compileInput($this->input) as $file => $content)
		{
			$dir = dirname($path.'/'.$file);
			if( ! is_dir($dir))
			{
				if( ! mkdir($dir, 0755, true))
				{
					return false;
				}
			}

			file_put_contents($path.'/'.$file, $content);
		}

		return true;
	}

}
