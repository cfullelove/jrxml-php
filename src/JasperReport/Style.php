<?php

namespace JasperReport;

class Style
{

	public $name;
	private $node;

	public $mode = '';
	public $backcolor = '';
	public $forecolor= '';
	public $lineWidth = 0;
	public $lineColor = '';
	public $leftPadding = 0;
	public $rightPadding = 0;
	public $topPadding = 0;
	public $bottomPadding = 0;

	private $jasperReport;

	function __construct( JasperReport $report, \DOMNode $node )
	{
		$this->jasperReport = $report;
		$this->node = $node;
		$this->name = $node->attributes->getNamedItem( 'name' )->nodeValue;


		$this->attributes = $this->jasperReport->getAttributes( $node );

		if ( isset( $this->attrubutes->mode ) )
			$this->mode = $this->attrubutes->mode;

		$that = $this;

		$this->jasperReport->processElements( "jr:box", function ( $node ) use ( $that ) {
			$attributes = $that->jasperReport->getAttributes( $node );

			if ( isset( $attributes->mode ) )
				$that->mode = $attributes->mode;

			if ( isset( $attributes->backcolor ) )
				$that->backcolor = $attributes->backcolor;

			if ( isset( $attributes->forecolor ) )
				$that->forecolor = $attributes->forecolor;

			$that->jasperReport->processElements( "jr:pen", function ( $node ) use ( $that ) {
				$attributes = $that->jasperReport->getAttributes( $node );

				if ( isset( $attributes->lineWidth ) )
					$that->lineWidth = $attributes->lineWidth;

				if ( isset( $attributes->lineColor ) )
					$that->lineColor = $attributes->lineColor;

			}, $node );

		}, $this->node );
		
	}
}