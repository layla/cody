<?php namespace Layla\Cody\Parsers;

class Json extends Parser {

	protected $extension = 'json';

	public function parse($content)
	{
		return json_decode($content);
	}

}
