<?php namespace Layla\Cody;

use Layla\Cody\Blueprints\Package;
use Layla\Cody\Blueprints\Resource;

class Objectifier {

	public static function objectify($input)
	{
		$packages = array();
		foreach($input as $identifier => $resources)
		{
			// create the package or get the existing one
			if(array_key_exists($identifier, $packages))
			{
				$package = $packages[$identifier];
			}
			else
			{
				list($vendor, $name) = explode('.', $identifier);
				$package = new Package($vendor, $name);

				// add the new package to our index
				$packages[$identifier] = $package;
			}

			foreach($resources as $name => $resource)
			{
				static::validateResource($resource);

				// grab the compilers key off the resource
				if(array_key_exists('compilers', $resource))
				{
					$compilers = $resource['compilers'];
					unset($resource['compilers']);
				}

				// loop over the last key and value pair in the array, we are using a foreach for convenience here, there is probably an even easier way to do this.
				foreach($resource as $type => $configuration)
				{
					$resource = new Resource($package, $type, $name, $configuration, $compilers);
				}

				$package->addResource($resource);
			}
		}

		return $packages;
	}

	protected static function validateResource($resource)
	{
		if( ! array_key_exists('compilers', $resource))
		{
			throw new Exception("Syntax error: no 'compilers' key found. Given resource is: ".json_encode($resource, JSON_PRETTY_PRINT));
		}

		if( ! count($resource) == 2)
		{
			$allowedKeys = array('compilers', 'model', 'controller', 'migration');

			$faultyKeys = array();
			foreach(array_keys($resource) as $key)
			{
				if( ! in_array($key, $allowedKeys))
				{
					$faultyKeys[] = $key;
				}
			}

			throw new Exception("Syntax error: faulty keys found in resource (".implode(',', $faultyKeys)."). Given resource is: ".json_encode($resource, JSON_PRETTY_PRINT));
		}
	}

}
