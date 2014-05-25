<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/*
* File: CaptchaSecurityImages.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 03/08/06
* Updated: 07/02/07
* Requirements: PHP 4/5 with GD and FreeType libraries
* Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
* Modified and extended to work with Community Builder by
* Beat and Nant - Feb. 2007
*/
class CaptchaSecurityImages {

	var $font = null;
    var $br = 255;
    var $bg = 255;
    var $bb = 255;
	var $tr = 20;
	var $tg = 40;
	var $tb = 100;
	var $nr = 100;
	var $ng = 120;
	var $nb = 180;
	    
	function CaptchaSecurityImages( ) {
		$this->font = dirname(__FILE__).'/fonts/monofont.ttf';
	}
	
	function setFont($font2use) {
		
		switch ($font2use) {
			case 0:
				$this->font = dirname(__FILE__).'/fonts/monofont.ttf';
				break;
			case 1:
				$this->font = dirname(__FILE__).'/fonts/EARWIGFA.ttf';
				break;
			case 2:
				$this->font = dirname(__FILE__).'/fonts/stilltim.ttf';
				break;
			case 3:
				$this->font = dirname(__FILE__).'/fonts/sayso chic.ttf';
				break;
			case 4:
				$this->font = dirname(__FILE__).'/fonts/CUOMOTYP.ttf';
				break;
			case 5:
				$this->font = dirname(__FILE__).'/fonts/PRIMER.ttf';
				break;
			case 6:
				$this->font = dirname(__FILE__).'/fonts/PRIMERB.ttf';
				break;
			case 7:
				$this->font = dirname(__FILE__).'/fonts/INTERPLA.ttf';
				break;
			default:
				$this->font = dirname(__FILE__).'/fonts/monofont.ttf';
				break;
		}				
	}

	function setrgb($value,$composite) {
		if ( ($value > 255) || ($value < 0 ) ) return;
		switch ($composite) {
			case 'br':
				$this->br = (int) $value;
				break;
			case 'bg':
				$this->bg = (int) $value;
				break;
			case 'bb':
				$this->bb = (int) $value;
				break;
			case 'tr':
				$this->tr = (int) $value;
				break;
			case 'tg':
				$this->tg = (int) $value;
				break;
			case 'tb':
				$this->tb = (int) $value;
				break;
			case 'nr':
				$this->nr = (int) $value;
				break;
			case 'ng':
				$this->ng = (int) $value;
				break;
			case 'nb':
				$this->nb = (int) $value;
				break;	
		}	
	}

	function _generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	function generate($width='150',$height='40',$characters='6') {
		$code = $this->_generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, $this->br, $this->bg, $this->bb);
		$text_color = imagecolorallocate($image, $this->tr, $this->tg, $this->tb);
		$noise_color = imagecolorallocate($image, $this->nr, $this->ng, $this->nb);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		return $code;
	}

}

?>