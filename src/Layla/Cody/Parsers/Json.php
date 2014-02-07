<?php namespace Layla\Cody\Parsers;

class Json extends Parser {

	protected $format = 'json';

	public function parse($content)
	{
		return json_decode($content);
	}

}
