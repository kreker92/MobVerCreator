<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelAdsmanager extends TModel
{  
    function getArea($max_width,
                     $max_height,
                     $max_width_t,
                     $max_height_t)
	{
	
		$original_aspect = $max_width / $max_height;
		$thumb_aspect = $max_width_t / $max_height_t;
		
		if ( $original_aspect >= $thumb_aspect )
		{
		   // If image is wider than thumbnail (in aspect ratio sense)
		   $new_height = $max_height;
		   $new_width = ($max_width_t / $max_height_t) * $new_height;
		   $y= 0;
 		   $x = (int) ($max_width-$new_width) /2;
		}
		else
		{
		   // If the thumbnail is wider than the image
		   $new_width = $max_width;
		   $new_height = ($max_height_t / $max_width_t) * $new_width;
		   $x = 0;
		   $y = (int) ($max_height-$new_height) /2;
		}
	
		return array($x, $y, $new_width, $new_height);
	}
    
	function changeState($table,$id,$field,$state,$cid)
	{
		$cids = implode( ',', $cid );
		$this->_db->setQuery("UPDATE $table SET $field = $state WHERE $id IN ($cids)");
		//echo "UPDATE $table SET $field = $state WHERE $id IN ($cids)";
		//exit();
		$this->_db->query();
	}
	
	function createimage($writefunction,$img,$destFile, $quality)
	{
		ob_start();
		$writefunction($img, null, $quality);
		$output = ob_get_contents();
		ob_end_clean();
		JFile::write($destFile, $output);
	}
	
	function createImageAndThumb($src_file,$image_name,$thumb_name,
								$max_width,
							    $max_height,
								$max_width_t,
								$max_height_t,
								$tag,
								$path,
								$orig_name,
								$max_width_m=null,
								$max_height_m=null,
								$medium_name=null)
	{
		if (intval(ini_get('memory_limit')) < 64)
			ini_set('memory_limit', '64M');
		
        $configModel = new AdsmanagerModelConfiguration();
        $conf = $configModel->getConfiguration();
        
		$src_file = urldecode($src_file);
		
		$orig_name = strtolower($orig_name);

		$ext = strtolower(JFile::getExt($orig_name));
		switch($ext) {
			case "jpg":
			case "jpeg":	
				$type = "jpeg";break;
			case "png":
				$type = "png";break;
			case "gif":
				$type = "gif";break;
			default:
				return;
		}
		
		$dst_ext = strtolower(JFile::getExt($image_name));
		switch($dst_ext) {
			case "jpg":
			case "jpeg":
				$dst_ext = "jpeg";break;
			case "png":
				$dst_ext = "png";break;
			case "gif":
				$dst_ext = "gif";break;
		}
		
		$max_h = $max_height;
		$max_w = $max_width;
		$max_thumb_h = $max_height_t;
		$max_thumb_w = $max_width_t;
		
		if ( is_file( "$path/$image_name")) {
			JFile::delete( "$path/$image_name");
		}
		
		if ( is_file( "$path/$thumb_name")) {
			JFile::delete( "$path/$thumb_name");
		}
		
		$read = 'imagecreatefrom' . $type; 
		$write = 'image' . $type; 
		
		$src_img = $read($src_file);
		
		// height/width
		$imginfo = getimagesize($src_file);
		$src_w = $imginfo[0];
		$src_h = $imginfo[1];
		
		//----------------------------------------------
		
		$zoom_h = $max_h / $src_h;
	    $zoom_w = $max_w / $src_w;
	    $zoom   = min($zoom_h, $zoom_w);
	    $dst_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
	    $dst_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;
		
		$dst_img = imagecreatetruecolor($dst_w,$dst_h);
		if ($dst_ext == "jpeg") {
			$white = imagecolorallocate($dst_img,255,255,255);
			imagefill($dst_img,0,0,$white);
		} else {
			imagealphablending( $dst_img, false );
			imagesavealpha( $dst_img, true );
		}	
		imagecopyresampled($dst_img,$src_img, 0,0,0,0, $dst_w,$dst_h,$src_w,$src_h);
		
		if ((ADSMANAGER_SPECIAL == "thiago")&&($tag!= null)) {
			$tag_file = JPATH_ROOT."/images/toto.png";
			$tag_ext = strtolower(JFile::getExt($tag_file));
			switch($tag_ext) {
				case "jpg":
				case "jpeg":
					$tag_ext = "jpeg";break;
				case "bmp":
					$tag_ext = "wbmp";break;
			}
			$cmd = 'imagecreatefrom' . $tag_ext;
			$load_image = $cmd($tag_file);
			$loadsize = getimagesize($tag_file);
				
			imagealphablending( $load_image, false );
			imagesavealpha( $load_image, true );
				
			$imageTag_w = $loadsize[0];
			$imageTag_h = $loadsize[1];
				
			$tagPosX = -5;
			$tagPosY = -5;

			if ($tagPosX < 0) {
				$tagPosX = $dst_w + $tagPosX - $imageTag_w;
			}
			if ($tagPosY < 0) {
				$tagPosY = $dst_h + $tagPosY - $imageTag_h;
			}

			imagecopy($dst_img, $load_image, $tagPosX, $tagPosY, 0, 0, $imageTag_w, $imageTag_h);
			imagedestroy($load_image);
		
		}
		else if ($tag!= null) {
			$textcolor = imagecolorallocate($dst_img, 255, 255, 255);
			$fontfile = JPATH_ROOT."/components/com_adsmanager/font/verdana.ttf";
			if ($dst_ext != "jpeg") {
				imagealphablending( $dst_img, true );
				imagettftext ($dst_img, 10, 0, 5, 20,$textcolor,$fontfile,$tag );
				imagealphablending( $dst_img, false );
			} else {
           		imagettftext ($dst_img, 10, 0, 5, 20,$textcolor,$fontfile,$tag );
			}
        } 
        
        if($type == 'jpeg'){
	        $desc_img = $this->createimage($write,$dst_img,"$path/$image_name", 100);
		}else{
	        $desc_img = $this->createimage($write,$dst_img,"$path/$image_name", 0);
		}
		
		imagedestroy($dst_img);
		
		
		//-------------------------------------------
		$zoom_h = $max_thumb_h / $src_h;
		$zoom_w = $max_thumb_w / $src_w;
		$zoom   = min($zoom_h, $zoom_w);
		$dst_thumb_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
		$dst_thumb_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;
		
        //HACK
		//The getArea function me select a good range to cut the original images
		list($x, $y, $new_width, $new_height) = $this->getArea($src_w, $src_h, $max_width_t, $max_height_t);
        
		if(isset($conf->image_scaling) && $conf->image_scaling == 1)
			$dst_t_img = imagecreatetruecolor($max_width_t,$max_height_t);
		else
			$dst_t_img = imagecreatetruecolor($dst_thumb_w,$dst_thumb_h);

		if ($dst_ext == "jpeg") {
			$white = imagecolorallocate($dst_t_img,255,255,255);
			imagefill($dst_t_img,0,0,$white);
		} else {
			imagealphablending( $dst_t_img, false );
			imagesavealpha( $dst_t_img, true );
		}
		
        if(isset($conf->image_scaling) && $conf->image_scaling == 1)
            imagecopyresampled($dst_t_img,$src_img,0,0, $x,$y, $max_width_t,$max_height_t,$new_width,$new_height);
		else
            imagecopyresampled($dst_t_img,$src_img, 0,0,0,0, $dst_thumb_w,$dst_thumb_h,$src_w,$src_h);
		
		if ((ADSMANAGER_SPECIAL == "thiago")&&($tag!= null)) {
			$tag_file = JPATH_ROOT."/images/toto.png";
			$tag_ext = strtolower(JFile::getExt($tag_file));
			switch($tag_ext) {
				case "jpg":
				case "jpeg":
					$tag_ext = "jpeg";break;
				case "bmp":
					$tag_ext = "wbmp";break;
			}
			$cmd = 'imagecreatefrom' . $tag_ext;
			$load_image = $cmd($tag_file);
			$loadsize = getimagesize($tag_file);
				
			imagealphablending( $load_image, false );
			imagesavealpha( $load_image, true );
				
			$imageTag_w = $loadsize[0];
			$imageTag_h = $loadsize[1];
				
			$tagPosX = -5;
			$tagPosY = -5;

			if ($tagPosX < 0) {
				$tagPosX = $dst_thumb_w + $tagPosX - $imageTag_w;
			}
			if ($tagPosY < 0) {
				$tagPosY = $dst_thumb_h + $tagPosY - $imageTag_h;
			}
				
			imagecopy($dst_t_img, $load_image, $tagPosX, $tagPosY, 0, 0, $imageTag_w, $imageTag_h);
			imagedestroy($load_image);
		
		}
		else if ($tag!= null) {
			$textcolor = imagecolorallocate($dst_t_img, 255, 255, 255);
			$fontfile = JPATH_ROOT."/components/com_adsmanager/font/verdana.ttf";
			if ($dst_ext != "jpeg") {
				imagealphablending( $dst_t_img, true );
				imagettftext ($dst_t_img, 7, 0, 5, 12,$textcolor,$fontfile,$tag );
				imagealphablending( $dst_t_img, false );
			} else {
				imagettftext ($dst_t_img, 7, 0, 5, 12,$textcolor,$fontfile,$tag );
			}
		} 
		
		if($type == 'jpeg'){
	        $desc_img = $this->createimage($write,$dst_t_img,"$path/$thumb_name", 100);
		}else{
	        $desc_img = $this->createimage($write,$dst_t_img,"$path/$thumb_name", 0);
		}
		imagedestroy($dst_t_img);
		
		//-------------------------------------------
		
		if ($max_width_m != null) {
			$zoom_h = $max_height_m / $src_h;
		    $zoom_w = $max_width_m / $src_w;
		    $zoom   = min($zoom_h, $zoom_w);
		    $dst_medium_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
		    $dst_medium_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;
	    
	    	$dst_m_img = imagecreatetruecolor($dst_medium_w,$dst_medium_h);
			if ($dst_ext == "jpeg") {
				$white = imagecolorallocate($dst_m_img,255,255,255);
				imagefill($dst_m_img,0,0,$white);
			} else {
				imagealphablending( $dst_m_img, false );
				imagesavealpha( $dst_m_img, true );
			}
			imagecopyresampled($dst_m_img,$src_img, 0,0,0,0, $dst_medium_w,$dst_medium_h,$src_w,$src_h);
			
			if ((ADSMANAGER_SPECIAL == "thiago")&&($tag!= null)) {
				$tag_file = JPATH_ROOT."/images/toto.png";
				$tag_ext = strtolower(JFile::getExt($tag_file));
				switch($tag_ext) {
					case "jpg":
					case "jpeg":
						$tag_ext = "jpeg";break;
					case "bmp":
						$tag_ext = "wbmp";break;
				}
				$cmd = 'imagecreatefrom' . $tag_ext;
				$load_image = $cmd($tag_file);
				$loadsize = getimagesize($tag_file);
					
				imagealphablending( $load_image, false );
				imagesavealpha( $load_image, true );
					
				$imageTag_w = $loadsize[0];
				$imageTag_h = $loadsize[1];
					
				$tagPosX = -5;
				$tagPosY = -5;
	
				if ($tagPosX < 0) {
					$tagPosX = $dst_medium_w + $tagPosX - $imageTag_w;
				}
				if ($tagPosY < 0) {
					$tagPosY = $dst_medium_h + $tagPosY - $imageTag_h;
				}
			
				imagecopy($dst_m_img, $load_image, $tagPosX, $tagPosY, 0, 0, $imageTag_w, $imageTag_h);
				imagedestroy($load_image);
		
			}
			else if ($tag != null) {
				$textcolor = imagecolorallocate($dst_m_img, 255, 255, 255);
				$fontfile = JPATH_ROOT."/components/com_adsmanager/font/verdana.ttf";
				if ($dst_ext != "jpeg") {
					imagealphablending( $dst_m_img, true );
					imagettftext ($dst_m_img, 7, 0, 5, 12,$textcolor,$fontfile,$tag );
					imagealphablending( $dst_m_img, false );
				} else {
					imagettftext ($dst_m_img, 7, 0, 5, 12,$textcolor,$fontfile,$tag );
				}
			}
			if($type == 'jpeg'){
				$desc_img = $this->createimage($write,$dst_m_img,"$path/$medium_name", 100);
			}else{
				$desc_img = $this->createimage($write,$dst_m_img,"$path/$medium_name", 0);
			}
			
			imagedestroy($dst_m_img);
		}
		//exit();
	}
}