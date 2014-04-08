<?php namespace Layla\Cody;

use Exception;

class Resolver {

	public function __construct($packages)
	{
		$this->packages = $packages;
	}

	public function resolve()
	{
		foreach($this->packages as $package)
		{
			foreach($package->getResources() as $resource)
			{
				$configuration = $resource->getConfiguration();

				if(array_key_exists('imports', $configuration))
				{
					$imports = $configuration['imports'];

					if( ! is_array($imports))
					{
						throw new Exception("Syntax error: imports are not of type array. Given resource is: ".json_encode($configuration, JSON_PRETTY_PRINT));
					}

					foreach($imports as $identifier => $keys)
					{
						foreach($keys as $key)
						{
							$value = $this->resolveImport($identifier, $key);

							$newConfiguration = array_merge($configuration, array($key => $value));

							$resource->setConfiguration($newConfiguration);
						}
					}
				}
			}
		}
	}

	public function resolveImport($identifier, $key)
	{
		$parts = explode('.', $identifier);
		$packageVendor = array_shift($parts);
		$packageName = array_shift($parts);
		$resourceName = implode('.', $parts);

		foreach($this->packages as $package)
		{
			if($package->getVendor() !== $packageVendor || $package->getName() !== $packageName) continue;

			foreach($package->getResources() as $resource)
			{
				if($resource->getName() !== $resourceName) continue;

				$configuration = $resource->getConfiguration();

				return $configuration[$key];
			}
		}
	}

}
