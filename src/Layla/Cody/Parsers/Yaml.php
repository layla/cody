<?php namespace Layla\Cody\Parsers;

use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml extends Parser {

	protected $format = 'yml';

	protected function parse($content)
	{
		return YamlParser::parse($content);
	}

}
