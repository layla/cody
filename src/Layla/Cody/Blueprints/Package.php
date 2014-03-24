<?php namespace Layla\Cody\Blueprints;

class Package {

	public $resources = array();

	public function __construct($vendor, $name)
	{
		$this->vendor = $vendor;
		$this->name = $name;
	}

	public function addResource(Resource $resource)
	{
		$this->resources[$resource->getName()] = $resource;
	}

	public function getResources()
	{
		return $this->resources;
	}

	public function getVendor()
	{
		return $this->vendor;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getIdentifier()
	{
		return $this->vendor.'.'.$this->name;
	}

}
