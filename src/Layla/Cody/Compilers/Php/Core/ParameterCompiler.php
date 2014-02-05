<?php namespace Layla\Cody\Compilers\Php\Core;

use Layla\Cody\Compilers\PhpCompiler;

class ParameterCompiler extends PhpCompiler {

	/**
	 * Create a new ParameterCompiler instance
	 *
	 * @param string $blueprint The blueprint to compile
	 */
	public function __construct($configuration)
	{
		$this->configuration = $configuration;
	}

	public function getLine()
	{
		return array('@param', $this->get('type'), '$'.$this->name, $this->get('comment'));
	}

}
