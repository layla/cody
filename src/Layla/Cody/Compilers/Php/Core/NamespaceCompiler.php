<?php namespace Layla\Cody\Compilers\Php\Core;

class NamespaceCompiler {

	protected $seperator = "\\";

	/**
	 * Create a new NamespaceCompiler instance
	 *
	 * @param string $resourceIdentifier The identifier of the resource (vendor.package::path.to.classname)
	 */
	public function __construct($resourceIdentifier)
	{
		$this->resourceIdentifier = $resourceIdentifier;
	}

	/**
	 * Get the classname without the namespace
	 *
	 * @return string
	 */
	public function getClass()
	{
		$parts = explode('.', $this->resourceIdentifier);

		return end($parts);
	}

	public function getName()
	{
		$parts = explode('.', $this->resourceIdentifier);

		return implode($this->seperator, $parts);
	}

	/**
	 * Get the namespace without the name of the class
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		$parts = explode('.', $this->resourceIdentifier);

		array_pop($parts);

		return implode($this->seperator, $parts);
	}

	public function hasNamespace()
	{
		return ! empty($this->getNamespace());
	}

	/**
	 * Check if a class is within the namespace of another class
	 *
	 * @param  string  $resourceIdentifier The identifier of the resource
	 *
	 * @return boolean
	 */
	public function isWithinNamespaceOf($resourceIdentifier)
	{
		$thisNamespace = $this->getNamespace();
		$otherNamespace = with(new static($resourceIdentifier))->getNamespace();

		return $thisNamespace == $otherNamespace;
	}

}
