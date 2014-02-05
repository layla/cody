<?php namespace Layla\Cody\Compilers;

use Layla\Cody\Compilers\Php\Core\NamespaceCompiler;

class PhpCompiler extends Compiler {

	public $compiler = 'php-core';

	/**
	 * Map of types
	 *
	 * @var array
	 */
	protected $typeMap = array(
		'',
		'array',
		'string',
		'integer',
		'float',
		'object'
	);

	/**
	 * Get the namespace compiler for a given resource identifier
	 *
	 * @param string $resourceIdentifier The identifier of the resource
	 *
	 * @return \Layla\Cody\Compilers\Php\Core\NamespaceCompiler
	 */
	public function getNamespaceCompilerFor($resourceIdentifier)
	{
		return new NamespaceCompiler($resourceIdentifier);
	}

	/**
	 * Turn an array into it's string form
	 *
	 * @param  array $array
	 *
	 * @return string
	 */
	protected function compileArray($array)
	{
		$segments = array();
		foreach($array as $key => $value)
		{
			$segments[] = (is_int($key) ? '' : "'".$key."'").(empty($value) ? '' : (is_int($value) ? ' => ' : '')."'".$value."'");
		}

		return "array(\n\t".implode(",\n\t", $segments)."\n);";
	}

	/**
	 * Comment text
	 *
	 * @param  string  $text       The text to comment
	 * @param  boolean $multiline  Should a multiline form comment be used?
	 *
	 * @return string The commented string
	 */
	public function comment($text, $multiline = true)
	{
		$lines = explode("\n", $text);

		if($multiline)
		{
			return "/**\n * ".implode("\n * ", $lines)."\n */";
		}
		else
		{
			return "// ".implode("\n// ", $lines);
		}
	}

	protected function compileType($type)
	{
		if(is_int($type))
		{
			return $this->typeMap[$type];
		}

		$namespaceCompiler = $this->getNamespaceCompilerFor($type);

		$typeParts = explode('.', $type);

		return (count($typeParts) > 1 ? "\\" : "").$namespaceCompiler->getName();
	}

	public function export($thing)
	{
		if(is_array($thing))
		{
			return $this->compileArray($thing);
		}

		return $thing;
	}

}
