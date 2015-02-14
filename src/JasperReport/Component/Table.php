<?php

namespace JasperReport\Component;

use JasperReport\JasperReport;
use JasperReport\DatasetRun;

class Table
{

	private $jasperReport;
	private $node;

	private $columns;
	private $tableHeader;
	private $detailCell;
	private $tableFooter;


	function __construct( JasperReport $report, \DOMNode $node )
	{
		$this->jasperReport = $report;
		$this->node = $node;

		$that = $this;

		$this->tableHeader = new \stdClass();
		$that->tableHeader->children = array();
		$that->tableHeader->attributes = array();

		$this->detailCell = new \stdClass();
		$that->detailCell->children = array();
		$that->detailCell->attributes = array();
		
		$this->tableFooter = new \stdClass();
		$that->tableFooter->children = array();
		$that->tableFooter->attributes = array();

		$this->columns = array();

		$col = 0;
		$this->jasperReport->processElements( 'jrc:column', function( $node ) use ( $that, & $col ) {
			$that->columns[$col] = $node->attributes->getNamedItem( 'width' )->nodeValue;

			$that->jasperReport->processElements( 'jrc:tableHeader', function ($node) use ( $that, $col ) {
				$that->tableHeader->attributes[$col] = $that->jasperReport->getAttributes( $node );
				$that->tableHeader->children[$col] = $that->processChildren( $node );
			}, $node );

			$that->jasperReport->processElements( 'jrc:detailCell', function ($node) use ( $that, $col ) {
				$that->detailCell->attributes[$col] = $that->jasperReport->getAttributes( $node );
				$that->detailCell->children[$col] = $that->processChildren( $node );
			}, $node );

			$that->jasperReport->processElements( 'jrc:tableFooter', function ($node) use ( $that, $col ) {
				$that->tableFooter->attributes[$col] = $that->jasperReport->getAttributes( $node );
				$that->tableFooter->children[$col] = $that->processChildren( $node );
			}, $node );

			$col++;
		}, $node );

	}

	function processChildren( $node )
	{
		$collection = array();
		$that = $this;

		$that->jasperReport->processElements( 'jr:textField', function ($node) use ($that,  & $collection ) {
			$collection[] = new TextField( $that->jasperReport, $node );
		}, $node );

		$that->jasperReport->processElements( 'jr:staticText', function ($node) use ($that, & $collection ) {
			$collection[] = new staticText( $that->jasperReport, $node );
		}, $node );

		
		return $collection;
	}

	function getHeader()
	{
		return $this->tableHeader;
	}

	function getDetail()
	{
		return $this->detailCell;
	}

	function getFooter()
	{
		return $this->tableFooter;
	}

	function getColumns()
	{
		return $this->columns;
	}
}
