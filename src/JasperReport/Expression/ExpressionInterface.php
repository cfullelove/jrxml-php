<?php

namespace JasperReport\Expression;

use JasperReport\Databag;

interface ExpressionInterface
{

	function parse();

	function evaluate( Databag $dataBag );
	
}