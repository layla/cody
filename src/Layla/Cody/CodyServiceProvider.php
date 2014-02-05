<?php namespace Layla\Cody;

use Layla\Cody\Cody;

use Layla\Cody\Parsers\Json;
use Layla\Cody\Parsers\Stdin;
use Layla\Cody\Parsers\Yaml;

use Layla\Cody\Compilers\Php\LaravelCompiler;
use Layla\Cody\Compilers\Js\EmberCompiler;
use Layla\Cody\Compilers\Python\DjangoCompiler;

class CodyServiceProvider {

	public function __construct($app)
	{
		$this->app = $app;
	}

	protected function registerCody()
	{
		$this->app->bind('cody', function($app)
		{
			return new Cody($app);
		});
	}

	protected function registerFormats()
	{
		$this->app->bind('formats.json', function()
		{
			return new Json;
		});

		$this->app->bind('formats.stdin', function()
		{
			return new Stdin;
		});

		$this->app->bind('formats.yml', function()
		{
			return new Yaml;
		});
	}

	protected function registerCompilers()
	{
		$app = $this->app;

		$app->bind('compiler.php-laravel', function($app, $arguments)
		{
			list($package, $name, $configuration) = $arguments;

			return new LaravelCompiler($app, $package, $name, $configuration);
		});

		$app->bind('compiler.js-ember', function($app, $arguments)
		{
			list($package, $name, $configuration) = $arguments;

			return new EmberCompiler($app, $app, $name, $configuration);
		});

		$app->bind('compiler.python-django', function($app, $arguments)
		{
			list($package, $name, $configuration) = $arguments;

			return new DjangoCompiler($app, $app, $name, $configuration);
		});
	}

	/**
	 * Register the helpers.
	 *
	 * @return void
	 */
	public function registerHelpers()
	{
		require __DIR__.'/../../helpers.php';
	}

	public function register()
	{
		$this->registerCody();
		$this->registerFormats();
		$this->registerCompilers();
		$this->registerHelpers();
	}

}