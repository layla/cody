<?php namespace Layla\Cody\Compilers\Php\Laravel;

use Layla\Cody\Compilers\Php\Core\ClassCompiler;

class ModelCompiler extends ClassCompiler {

	public function setup()
	{
		$columns = $this->get('columns', array());
		$relations = $this->get('relations', array());

		$this->addProperty('fillable', array(
			"type" => "array",
			"comment" => "The attributes that are mass assignable",
			"value" => array_keys($columns)
		));

		foreach($relations as $name => $relation)
		{
			$namespaceCompiler = $this->getNamespaceCompilerFor($this->package.'.'.$relation['other']);

			$this->addMethod($name, array(
				"returnType" => $namespaceCompiler->getName(),
				"comment" => "Relation with ".$namespaceCompiler->getClass()
			), 'return $this->'.$relation['type']."('".$namespaceCompiler->getName()."');");
		}
	}

}