# Cody

Cody is a code generator that generates objects and resources for different languages and frameworks.
Cody utilises a very simple configuration format for defining your objects and resources.

# The input format

The input format for the generator is as following (example is given in yaml, other formats are supported too)

## The root

The root of the input contains the package name the resources that exist within in the package.

```yaml
package: Vendor.Name
resources:
  ...
```

Property | Description
--- | ---
`package` | Contains the vendor and name of the page, seperated with a `.` and capitalized.<br>The reason we capitalize the vendor and name is because this way, it will contain more information for our compilers.<br>Compilers will use the Package name in filenames and namespaces, this may differ per compiler.
`resources` | Determines the resources that are present in the package

## Resources

There are many types of resources available, let's start with the core language resources

### Class

```yaml
Models.User:
	class:
		properties:
			rules:
				value:
					name: required
					email: required|email
				comment: The rules for this model
		methods:
			get.rules:
				body:
					php-core: return $this->rules;
				comment: Get the rules for this model
				returnType: array
			set.rules
				parameters:
					rules:
						default: array
				body:
					php-core: $this->rules = $rules;
```

# Generate from CLI

`./generator generate [--format="yml"] [--save] [--path="."] [--json] [--sync] [path]`

## Arguments

Argument | Description
--- | ---
path | path to file or directory containing config code

## Options

Option | Description
--- | ---
--format | Specify the input format (default: "yml")
--save | Save code code to path
--path | Set the path for files (default: ".")
--json | Return files as JSON
--sync | Sync code with database

# Generate from PHP

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
