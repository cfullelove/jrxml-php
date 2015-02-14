<?php

namespace JasperReport;

class SubDataset
{
	public $name;

	private $queryString = "";

	private $fields = array();

	private $parameters = array();

	private $jasperReport;
	private $node;

	function __construct( JasperReport $report, \DomNode $node )
	{
		$this->node = $node;

		$this->jasperReport = $report;

		$this->name = $node->attributes->getNamedItem( 'name' )->nodeValue;

		$this->process();
	}

	function process()
	{
		$that = $this;

		// Query String
		$this->jasperReport->processSingleElement( "jr:queryString",  function ( $node ) use ( $that ) {
			$that->queryString = $node->textContent;
		}, $this->node );

		// Fields
		$this->jasperReport->processElements( "jr:field", function ( $field ) use ($that) {
			$f = new Field( $field );
			$that->fields[ $f->name ] = $f;
		}, $this->node );

		// Parameters
		$this->jasperReport->processElements( "jr:parameter", function ( $param ) use ($that) {
			$p = new Parameter( $param );
			$that->parameters[ $p->name ] = $p;
		}, $this->node );

	}

	function getQueryString()
	{
		return $this->queryString;
	}

	function getParameters()
	{
		return $this->parameters;
	}

	function getFields()
	{
		return $this->fields;
	}
}