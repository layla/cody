<?php namespace Layla\Cody\Compilers;

class Compiler {

	public function __construct($app, $resource)
	{
		$this->app = $app;
		$this->resource = $resource;

		$this->setup();
	}

	/**
	 * Runs after properties are setup,
	 * Allows for easily modifying the configuration by extending a compiler
	 *
	 * @return void
	 */
	public function setup(){}

	/**
	 * Compile an array of arrays into a evenly spaced table
	 *
	 * @param  array   $rows    The rows of the table
	 * @param  integer $padding Amount of spaces to add as padding
	 *
	 * @return string
	 */
	protected function compileTable($rows, $padding = 1)
	{
		$maxLengthPerColumn = array();
		foreach($rows as $columns)
		{
			foreach($columns as $i => $value)
			{
				$length = strlen($value);
				if( ! isset($maxLengthPerColumn[$i]) || $maxLengthPerColumn[$i] < $length)
				{
					$maxLengthPerColumn[$i] = $length;
				}
			}
		}

		$content = "";
		foreach($rows as $columns)
		{
			foreach($columns as $i => $value)
			{
				$content .= sprintf("%-".($maxLengthPerColumn[$i] + $padding)."s", $value);
			}

			$content .= "\n";
		}

		return $content;
	}

	/**
	 * Remove tabs from a text
	 *
	 * @param  string $text
	 *
	 * @return string
	 */
	protected function removeTabs($text)
	{
		return str_replace("\t", "", $text);
	}

	/**
	 * Increase tabs on a text
	 *
	 * @param  string  $text
	 * @param  integer $amount Amount of tabs to add
	 *
	 * @return string
	 */
	protected function indent($text, $amount = 1)
	{
		$lines = array();
		foreach(explode("\n", $text) as $i => $line)
		{
			$lines[] = str_repeat("\t", $amount).$line;
		}

		return implode("\n", $lines);
	}

	// setNamespace
	// setName
	// setBase
	// setUses

	public function addProperty($name, $configuration)
	{
		$this->resource->add('properties', $name, $configuration);

		return $this;
	}

	public function addMethod($name, $configuration, $body)
	{
		$configuration['content'][$this->compiler] = $body;

		$this->resource->add('methods', $name, $configuration);

		return $this;
	}

	public function get($key, $default = null, $on = null)
	{
		if(is_null($on))
		{
			$on = $this->resource->getConfiguration();
		}

		return array_key_exists($key, $on) ? $on[$key] : $default;
	}

}
