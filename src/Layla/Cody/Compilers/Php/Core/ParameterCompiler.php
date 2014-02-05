<?php namespace Layla\Cody\Compilers\Php\Core;

use Layla\Cody\Compilers\PhpCompiler;

class ParameterCompiler extends PhpCompiler {

	/**
	 * Create a new ParameterCompiler instance
	 *
	 * @param string $name The name of the parameter
	 * @param string $configuration The configuration
	 */
	public function __construct($name, $configuration)
	{
		$this->name = $name;
		$this->configuration = $configuration;
	}

	public function getLine()
	{
		return array('@param', $this->get('type'), '$'.$this->name, $this->get('comment'));
	}

}
