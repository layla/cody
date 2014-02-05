# Cody

The Layla Code generator


# CLI usage

`./generator generate [--format="yml"] [--save] [--path="."] [--json] [--sync] [path]`

Arguments:
 path                  path to file or directory containing config code

Options:
 --format              Specify the input format (default: "yml")
 --save                Save code code to path
 --path                Set the path for files (default: ".")
 --json                Return files as JSON
 --sync                Sync code with database

# Use in PHP

1) add this following line to the `require` section in your `composer.json`

`"layla/cody": "dev-master"`

2) run `composer update`

3) Register Cody's services by calling the following code
```php
use Layla\Cody\CodyServiceProvider;
use Illuminate\Container\Container as Application;

$app = new Application;
$provider = new CodyServiceProvider($app);
$provider->register();
```

In case you already have a (compatible) container, you can pass that into the ServiceProvider.

4) Profit!
```php
$input = array(
	'package' => 'Example.Package',
	'resources' => array(
		'Models.News' => array(
			'model' => array(
				'relations' => array(
					'categories' => array(
						'other' => 'Models.Category'
					)
				)
			),
			'compilers' => array(
				'laravel-php'
			)
		)
	)
);

$files = $app->make('cody')->compileInput($input);

foreach($files as $filename => $content)
{
	// save it, echo it, do whatever you want to do with it
}
```
