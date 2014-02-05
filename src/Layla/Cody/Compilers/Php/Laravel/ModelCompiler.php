<?php namespace Layla\Cody\Compilers\Php\Laravel;

use Layla\Cody\Compilers\Php\Core\ClassCompiler;

class ModelCompiler extends ClassCompiler {

	public function setup()
	{
		if(array_key_exists('relations', $this->configuration))
		{
			foreach($this->configuration['relations'] as $name => $relation)
			{
				$namespaceCompiler = $this->getNamespaceCompilerFor($this->package.'.'.$relation['other']);

				$this->addMethod($name, array(
					"returnType" => "array",
					"comment" => "Relation with ".$namespaceCompiler->getClass()
				), 'return $this->'.$relation['type']."('".$namespaceCompiler->getName()."');");
			}
		}
	}

}
