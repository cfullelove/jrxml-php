<?php

namespace JasperReport\Datasource;

use JasperReport\DataBag;

class FromCopyDatasource extends AbstractDatasource
{

	private $dir;

	function __construct( $dir )
	{
		$this->dir = $dir;

	}


	function execQuery( $query )
	{
		$data = json_decode( file_get_contents( $this->dir . '/' . md5( $query ) . '.json' ) );

		return $data;

	}


}