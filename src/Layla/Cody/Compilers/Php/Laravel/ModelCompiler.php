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
			if(array_key_exists('other', $relation)) {
				$namespaceCompiler = $this->getNamespaceCompilerFor($relation['other']);
				$otherClass = $namespaceCompiler->getClass();
				$arg = "'".$namespaceCompiler->getName()."'";
			} else {
				$otherClass = null;
				$arg = $this->export(array_get($relation, 'morph_name', null));
			}

			$this->addMethod($name, array(
				"returnType" => "lluminate\Database\Eloquent\Relations\Relation",
				"comment" => is_null($otherClass) ? "Relation" : "Relation with " . $otherClass
			), 'return $this->'.$relation['type']."(".$arg.");");
		}
	}

}
