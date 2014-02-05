<?php namespace Layla\Cody\Compilers\Php\Core;

use Layla\Cody\Compilers\PhpCompiler;

class PropertyCompiler extends PhpCompiler {

	/**
	 * Create a new PropertyCompiler instance
	 *
	 * @param string $name The name of the parameter
	 * @param string $configuration The configuration
	 */
	public function __construct($name, $configuration)
	{
		$this->name = $name;
		$this->configuration = $configuration;
	}

	/**
	 * Compile the property
	 *
	 * @return string
	 */
	public function compile()
	{
		$comment = $this->comment($this->get('comment')."\n\n@var ".$this->compileType($this->get('type')));
		$property = $this->get('visibility', 'public').' $'.$this->name.(is_null($this->get('value')) ? ';' : ' = '.$this->export($this->get('value')));

		return $comment."\n".$property;
	}

}
