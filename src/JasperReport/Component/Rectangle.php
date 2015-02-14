<?php

namespace JasperReport\Component;

use JasperReport\DataBag;

class Rectangle extends Component
{
	function eachDrawable( Callable $callback, DataBag $dataBag )
	{
		if ( ! $this->getPrintWhen( $dataBag ) )
			return;

		$drawable = $this->getDrawableBase();
		$drawable->cellStyle->lineWidth = 1;

		if ( isset( $this->style ) )
		{
			$drawable->updateStyle( $this->style );
		}


		call_user_func( $callback, $drawable );
	}

}