<?php

namespace JasperReport;

require_once ( __DIR__ . "/functions.php" );

class JasperReport
{

	public $name = "default_jasper_report";

	public $attributes = array();

	public $properties = array();

	private $bands = array();

	private $queryString = "";

	private $parameters = array();

	private $fields = array();

	public $styles = array();

	private $subDatasets = array();

	private $dom = null;
	private $root;

	private $dataSource;

	function __construct( $filename )
	{
		$this->dom = new \DomDocument();
		if ( ! $this->dom->load( $filename, LIBXML_NOBLANKS ) )
		{
			throw new \Exception( "Failed to load report file" );
		}

		$this->xpath = new \DOMXPath( $this->dom );
		$this->xpath->registerNamespace( 'jr', "http://jasperreports.sourceforge.net/jasperreports" );
		$this->xpath->registerNamespace( 'jrc', "http://jasperreports.sourceforge.net/jasperreports/components" );

		if ( $this->xpath->query( '/jr:jasperReport' )->length != 1 )
		{
			throw new \Exception( "Invalid report file!" );
		}

		$this->root = &$this->dom->documentElement;

		$this->processReport();
	}

	private function processReport()
	{
		$that = $this;

		// attributes
		$this->processElements( '@*', function( $node ) use ( $that ) {
			$that->attributes[ $node->nodeName ] = $node->nodeValue;
		});

		// Properties
		$this->processElements( "//jr:jasperReport/jr:property", function ( $p ) use ($that) {
			$that->properties[ $p->attributes->getNamedItem( 'name' )->nodeValue ] = $p->attributes->getNamedItem( 'value' )->nodeValue;
		});

		// Parameters
		$this->processElements( "/jr:jasperReport/jr:parameter", function ( $param ) use ($that) {
			$p = new Parameter( $param );
			$that->parameters[ $p->name ] = $p;
		});

		// Query
		$this->processSingleElement( "//jr:jasperReport/jr:queryString",  function ( $node ) use ( $that ) {
			$that->queryString = $node->textContent;
		});

		// Fields
		$this->processElements( "/jr:jasperReport/jr:field", function ( $field ) use ($that) {
			$f = new Field( $field );
			$that->fields[ $f->name ] = $f;
		});

		// Styles
		$this->processElements( "/jr:jasperReport/jr:style", function ( $style ) use ($that) {
			$s = new Style( $that, $style );
			$that->styles[ $s->name ] = $s;
		});

		// Sub Datasets
		$this->processElements( "/jr:jasperReport/jr:subDataset", function ( $sds ) use ($that) {
			$s = new SubDataset( $that, $sds );
			$that->subDatasets[ $s->name ] = $s;
		});

		$this->processElements( "/jr:jasperReport/*/jr:band/..", function ($node) use ($that) {
			$that->bands[ $node->nodeName ] = new Band( $that, $node->nodeName, $node );
			$that->bands[ $node->nodeName ]->processBand();
		});


	}

	function processElements( $path, Callable $callback, $context = null )
	{
		if ( $context == null )
			$content = $this->root;

		foreach( $this->xpath->query( $path, $context, false ) as $p )
		{
			call_user_func( $callback, $p );
		}
	}

	function processSingleElement( $path, Callable $callback, $context = null )
	{
		if ( $context == null )
			$content = $this->root;

		$r = $this->xpath->query( $path, $context, false );

		if ( $r->length == 0 )
			throw new \Exception( "Attempting to fetch a element where none exists : " . $path );

		call_user_func( $callback, $r->item(0) );

	}

	function getAttributes( $node )
	{
		$attr = new \stdclass();
		$this->processElements( '@*', function ( $node ) use ( $attr ) {
			$a = $node->nodeName;
			$attr->$a = $node->nodeValue;
		}, $node );

		return $attr;
	}

	function getDatasource()
	{
		return $this->dataSource;
	}

	function renderReport( OutputAdapterInterface $outputAdapter, DatasourceInterface $datasource, $params )
	{
		$this->outputAdapter = $outputAdapter;
		$this->datasource = $datasource;

		$this->outputAdapter->pageSetup();

		$cursor = array( 'x' => 0, 'y' => 0 );

		
		// Execute main query
		$query = evalQuery( $this->queryString, new DataBag( $this->parameters, $params ) );
		$rows = $datasource->execQuery( $query );


		// page one
		$cursor['x'] += $this->attributes['leftMargin'];
		$cursor['y'] += $this->attributes['topMargin'];
		
		// Title
		$this->renderBand( 'title', $cursor, new DataBag( $this->parameters, $params, $this->fields, $rows[0] ) );
		

		// pageHeader
		$this->renderBand( 'pageHeader', $cursor, new DataBag( $this->parameters, $params, $this->fields, $rows[0] ) );

		// columnHeader
		$this->renderBand( 'columnHeader', $cursor, new DataBag( $this->parameters, $params, $this->fields, $rows[0] ) );
		

		// Detail

		if ( isset( $this->bands['detail'] ) && $this->bands['detail']->height > 0 )
		{
			foreach( $rows as $row )
			{
				$this->renderBand( 'detail', $cursor, new DataBag( $this->parameters, $params, $this->fields, $row ) );
			}
		}

		// columnFooter
		$this->renderBand( 'columnFooter', $cursor, new DataBag( $this->parameters, $params, $this->fields, $rows[0] ) );

		// pageFooter
		$this->renderBand( 'pageFooter', $cursor, new DataBag( $this->parameters, $params, $this->fields, $rows[0] ) );

		return $this->outputAdapter;

	}


	function renderBand( $band, & $cursor, DataBag $dataBag, $checkNextPage = true )
	{
		if ( isset( $this->bands[ $band ] ) && $this->bands[ $band ]->height > 0 )
		{
			if ( $this->checkNextPage( $cursor, $band ) && $checkNextPage )
			{
				$this->doNextPage( $cursor, $dataBag );
			}

			foreach( $this->bands[ $band]->getElements() as $element )
			{
				$this->draw( $this, $element, $cursor, $dataBag );
			}

			$cursor['x'] = $this->attributes['leftMargin'];
			$cursor['y'] += $this->bands[ $band ]->height;
		}
	}

	function checkNextPage( $cursor, $band )
	{
		// current position
		$nextBottom = $cursor['y'];

		// pageMargin
		$nextBottom += $this->attributes['bottomMargin' ];

		// pageFooter
		$nextBottom += (isset( $this->bands['pageFooter']->height ) ? $this->bands['pageFooter']->height : 0);

		// columnFooter
		$nextBottom += (isset( $this->bands['columnFooter']->height ) ? $this->bands['columnFooter']->height : 0);
		
		// Current Band
		$nextBottom += $this->bands[ $band ]->height;

		return ( $nextBottom > $this->attributes['pageHeight'] );
	}

	function doNextPage( & $cursor, DataBag $dataBag )
	{
		if ( isset( $this->bands['columnFooter'] ) )
			$this->renderBand( 'columnFooter', $cursor, $dataBag, false );

		if ( isset( $this->bands['pageFooter'] ) )
			$this->renderBand( 'pageFooter', $cursor, $dataBag, false );

		$this->outputAdapter->nextPage();

		$cursor['x'] = $this->attributes['leftMargin'];
		$cursor['y'] = $this->attributes['topMargin'];

		$this->renderBand( 'pageHeader', $cursor, $dataBag );
	}


	function draw( JasperReport $report, Component\Component $element, $cursor, DataBag $dataBag )
	{
		$element->eachDrawable( function( $e ) use ( $report, $cursor ) {
			$e->x += $cursor['x'];
			$e->y += $cursor['y'];
			$this->outputAdapter->draw( $e );
		}, $dataBag );
	}

	function subDataset( $id )
	{
		if ( isset( $this->subDatasets[ $id ] ) )
			return $this->subDatasets[ $id ];
		else
			return false;
	}

}

?>