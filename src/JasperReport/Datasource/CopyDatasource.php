<?php

namespace JasperReport\Datasource;

use JasperReport\DataBag;

class CopyDatasource extends AbstractDatasource
{
	private $source;
	private $dir;

	function __construct( DatasourceInterface $source, $dir )
	{
		$this->source = $source;

		$this->dir = $dir;

	}

	function evalQuery( $string, DataBag $dataBag )
	{
		return $this->source->evalQuery( $string, $dataBag );
	}
	
	function execQuery( $query )
	{
		$data = $this->source->execQuery( $query );

		file_put_contents( $this->dir . '/' . md5( $query ) . '.json', json_encode( $data, JSON_PRETTY_PRINT ) );

		return $data;

	}


}