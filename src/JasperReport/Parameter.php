<?php

namespace JasperReport;

class Parameter
{
	public $name;
	public $class;
	public $default;
	public $value;

	function __construct( \DOMNode $node )
	{
		$this->name = $node->attributes->getNamedItem( 'name' )->nodeValue;
		$this->class = $node->attributes->getNamedItem( 'class' )->nodeValue;
		
		$this->default = isset( $node->firstChild ) ? $node->firstChild->nodeValue : null ;
	}
}