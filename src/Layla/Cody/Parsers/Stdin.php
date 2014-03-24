<?php namespace Layla\Cody\Parsers;

class Stdin extends Parser {

	protected $extension = 'stdin';

	public function parse()
	{
		return trim(fgets(STDIN));
	}

}
