<?php

namespace JasperReport\Component;

use JasperReport\JasperReport;

class StaticText extends Component
{

	private $expression;

	function __construct( JasperReport $report, \DOMNode $node )
	{
		parent::__construct( $report, $node );

		$that = $this;

		// Text Element
		$this->jasperReport->processSingleElement( "jr:textElement", function ( $node ) use ( $that ) {
			$that->textElement = $that->jasperReport->getAttributes( $node );

			// Font
			$this->jasperReport->processElements( "jr:font", function ( $node ) use ( $that ) {
				$that->font = $that->jasperReport->getAttributes( $node );
			}, $node );

		}, $this->node );

		// Text field expression
		$this->jasperReport->processSingleElement( "jr:text", function ( $node ) use ( $that ) {
			$that->text = $node->textContent;
		}, $this->node );

	}

	function eachDrawable( Callable $callback, \JasperReport\DataBag $dataBag )
	{
		if ( ! $this->getPrintWhen( $dataBag ) )
			return;

		$drawable = $this->getDrawableBase();

		if ( isset( $this->style ) )
		{
			$drawable->updateStyle( $this->style );
		}


		// text styles
		if ( isset( $this->font->isBold ) )
			$drawable->textStyle->bold = ( $this->font->isBold == 'true' ) ? true : false;

		if ( isset( $this->font->size ) )
			$drawable->textStyle->size = intval( $this->font->size );

		if ( isset( $this->textElement->textAlignment ) )
			$drawable->textAlign =  $this->textElement->textAlignment;
		
		if ( isset( $this->textElement->verticalAlignment ) )
			$drawable->verticalAlign = $this->textElement->verticalAlignment;


		$drawable->text = $this->text;

		call_user_func( $callback, $drawable );
	}

}