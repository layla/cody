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

	public function compileInput($input, $format = null)
	{
		if( ! is_null($format))
		{
			$parser = $this->app->make('parsers.'.$format);

			$input = $parser->parse($input);
		}

		$packages = Objectifier::objectify($input);

		$results = array();
		foreach($packages as $package)
		{
			foreach($package->getResources() as $resource)
			{
				foreach($resource->getCompilers() as $identifier)
				{
					$compiler = $this->app->make('compiler.'.$identifier, array($resource));

					$results[$compiler->getDestination()] = $compiler->compile();
				}
			}
		}

		return $results;
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
