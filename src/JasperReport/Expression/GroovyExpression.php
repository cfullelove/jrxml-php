<?php

namespace JasperReport\Expression;

use JasperReport\Databag;

class GroovyExpression
{

	private $expressionString;

	function __construct( $expressionString )
	{
		$this->expressionString = $expressionString;
	}

	function parse()
	{
		return $this->expressionString;
	}

	function evaluate( Databag $dataBag )
	{

		$string = preg_replace( '/\+/', '.', $this->expressionString );
		
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


		return eval( $exec );

	}
	
}