<?php

namespace JasperReport\Datasource;

use JasperReport\Databag;

abstract class AbstractDatasource implements DatasourceInterface
{
	
	function evalQuery( $string, DataBag $dataBag )
	{

		foreach( $dataBag->paramDefs as $key => $def )
		{
			if ( $def->class = 'java.lang.Integer' )
			{
				$value = isset( $dataBag->paramVals[$key] ) ? $dataBag->paramVals[$key] : $def->default;
			}
			else
			{
				$value = sprintf( "'%s'", addslashes( isset( $dataBag->paramVals[$key] ) ? $dataBag->paramVals[$key] : $def->default ) );
			}
			
			$string = str_replace( sprintf( '$P{%s}', $key ), $value, $string );

		}

		return $string;
	}

	abstract function execQuery( $query );

}