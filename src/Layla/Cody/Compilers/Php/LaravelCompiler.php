<?php namespace Layla\Cody\Compilers\Php;

use Layla\Cody\Compilers\PhpCompiler;

use Layla\Cody\Compilers\Php\Laravel\ModelCompiler;
use Layla\Cody\Compilers\Php\Laravel\ControllerCompiler;
use Layla\Cody\Compilers\Php\Laravel\MigrationCompiler;

class LaravelCompiler extends PhpCompiler {

	public function compile()
	{
		switch($this->resource->getType())
		{
			case 'model':
				$compiler = new ModelCompiler($this->app, $this->resource);
			break;

			case 'controller':
				$compiler = new ControllerCompiler($this->app, $this->resource);
			break;

			case 'migration':
				$compiler = new MigrationCompiler($this->app, $this->resource);
			break;
		}

		return $compiler->compile();
	}

}
