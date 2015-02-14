<?php

namespace JasperReport;

class Field
{
	public $name;
	public $class;
	public $value = null;

	function __construct( \DOMNode $node )
	{
		$this->name = $node->attributes->getNamedItem( 'name' )->nodeValue;
		$this->class = $node->attributes->getNamedItem( 'class' )->nodeValue;
	}
}