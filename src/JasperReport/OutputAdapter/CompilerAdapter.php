<?php

namespace JasperReport\OutputAdapter;

class CompilerAdapter implements OutputAdapterInterface
{

	private $drawables = array();

	function __construct()
	{

	}

	function pageSetup()
	{

	}

	function nextPage()
	{
		return;
	}

	function draw( $drawable )
	{
		$this->drawables[] = $drawable;
	}

	function output()
	{
		return json_encode( $this->drawables, JSON_PRETTY_PRINT );
	}

}