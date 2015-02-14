<?php

namespace JasperReport;

class Drawable
{

	public $x = 0;
	public $y = 0;
	public $height = 0;
	public $width = 0;

	public $forecolor = null;
	public $backcolor = null;
	public $mode = null;

	public $textStyle;
	public $cellStyle;

	public $textAlign = 'Left';
	public $verticalAlign = 'Middle';

	public $text = "";

	function __construct()
	{
		$this->textStyle = new \stdclass();
		$this->textStyle->bold = false;
		$this->textStyle->italic = false;
		$this->textStyle->underline = false;
		$this->textStyle->size = 10;

		$this->cellStyle = new \stdclass();
		$this->cellStyle->lineWidth = 0;
		$this->cellStyle->lineColor = '';
		$this->cellStyle->leftPadding = 0;
		$this->cellStyle->rightPadding = 0;
		$this->cellStyle->topPadding = 0;
		$this->cellStyle->bottomPadding = 0;

		return $this;
	}

	function updateStyle( Style $style )
	{
		$this->mode = $style->mode;
		$this->forecolor = $style->forecolor;
		$this->backcolor = $this->backcolor;
		$this->cellStyle->lineWidth = $style->lineWidth;
		$this->cellStyle->lineColor = $style->lineColor;
		$this->cellStyle->leftPadding = $style->leftPadding;
		$this->cellStyle->rightPadding = $style->rightPadding;
		$this->cellStyle->topPadding = $style->topPadding;
		$this->cellStyle->bottomPadding = $style->bottomPadding;

		return $this;
	}

}