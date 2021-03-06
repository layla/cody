<?php namespace Layla\Cody\Compilers\Php\Core;

use Layla\Cody\Compilers\PhpCompiler;
use Layla\Cody\Compilers\Php\Core\MethodCompiler;

class ClassCompiler extends PhpCompiler {

	public $type = 'class';

	/**
	 * Get the other classnames this class needs
	 *
	 * @return array
	 */
	public function getUses()
	{
		$package = $this->resource->getPackage();

		$uses = $this->get('uses', array());

		$namespaceCompiler = $this->getNamespaceCompilerFor($this->get('base'));

		// Add baseclass to use statements if necesarry
		if( ! is_null($this->get('base')) && ! $namespaceCompiler->isWithinNamespaceOf($package->getVendor().'.'.$package->getName()))
		{
			$uses[] = $namespaceCompiler->getName();
		}

		// Remove any unneeded uses
		// $me = $this;
		// array_filter($uses, function($use) use ($me)
		// {
		// 	return $me->getNameCompiler()->getNamespace();
		// });

		// Sort the bunch
		asort($uses);

		return $uses;
	}

	/**
	 * Get the class comment
	 *
	 * @return string
	 */
	protected function compileComment()
	{
		if($this->get('comment'))
		{
			return $this->comment($this->get('comment'));
		}

		return "";
	}

	/**
	 * Compile the class
	 *
	 * @return string The contents of the file
	 */
	public function compile()
	{
		$nameCompiler = $this->getNamespaceCompilerFor($this->resource->getIdentifier());
		$baseCompiler = $this->getNamespaceCompilerFor($this->get('base'));

		// Start the php file
		$content = "<?php";

		// Add the namespace if necessary
		if($nameCompiler->hasNamespace())
		{
			$content .= " namespace ".$nameCompiler->getNamespace().";\n";
		}
		$content .= "\n";

		// Add the use statements if necessary
		if(count($this->getUses()) > 0)
		{
			foreach($this->getUses() as $use)
			{
				$content .= "use ".$use.";\n";
			}

			$content .= "\n";
		}

		if($this->resource->get('comment'))
		{
			$content .= $this->comment($this->resource->get('comment'))."\n";
		}

		// Add the name of the class
		$content .= $this->type." ".$nameCompiler->getClass();

		// Extend a base class if necessary
		if( ! is_null($this->get('base')))
		{
			$content .= " extends ".$baseCompiler->getClass();
		}

		// Open the class
		$content .= " {\n";

		// Add properties
		$properties = $this->get('properties');
		if($properties)
		{
			foreach($properties as $name => $configuration)
			{
				$compiler = new PropertyCompiler($name, $configuration);

				$content .= "\n".$this->indent($compiler->compile())."\n";
			}

		}

		// // Add methods
		$methods = $this->get('methods');
		if($methods)
		{
			foreach($methods as $name => $configuration)
			{
				if(isset($configuration['content'][$this->compiler]))
				{
					$configuration['content'] = $configuration['content'][$this->compiler];
				}
				else
				{
					$configuration['content'] = "";
				}

				$compiler = new MethodCompiler($name, $configuration);

				$content .= "\n".$this->indent($compiler->compile())."\n";
			}
		}

		// Close the class
		$content .= "\n}\n\n";

		return $content;
	}

}
