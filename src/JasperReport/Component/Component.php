<?php

namespace JasperReport\Component;

use JasperReport\JasperReport;
use JasperReport\Drawable;
use JasperReport\DataBag;

require_once ( __DIR__ . "/../functions.php" );

abstract class Component
{
	public $x = 0;
	public $y = 0;
	public $width = 0;
	public $height = 0;
	public $style = null;
	public $printWhenExpression = '';

	protected $node;
	protected $jasperReport;

	function __construct( JasperReport $report, \DOMNode $node )
	{
		$this->jasperReport = $report;
		$this->name = $node->nodeName;
		$this->node = $node;

		$this->processReportElement();
	}

	function processReportElement()
	{
		$that = $this;

		$this->jasperReport->processSingleElement( 'jr:reportElement', function ( $node ) use ( $that ) {
			$attributes = $that->jasperReport->getAttributes( $node );


			$that->forecolor = isset( $attributes->forecolor ) ? $attributes->forecolor : null;
			$that->backcolor = isset( $attributes->backcolor ) ? $attributes->backcolor : null;
			$that->mode = isset( $attributes->mode ) ? $attributes->mode : null;
			$that->style = isset( $attributes->style ) ? $that->jasperReport->styles[ $attributes->style ] : null;

			$that->x = intval( $node->attributes->getNamedItem( 'x' )->nodeValue );
			$that->y = intval( $node->attributes->getNamedItem( 'y' )->nodeValue );
			$that->width = intval( $node->attributes->getNamedItem( 'width' )->nodeValue );
			$that->height = intval( $node->attributes->getNamedItem( 'height' )->nodeValue );


			$this->jasperReport->processElements( 'jr:printWhenExpression', function ($node ) use ($that ) {
				$that->printWhenExpression = $this->jasperReport->expressionFactory( $node->nodeValue );
			}, $node );
		}, $this->node );
	}

	function getDrawableBase()
	{
		$drawable = new Drawable();

		$drawable->x = $this->x;
		$drawable->y = $this->y;
		$drawable->height = $this->height;
		$drawable->width = $this->width;

		// Cell style
		$drawable->forecolor = $this->forecolor;
		$drawable->backcolor = $this->backcolor;
		$drawable->mode = $this->mode;

		$drawable->style = $this->style;

		// Text style
		$drawable->textStyle->bold = false;
		$drawable->textStyle->italic = false;
		$drawable->textStyle->underline = false;
		$drawable->textStyle->size = 8;

		$drawable->text = '';

		if ( $this->style != null )
			$drawable->updateStyle( $this->style );

		return $drawable;

	}

	function getPrintWhen( DataBag $dataBag )
	{
		$printWhen = ( $this->printWhenExpression == '' ) ? true : $this->printWhenExpression->evaluate( $dataBag );

		if ( $printWhen === false )
			return false;

		return true;
	}

	abstract function eachDrawable( Callable $callback, DataBag $dataBag );

	function evalString()
	{
		return call_user_func_array( 'JasperReport\evalString', func_get_args() );
	}
}