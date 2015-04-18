<?php

namespace JasperReport\Datasource;

use JasperReport\Databag;

interface DatasourceInterface
{

	function evalQuery( $string, DataBag $dataBag );
	
	function execQuery( $query );

}