<?php namespace Layla\Cody;

use Exception;

use Layla\Cody\Blueprints\Package;
use Layla\Cody\Blueprints\Resource;

class Objectifier {

	public static function objectify($input)
	{
		$packages = array();
		foreach($input as $packageIdentifier => $packageConfiguration)
		{
			// create the package or get the existing one
			if(array_key_exists($packageIdentifier, $packages))
			{
				$package = $packages[$packageIdentifier];
			}
			else
			{
				list($vendor, $name) = explode('.', $packageIdentifier);
				$package = new Package($vendor, $name);

				// add the new package to our index
				$packages[$packageIdentifier] = $package;
			}

			// grab the compilers key off the package config if it is set there
			if(array_key_exists('compilers', $packageConfiguration))
			{
				$compilers = $packageConfiguration['compilers'];
			}

			$resources = array_key_exists('resources', $packageConfiguration) ? $packageConfiguration['resources'] : array();
			foreach($resources as $resourceIdentifier => $resourceConfiguration)
			{
				static::validateResourceConfiguration($resourceConfiguration);

				// grab the compilers key off the resource if it is set there
				if(array_key_exists('compilers', $resourceConfiguration))
				{
					$compilers = $resourceConfiguration['compilers'];
				}

				if( ! isset($compilers))
				{
					throw new Exception("Syntax error: no 'compilers' key found in package or resource. Given resource is: ".json_encode($resourceConfiguration, JSON_PRETTY_PRINT));
				}

				$type = $resourceConfiguration['type'];

				$package->addResource(new Resource($package, $type, $resourceIdentifier, $resourceConfiguration, $compilers));
			}
		}

		return $packages;
	}

	protected static function validateResourceConfiguration($configuration)
	{
		if( ! array_key_exists('type', $configuration))
		{
			throw new Exception("Syntax error: no 'type' key found in resource configuration. Given resource is: ".json_encode($configuration, JSON_PRETTY_PRINT));
		}
	}

}
