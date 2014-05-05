<?php namespace Layla\Cody\Compilers\Php;

use Layla\Cody\Compilers\PhpCompiler;

use Layla\Cody\Compilers\Php\Doctrine\ModelCompiler;

class DoctrineCompiler extends PhpCompiler {

    public function compile()
    {
        switch($this->resource->getType())
        {
            case 'model':
                $compiler = new ModelCompiler($this->resource);
            break;
        }

        return $compiler->compile();
    }

    public function getDestination()
    {
        $package = $this->resource->getPackage();

        return strtolower($package->getVendor()).'/'.strtolower($package->getName()).'/src/'.implode('/', explode('.', $this->resource->getName())).'.php';
    }

}
