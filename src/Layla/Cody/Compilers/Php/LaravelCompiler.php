<?php namespace Layla\Cody\Compilers\Php;

use Layla\Cody\Compilers\PhpCompiler;

use Layla\Cody\Compilers\Php\Laravel\ModelCompiler;
use Layla\Cody\Compilers\Php\Laravel\ControllerCompiler;

class LaravelCompiler extends PhpCompiler {

	public function compile($type)
	{
		switch($type)
		{
			case 'model':
				$compiler = new ModelCompiler($this->app, $this->package, $this->name, $this->configuration);
			break;

			case 'controller':
				$compiler = new ControllerCompiler($this->app, $this->package, $this->name, $this->configuration);
			break;
		}

		return $compiler->compile();
	}

}
