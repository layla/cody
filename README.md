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
  __RESOURCES__
```

Property | Description
--- | ---
`package` | Contains the vendor and name of the page, seperated with a `.` and capitalized.<br>The reason we capitalize the vendor and name is because this way, it will contain more information for our compilers.<br>Compilers will use the Package name in filenames and namespaces, this may differ per compiler.
`resources` | Determines the [resources](#resources) that are present in the package

<a name="resources"></a>
## Resources

The resources are defined with the names as the key, and the configurations as the value

Property | Description
--- | ---
name | The name property indicates the name as the key, and the [resource configuration](#resource-configuration) as value

An example:
```yaml
Models.User:
  __RESOURCE_CONFIGURATION__
Models.NewsItem
  __RESOURCE_CONFIGURATION__
```

<a name="resource-configuration"></a>
## Resource Configuration

The resource configuration may only contain 2 keys.
The compilers key should always be present, it tells Cody what compiler(s) it should use to compile your resource.
The second key can be one of the following:

Key | Type | Description
--- | --- | ---
`compilers` | array | Indicates what compilers should be used to compile the resource, available options are:<br>**php-core**<br>**php-laravel**<br>**js-core**<br>**js-ember**
`class` | [class configuration](#class-configuration) | Indicates the resource is of type Class, value of this key is the configuration for the class
`model` | [model configuration](#model-configuration) | Indicates the resource is of type Model, value of this key is the configuration for the model
`controller` | [controller configuration](#controller-configuration) | Indicates the resource is of type Controller, value of this key is the configuration for the controller

The compiler expects the resource to ONLY contain the `compilers` property and on of the available types.

An example:
```yaml
Models.User:
  class:
    __CLASS_CONFIGURATION__
  compilers:
    - php-laravel
```

<a name="class-configuration"></a>
## Class configuration

The class configuration holds all the information we need to build a class.
The available options are

Key | Type | Description
--- | --- | ---
`base` | string | Indicates the base class of this class
`properties` | [property configuration](#property-configuration) | Keys represent the name of the property, values contain the [property configuration](#property-configuration)
`methods` | [method configuration](#method-configuration) | Keys represent the method name, values contain the [method configuration](#method-configuration)

```yaml
base: MyApp.Foundation.Models.Base
properties:
  rules:
    __PROPERTY_CONFIGURATION__
methods:
  get.rules:
    __METHOD_CONFIGURATION__
  set.rules
    __METHOD_CONFIGURATION__
```

<a name="model-configuration"></a>
## Model configuration

A model is an extension of the class, it allows you to specify relations and columns, and will automatically add the necesarry methods / properties for you, depending on the compiler.

Key | Type | Description
--- | --- | ---
`base` | string | Indicates the base class of this class
`properties` | [property configuration](#property-configuration) | Indicates the properties that should be present on the class
`methods` | [method configuration](#method-configuration) | Indicates the methods that should be present on the class
`relations` | [relation configuration](#relation-configuration) | Indicates the relations that should be present on the model
`columns` | [column configuration](#column-configuration) | Indicates the columns that should be present on the model

```yaml
base: MyApp.Foundation.Models.Base
properties:
  rules:
    __property_configuration
methods:
  get.rules:
    __METHOD_CONFIGURATION__
  set.rules
    __METHOD_CONFIGURATION__
relations:
  __RELATION_CONFIGURATION__
columns:
  __COLUMN_CONFIGURATION__
```

<a name="method-configuration"></a>
## Method configuration

A method can be added to class resources, or subclasses thereof (models, controllers, etc.)

Key | Type | Description
--- | --- | ---
`body` | array | Keys represent the compiler name, values contain the method body for the given compiler
`comment` | string | The method's comment
`returnType` | * | The return type, resource identifier or one of the following types:<br>**array**<br>**integer**

An example:
```yaml
body:
  php-core: return $this->rules;
comment: Get the rules for this model
returnType: array
```

<a name="property-configuration">
## Property configuration

A property can be added to class resources, or subclasses thereof (models, controllers, etc.)

Key | Type | Description
--- | --- | ---
`value` | * | The value of the property
`comment` | string | The property's comment

An example:
```yaml
value:
  name: required
  email: required|email
comment: The rules for this model
```

<a name="relation-configuration">
## Relation configuration

A relation can be added to a model resource, or subclasses thereof

Key | Type | Description
--- | --- | ---
`type` | * | The type of the relation
`other` | string | The other resource

An example:
```yaml
type: hasMany
other: Models.TrailCategory
```

<a name="column-configuration">
## Column configuration

A column can be added to a model resource, or subclasses thereof

Key | Type | Description
--- | --- | ---
`type` | * | The type of the column
`max` | string | The max size
`nullable` | boolean | Indicates if the columns is nullable

An example:
```yaml
type: string
max: 255
nullable: true
```

# Generate from CLI

The generator can take your input file and spit out JSON, or save the files to their calculated destinations.
The input can even be a folder, if that's the case, Cody will use the top 2 folders as the package name, and all the folders below indicate the namespace.
The files found in the deepest folders represent the resource name, and the contents of the file represent the resource configuration.
An example of this setup can be found in `vendor/cody/example`

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
