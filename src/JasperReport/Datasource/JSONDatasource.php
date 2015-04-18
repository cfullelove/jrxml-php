<?php

namespace JasperReport\Datasource;

class JSONDatasource extends AbstractDatasource
{
	private $filename;

	function __construct( $filename )
	{
		if ( ! file_exists( $filename ) )
			throw \Exception( "File Not Found: " . $filename );

		$this->filename = $filename;
	}

	function execQuery( $query )
	{
		return json_decode( file_get_contents( $this->filename ) );
	}
	
}