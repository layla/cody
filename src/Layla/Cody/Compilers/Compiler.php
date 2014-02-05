<?php namespace Layla\Cody\Compilers;

class Compiler {

	public function __construct($app, $package, $name, $configuration)
	{
		$this->app = $app;
		$this->package = $package;
		$this->name = $name;
		$this->configuration = $configuration;
		$this->setup();
	}

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
		$this->configuration['properties'][$name] = $configuration;

		return $this;
	}

	public function addMethod($name, $configuration, $body)
	{
		$configuration['content'][$this->compiler] = $body;

		$this->configuration['methods'][$name] = $configuration;

		return $this;
	}

	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->configuration) ? $this->configuration[$key] : $default;
	}

}
