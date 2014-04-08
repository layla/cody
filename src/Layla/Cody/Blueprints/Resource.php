<?php namespace Layla\Cody\Blueprints;

class Resource {

	public function __construct($package, $type, $name, $configuration, $compilers)
	{
		$this->package = $package;
		$this->type = $type;
		$this->name = $name;
		$this->configuration = $configuration;
		$this->compilers = $compilers;
	}

	public function getPackage()
	{
		return $this->package;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getConfiguration()
	{
		return $this->configuration;
	}

	public function setConfiguration($configuration)
	{
		$this->configuration = $configuration;

		return $this;
	}

	public function getCompilers()
	{
		return $this->compilers;
	}

	public function getIdentifier()
	{
		return $this->package->getIdentifier().'.'.$this->name;
	}

	public function get($key, $default = null)
	{
		return isset($this->configuration[$key]) ? $this->configuration[$key] :  $default;
	}

	public function set($key, $value)
	{
		$this->configuration[$key] = $value;

		return $this;
	}

	public function add($type, $key, $value)
	{
		$this->configuration[$type][$key] = $value;
	}

}

