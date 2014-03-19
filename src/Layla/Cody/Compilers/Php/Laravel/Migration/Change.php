<?php namespace Layla\Cody\Compilers\Php\Laravel\Migration;

use Exception;

class Change {

	protected $method;

	protected $arguments = array();

	public function __construct($action, $name, $configuration)
	{
		$this->name = $name;
		$this->configuration = $configuration;

		$this->$action();
	}

	public function createColumn()
	{
		if( ! isset($this->name))
		{
			throw new Exception("Syntax error: migration column configuration does not contain 'name' attribute. Given configuration is: ".json_encode($this->configuration, JSON_PRETTY_PRINT));
		}

		$this->method = $this->configuration['type'];

		if(isset($this->name))
		{
			$this->arguments[] = "'".$this->name."'";
		}

		if(isset($this->configuration['size']))
		{
			$this->arguments[] = $this->configuration['size'];
		}

		if(isset($this->configuration['precision']))
		{
			$this->arguments[] = $this->configuration['precision'];
		}

		$this->default = isset($this->configuration['default']) ? $this->configuration['default'] : null;
	}

	public function renameColumn()
	{
		$this->method = 'renameColumn';

		$this->arguments[] = "'".$this->configuration['old']."'";
		$this->arguments[] = "'".$this->configuration['new']."'";
	}

	public function dropColumn()
	{
		if( ! isset($this->name))
		{
			throw new Exception("Syntax error: migration column configuration does not contain 'name' attribute. Configuration is: ".json_encode($this->configuration, JSON_PRETTY_PRINT));
		}

		$this->method = 'dropColumn';

		$this->arguments[] = "'".$this->name."'";
	}

	public function compile()
	{
		return '$table->'.$this->method.'('.implode(', ', $this->arguments).')'.
			(empty($this->default) ? '' : "->default('".$this->default."')").";\n";
	}

}
