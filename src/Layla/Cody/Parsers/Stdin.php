<?php namespace Layla\Cody\Parsers;

class Stdin extends Parser {

	protected $format = 'stdin';

	public function parse()
	{
		return trim(fgets(STDIN));
	}

}
