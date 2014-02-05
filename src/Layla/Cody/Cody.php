<?php namespace Layla\Cody;

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

	public function compileInput($input)
	{
		$package = $input['package'];
		$resources = $input['resources'];

		$files = array();
		foreach($resources as $name => $resource)
		{
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
