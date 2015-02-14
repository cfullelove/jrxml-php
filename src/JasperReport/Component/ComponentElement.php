<?php

namespace JasperReport\Component;

use JasperReport\JasperReport;
use JasperReport\DatasetRun;
use JasperReport\Databag;
use JasperReport\Drawable;

class ComponentElement extends Component
{

	private $datasetRun;

	function __construct( JasperReport $report, \DOMNode $node )
	{
		parent::__construct( $report, $node );

		$that = $this;

		// Table
		$this->jasperReport->processElements( "jrc:table", function ( $node ) use ( $that ) {
			$this->jasperReport->processElements( "jr:datasetRun", function ( $node ) use ( $that ) {
				$that->datasetRun = new DatasetRun( $that->jasperReport, $node );
			}, $node );				

			$that->table = new Table( $that->jasperReport, $node );

		}, $node );

	}

	function eachDrawable( Callable $callback, DataBag $mainDataBag )
	{

		if ( ! $this->getPrintWhen( $mainDataBag ) )
			return;

		$that = $this;

		// Do Query

		$subDataset = $this->jasperReport->subDataset( $this->datasetRun->subDatasetName );

		$params = array();

		foreach ( $subDataset->getParameters() as $paramDef )
		{
			$params[ $paramDef->name ] = \JasperReport\evalString(
				$this->datasetRun->getParameterExpression( $paramDef->name ),
				$mainDataBag );
		}

		$query = \JasperReport\evalQuery(
			$subDataset->getQueryString(),
			new Databag( $subDataset->getParameters(), $params )
		);

		$rows = $this->jasperReport->datasource->execQuery( $query );


		$cursor = array(
			'x' => $this->x,
			'y' => $this->y
		);

		$dataBag = new DataBag( $subDataset->getParameters(), $params, $subDataset->getFields(), count( $rows ) > 0 ? $rows[0] : array() );

		// Table Header
		if ( count( $this->table->getHeader()->children ) > 0 )
		{

			$i = 0;
			foreach( $this->table->getHeader()->children as $column )
			{

				foreach ( $column as $el )
				{
					$el->eachDrawable( function( $drawable ) use ( & $cursor, $callback, $that ) {
						$drawable->x += $cursor['x'];
						$drawable->y += $cursor['y'];
						call_user_func( $callback, $drawable );
					}, $dataBag );
				}

				if ( isset( $this->table->getHeader()->attributes[$i]->style ) );
				{
					$drawable = new Drawable();
					$drawable->updateStyle( $this->jasperReport->styles[ $this->table->getHeader()->attributes[$i]->style ] );	
					$drawable->x = $cursor['x'];
					$drawable->y = $cursor['y'];
					$drawable->height = $column[0]->height; // HACK!!
					$drawable->width = $this->table->getColumns()[$i];
					call_user_func( $callback, $drawable );
				}

				$cursor['x'] += $this->table->getColumns()[$i];
				$i++;
			}

			// start next line
			$cursor['y'] += $this->table->getHeader()->attributes[0]->height;
			$cursor['x'] = $this->x;
		}

		// Table Detail
		if ( count( $this->table->getDetail()->children ) > 0 )
		{
			foreach ( $rows as $row )
			{
				$dataBag = new DataBag( $subDataset->getParameters(), $params, $subDataset->getFields(), $row );

				$i = 0;
				foreach( $this->table->getDetail()->children as $column )
				{

					foreach ( $column as $el )
					{
						$el->eachDrawable( function( $drawable ) use ( & $cursor, $callback ) {
							$drawable->x += $cursor['x'];
							$drawable->y += $cursor['y'];
							call_user_func( $callback, $drawable );
						}, $dataBag );
					}

					if ( isset( $this->table->getDetail()->attributes[$i]->style ) );
					{
						$drawable = new Drawable();
						$drawable->updateStyle( $this->jasperReport->styles[ $this->table->getDetail()->attributes[$i]->style ] );
						$drawable->x = $cursor['x'];
						$drawable->y = $cursor['y'];
						$drawable->height = $column[0]->height; // HACK!!
						$drawable->width = $this->table->getColumns()[$i];
						call_user_func( $callback, $drawable );
					}

					$cursor['x'] += $this->table->getColumns()[$i];
					$i++;
				}

				$cursor['y'] += $this->table->getDetail()->attributes[0]->height;
				$cursor['x'] = $this->x;
			}

		}

		call_user_func( $callback, $this->getDrawableBase() );


	}

}