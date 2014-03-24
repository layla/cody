<?php namespace Layla\Cody\Parsers;

use ReflectionClass;

use Symfony\Component\Yaml\Yaml as YamlParser;

use Sami\Parser\DocBlockParser;

class NamespaceConverter extends \PHPParser_NodeVisitorAbstract {

	public $name;

    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Name) {
        	$this->name = $node->toString();
        }
    }

}

class Php extends Parser {

	protected $extension = 'php';

	public function getFullyQualifiedClassName($file)
	{
		// use the emulative lexer here, as we are running PHP 5.2 but want to parse PHP 5.3
		$parser        = new \PHPParser_Parser(new \PHPParser_Lexer);
		$traverser     = new \PHPParser_NodeTraverser;
		$prettyPrinter = new \PHPParser_PrettyPrinter_Default;

		$nameSaver = new NamespaceConverter;
		$traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver); // we will need resolved names
		$traverser->addVisitor($nameSaver);

		try {
		    // read the file that should be converted
		    $code = file_get_contents($file);

		    // parse
		    $stmts = $parser->parse($code);

		    // traverse
		    $stmts = $traverser->traverse($stmts);

		    return $nameSaver->name;
		}
		catch (PHPParser_Error $e) {
			return false;
		}
	}

	public function parse($content, $file)
	{
		$name = $this->getFullyQualifiedClassName($file);

		if( ! in_array($name, get_declared_classes()))
		{
			require $file;
		}

		$this->reflector = new ReflectionClass($name);

		$name = $this->getName();
		$base = $this->getBase();
		$methods = $this->getMethods();

		$configuration = array(
			'methods' => $methods
		);

		if( ! is_null($base))
		{
			$configuration['base'] = $base;
		}

		$resource = array(
			$name => $configuration
		);

		return $resource;
	}

	public function parseFile($file)
	{
		$content = file_get_contents($file);

		return $this->parse($content, $file);
	}

	protected function parseDocBlock($comment)
	{
		$parser = new DocBlockParser;

		return $parser->parse($comment);
	}

	protected function getName()
	{
		return $this->parseNamespace($this->reflector->getName());
	}

	protected function getBase()
	{
		if( ! $this->reflector->getParentClass())
		{
			return null;
		}

		return $this->parseNamespace($this->reflector->getParentClass()->getName());
	}

	protected function getMethods()
	{
		$methods = array();

		foreach($this->reflector->getMethods() as $method)
		{
			if($this->reflector->getName() != $method->getDeclaringClass()->getName())
			{
				continue;
			}

			$name = $this->parseName($method->getName());
			$docblock = $this->parseDocBlock($method->getDocComment());
			$returnType = $this->getReturnType($docblock);
			$comment = $docblock->getShortDesc();

			$body = $this->getMethodBody($method);

			$methods[$name] = array(
			 	'body' => array(
			 		'php-core' => $body
			 	)
			);

			if( ! is_null($returnType))
			{
				$methods[$name]['returnType'] = $returnType;
			}

			if( ! empty($comment))
			{
				$methods[$name]['comment'] = $comment;
			}
		}

		return $methods;
	}

	public function parseName($value)
	{
		$replace = '$1.$2';

		return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
	}

	protected function getReturnType($docblock)
	{
		$return = $docblock->getTag('return');

		$returnType = isset($return[0][0][0][0]) ? $return[0][0][0][0] : null;

		return $this->parseNamespace($returnType);
	}

	protected function parseNamespace($namespace)
	{
		return str_replace('\\', '.', $namespace);
	}

	protected function getMethodBody($method)
	{
		$line_start = $method->getStartLine() - 1;
		$line_end = $method->getEndLine();
		$line_count = $line_end - $line_start;
		$line_array = file($method->getFileName());

		$code = implode("", array_slice($line_array, $line_start, $line_count));

		preg_match('/{(.*)}/s', $code, $matches);

		return $this->cleanIndentation($matches[1]);
	}

	protected function cleanIndentation($str)
	{
        $content = '';

        foreach(preg_split("/((\r?\n)|(\r\n?))/", trim($str)) as $line) {
            $content .= " " . trim($line) . PHP_EOL;
        }

        return $content;
    }

}
