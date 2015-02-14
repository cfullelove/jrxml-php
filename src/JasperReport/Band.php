<?php

namespace JasperReport;

class Band
{

	protected $jasperReport;
	protected $node;

	protected $name;

	public $height = 0;
	public $splitType = "Stretch";

	private $elements = array();


	function __construct( JasperReport $report, $name, $node )
	{
		$this->jasperReport = $report;
		$this->name = $name;
		$this->node = $node;
	}

	function processBand()
	{
		$that = $this;

		// Band properties
		$this->jasperReport->processSingleElement( "jr:band", function ( $node ) use ($that) {
			$that->height = $node->attributes->getNamedItem( "height" )->nodeValue;
			//$that->splitType = $node->attributes->getNamedItem( "splitType" )->nodeValue;
		}, $this->node );

		// Elements

		$this->jasperReport->processElements( "jr:band/*/jr:reportElement/..", function ( $node ) use ($that) {

			$el = null;
			switch ( $node->nodeName )
			{
				case 'textField':
					$el = new Component\TextField( $that->jasperReport, $node );
					break;
				case 'staticText':
					$el = new Component\StaticText( $that->jasperReport, $node );
					break;
				case 'componentElement':
					$el = new Component\ComponentElement( $that->jasperReport, $node );
					break;
				case 'rectangle':
					$el = new Component\Rectangle( $that->jasperReport, $node );
					break;

			}

			if ( $el != null )
				$that->elements[] = $el;

		}, $this->node );

	}

	function getElements()
	{
		return $this->elements;
	}

}

?>