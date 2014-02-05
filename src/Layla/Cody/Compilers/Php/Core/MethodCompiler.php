<?php namespace Layla\Cody\Compilers\Php\Core;

use Layla\Cody\Compilers\PhpCompiler;

use Layla\Cody\Compilers\Php\Core\ParameterCompiler;

class MethodCompiler extends PhpCompiler {

	/**
	 * Create a new MethodCompiler instance
	 *
	 * @param string $blueprint The blueprint to compile
	 */
	public function __construct($name, $configuration)
	{
		$this->name = $name;
		$this->configuration = $configuration;
	}

	public function getComment()
	{
		$parameters = $this->get('parameters');

		$content = '';
		if( ! is_null($this->get('comment')))
		{
			$content .= $this->get('comment')."\n";
		}

		if( ! is_null($parameters))
		{
			$lines = array();
			foreach($parameters as $name => $configuration)
			{
				$parameter = new ParameterCompiler($configuration);

				$lines[] = $parameter->getLine();
			}

			$content .= $this->compileTable($lines);
		}

		$content .= "\n@return ".$this->compileType($this->get('returnType'))." ".$this->get('returnComment');

		return $this->comment($content);
	}

	public function getName()
	{
		$parts = explode('.', $this->name);

		$newParts = array();
		foreach ($parts as $i => $part)
		{
			$newParts[] = $i == 0 ? $part : ucfirst($part);
		}

		return implode('', $newParts);
	}

	public function compile()
	{
		$comment = $this->getComment();

		return $comment."\n".'public function '.$this->getName()."()\n{\n".$this->indent($this->get('content'))."\n}";
	}

}
