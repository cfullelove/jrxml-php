<?php

namespace JasperReport\OutputAdapter;

use fpdf\FPDF;

class FPDFOutputAdapter implements OutputAdapterInterface
{

	private $pdf;

	function __construct( $orientation = "Portrait", $units = "pt", $size = "A4" )
	{
		$this->pdf = new FPDF( $orientation, $units, $size );
	}

	function pageSetup()
	{
		$this->pdf->SetMargins( 0, 0, 0, 0 );
		$this->pdf->SetFont('Arial', 'B', 12 );
		$this->pdf->SetAutoPageBreak( false );
		$this->pdf->AddPage();
	}

	function draw( $drawable )
	{
		$this->pdf->SetXY( $drawable->x, $drawable->y );

		$textStyle = '';
		$textStyle .= ( $drawable->textStyle->bold ) ? 'B' : '';
		$textStyle .= ( $drawable->textStyle->italic ) ? 'I' : '';
		$textStyle .= ( $drawable->textStyle->underline ) ? 'U' : '';

		//var_dump( array( $drawable->text, $textStyle ) );
		$this->pdf->SetFont( 'Arial', $textStyle, $drawable->textStyle->size );

		if ( ! isset( $drawable->textAlign ) )
			$drawable->textAlign = '';			

		switch( $drawable->textAlign )
		{
			case 'Center':
				$align = 'C';
				break;
			case 'Left':
				$align = 'L';
				break;
			case 'Right':
				$align = 'R';
				break;
			default:
				$align = 'L';
				break;
		}

		if ( ! isset( $drawable->verticalAlign ) )
			$drawable->verticalAlign = '';			


		if ( $drawable->forecolor != null )
		{
			$forecolor = $this->hex2RGB( $drawable->forecolor );
			$this->pdf->SetTextColor( $forecolor['red'], $forecolor['green'], $forecolor['blue'] );
		}
		else
		{
			// default
			$this->pdf->SetTextColor( 0 );
		}

		if ( $drawable->backcolor != null )
		{
			$backcolor = $this->hex2RGB( $drawable->backcolor );
			$this->pdf->SetFillColor( $backcolor['red'], $backcolor['green'], $backcolor['blue'] );			
		}


		// Deal with background colors, borders, etc
		$borders = '';
		if ( $drawable->cellStyle->lineWidth > 0 )
		{
			$borders = 'TRLB';
			$this->pdf->SetLineWidth( $drawable->cellStyle->lineWidth );
		}
			

		$this->pdf->Cell( $drawable->width, $drawable->height, '', $borders, null, null, $drawable->mode == 'Opaque' );


		switch( $drawable->verticalAlign )
		{
			case 'Middle':
				$lines = $this->getStringLines($drawable->text, $drawable->width);
				$cellHeight = $this->pdf->FontSize * $lines;
				$yOffset = ($drawable->height - $cellHeight) / 2;
				$this->pdf->SetXY( $drawable->x, $drawable->y + $yOffset );
				$this->pdf->MultiCell( $drawable->width, $this->pdf->FontSize, $drawable->text, null, $align );
				break;
			case 'Bottom':
				$lines = $this->getStringLines($drawable->text, $drawable->width);
				$cellHeight = $this->pdf->FontSize * $lines;
				error_log(sprintf("CellHeight for \"%s\" is %s", $drawable->text, $cellHeight));
				$yOffset = $drawable->height - ($cellHeight + $this->pdf->cMargin);
				$this->pdf->SetXY( $drawable->x, $drawable->y + $yOffset );
				$this->pdf->MultiCell( $drawable->width, $this->pdf->FontSize, $drawable->text, null, $align );
				break;
			case 'Top':
				$this->pdf->SetXY( $drawable->x, $drawable->y + $this->pdf->cMargin );
				$this->pdf->MultiCell( $drawable->width, $this->pdf->FontSize, $drawable->text, null, $align );
				break;
		}		

		

		// $this->pdf->SetXY( $drawable->x, $drawable->y );
		// $this->pdf->MultiCell( $drawable->width, $this->pdf->FontSize, $drawable->text, null, $align );
		// $this->pdf->Cell( $drawable->width, $drawable->height, $drawable->text, null, null, $align );

	}

	// Calculate number of lines for text that wraps within a MultiCell with provided width.
	// Takes into account Cell Margin
	function getStringLines( $str, $width ) {

		$width = $width - 2*$this->pdf->cMargin;

		$n = 0;
		
		$lines = explode("\n", $str);

		foreach($lines as $line) {
			$w = 0;
			
			foreach(explode(" ", $line) as $word) {
				$wordLength = $this->pdf->GetStringWidth($word);
				if ($w + $wordLength > $width) {
					$n++;
					$w = $wordLength;
				}
				else {
					$w = $w + $wordLength + $this->pdf->GetStringWidth(" ");
				}
				
			}
			$n++;
		}

		return $n;
	}

	function hex2RGB( $hexStr ) {

		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string

		$rgbArray = array();

		if (strlen($hexStr) == 6) //If a proper hex code, convert using bitwise operation. No overhead... faster
		{ 
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		}
		elseif (strlen($hexStr) == 3) //if shorthand notation, need some string manipulations
		{ 
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		}
		else
		{
			return false; //Invalid hex color code
		}
		return $rgbArray; // returns the rgb string or the associative array
	}

	function nextPage()
	{
		$this->pdf->AddPage();
	}

	function output( $filename )
	{
		return $this->pdf->Output( '', 'S' );
	}

}

?>