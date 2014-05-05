<?php namespace Layla\Cody\Compilers\Php\Doctrine;

use Layla\Cody\Compilers\Php\Core\ClassCompiler;

class ModelCompiler extends ClassCompiler {

    public function setup()
    {
        $columns = $this->get('columns', array());
        $relations = $this->get('relations', array());

        foreach ($columns as $key => $options) {
            $this->addProperty($key, array(
                "type" => $options['type'],
                "comment" => "The $key property",
                "value" => null
            ));
        }

        // $this->addProperty('fillable', array(
        //     "type" => "array",
        //     "comment" => "The attributes that are mass assignable",
        //     "value" => array_keys($columns)
        // ));

        // foreach($relations as $name => $relation)
        // {
        //     $arg = '';
        //     if($relation['other']) {
        //         $namespaceCompiler = $this->getNamespaceCompilerFor($relation['other']);
        //         $arg = "'".$namespaceCompiler->getName()."'";
        //     }

        //     $this->addMethod($name, array(
        //         "returnType" => $namespaceCompiler->getName(),
        //         "comment" => "Relation with ".$namespaceCompiler->getClass()
        //     ), 'return $this->'.$relation['type']."(".$arg.");");
        // }
    }

}
