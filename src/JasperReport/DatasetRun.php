<?php

namespace JasperReport;


class DatasetRun
{

	private $jasperReport;
	private $node;

	private $parameters = array();
	public $subDatasetName;


	function __construct( JasperReport $report, \DOMNode $node )
	{
		$this->jasperReport = $report;
		$this->node = $node;

		$that = $this;

		$this->subDatasetName = $node->attributes->getNamedItem( 'subDataset' )->nodeValue;

		// Get Dataset Elements
		$this->jasperReport->processElements( "jr:datasetParameter", function ( $node ) use ( $that ) {

			$name = $node->attributes->getNamedItem( 'name' )->nodeValue;

			$that->jasperReport->processSingleElement(
				"jr:datasetParameterExpression",
				function ( $node ) use ( $that, $name ) {
					$that->parameters[ $name ] = $that->jasperReport->expressionFactory( $node->nodeValue );
				},
				$node
			);

		}, $node);

	}

	function getParameterExpression( $name )
	{
		return $this->parameters[ $name ];
	}
}