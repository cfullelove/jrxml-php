<?php

namespace JasperReport;

class DataBag
{
	public $paramDefs;
	public $paramVals;
	public $fieldDefs;
	public $fieldVals;

	function __construct( $paramDefs = array(), $paramVals = array(), $fieldDefs = array(), $fieldVals = array() )
	{
		$this->paramDefs = $paramDefs;
		$this->paramVals = $paramVals;
		$this->fieldVals = $fieldVals;
		$this->fieldDefs = $fieldDefs;
	}

}