<?php

namespace JasperReport\OutputAdapter;

interface OutputAdapterInterface
{

	function pageSetup();

	function draw( $drawable );
	
}