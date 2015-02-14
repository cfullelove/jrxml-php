<?php

namespace JasperReport;

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

function evalString( $string, DataBag $dataBag )
{

	$string = preg_replace( '/\+/', '.', $string );
	
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

	foreach( $dataBag->fieldDefs as $key => $def )
	{
		if ( $def->class == 'java.lang.Integer' )
		{
			$value = $dataBag->fieldVals->$key;
		}
		else
		{
			$value = sprintf( "'%s'", addslashes( $dataBag->fieldVals->$key ) );
		}
		
		$string = str_replace( sprintf( '$F{%s}', $key ), $value, $string );
	}

	$exec = sprintf( 'return %s;', $string );

	//echo $exec . PHP_EOL;

	return eval( $exec );

}

function evalCondition( $string, DataBag $dataBag )
{
	return; 
}