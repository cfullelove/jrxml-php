<?php

namespace JasperReport\Datasource;

class MysqlDatasource extends AbstractDatasource
{

	private $db;

	function __construct( $host, $username, $password, $database, $port )
	{

		$this->db = new \mysqli(
			$host,
			$username,
			$password,
			$database,
			$port
        );

	}

	function nextPage()
	{
		return;
	}

	function execQuery( $query )
	{
		$r = $this->db->query( $query );

		if ( $r === false )
			throw new \Exception( "Error with query" );

		$rows = array();
		while ( $row = $r->fetch_object() )
		{
			$rows[ count( $rows )] = $row;
		}

		return $rows;
	}
	
}