<?php namespace Layla\Cody;

use Layla\Cody\Cody;

use Layla\Cody\Parsers\Json;
use Layla\Cody\Parsers\Stdin;
use Layla\Cody\Parsers\Yaml;
use Layla\Cody\Parsers\Php;

use Layla\Cody\Compilers\PhpCompiler;
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

	protected function registerParsers()
	{
		$this->app->bind('parsers.json', function()
		{
			return new Json;
		});

		$this->app->bind('parsers.stdin', function()
		{
			return new Stdin;
		});

		$this->app->bind('parsers.yml', function()
		{
			return new Yaml;
		});

		$this->app->bind('parsers.yml', function()
		{
			return new Yaml;
		});

		$this->app->bind('parsers.php', function()
		{
			return new Php;
		});
	}

	protected function registerCompilers()
	{
		$app = $this->app;

		$app->bind('compiler.php-core', function($app, $arguments)
		{
			list($resource) = $arguments;

			return new PhpCompiler($app, $resource);
		});

		$app->bind('compiler.php-laravel', function($app, $arguments)
		{
			list($resource) = $arguments;

			return new LaravelCompiler($app, $resource);
		});

		$app->bind('compiler.js-ember', function($app, $arguments)
		{
			list($resource) = $arguments;

			return new EmberCompiler($app, $resource);
		});

		$app->bind('compiler.python-django', function($app, $arguments)
		{
			list($resource) = $arguments;

			return new DjangoCompiler($app, $resource);
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
		$this->registerParsers();
		$this->registerCompilers();
		$this->registerHelpers();
	}

}
