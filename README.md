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
  __resources__
```

Property | Description
--- | ---
`package` | Contains the vendor and name of the page, seperated with a `.` and capitalized.<br>The reason we capitalize the vendor and name is because this way, it will contain more information for our compilers.<br>Compilers will use the Package name in filenames and namespaces, this may differ per compiler.
`resources` | Determines the resources that are present in the package

## Resources

The resources are defined with the names as the key, and the configurations as the value

```yaml
Models.User:
  __resource_configuration__
Models.NewsItem
  __resource_configuration__
```

## Resource Configuration

The resource configuration may only contain 2 keys.
The compilers key should always be present, it tells Cody what compiler(s) it should use to compile your resource.
The second key can be one of the following:

Key | Type | Description
--- | --- | ---
`compilers` | `compiler` | Indicates what compilers should be used to compile the resource
`class` | `class configuration` | Indicates the resource is of type Class, value of this key is the configuration for the class
`model` | `model configuration` | Indicates the resource is of type Model, value of this key is the configuration for the model
`controller` | `controller configuration` | Indicates the resource is of type Controller, value of this key is the configuration for the controller

The compiler expects the resource to ONLY contain the `compilers` property and on of the available types.

## Compiler

The available compilers are

```yaml
- php-core
- php-laravel
- js-core
- js-ember
```

### Class configuration

Every resource has a `name`, it is the only key of the resource object.
The value of the key is an object containing the following properties

Key | Type | Description
--- | --- | ---
`base` | string | Indicates the base class of this class
`properties` | `property configuration` | Indicates the properties that should be present on the class
`methods` | `method configuration` | Indicates the methods that should be present on the class

```yaml
base: MyApp.Foundation.Models.Base
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
