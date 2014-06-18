<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.filesystem.file');

class AdsmanagerTableCategory extends JTable
{
	var $id = null;
	var $parent = null;
	var $name = null;
	var $description = null;
	var $ordering = null;
	var $metadata_description = null;
	var $metadata_keywords = null;
	var $published = 1;
			
    function __construct(&$db)
    {
    	parent::__construct( '#__adsmanager_categories', 'id', $db );
    }
    
    function saveContent($post,$files,$conf,$model)
    {
    	$app = JFactory::getApplication();
		
    	$extensions = array("jpg","png","gif");

		// image2 delete
		if ((isset( $files['cat_image']) and !$files['cat_image']['error'] )|| 
			( $post['cb_image'] == "delete")) {
			foreach($extensions as $ext) {
				$pict = JPATH_ROOT."/images/com_adsmanager/categories/".$this->id."cat.$ext";
				if ( is_file( $pict)) {
					JFile::delete( $pict);
				}
				$pict = JPATH_ROOT."/images/com_adsmanager/categories/".$this->id."cat_t.$ext";
				if ( is_file( $pict)) {
					JFile::delete( $pict);
				}
			}
		}
								
		if (isset( $files['cat_image'])) {
			if ( $files['cat_image']['size'] > $conf->max_image_size) {
				$app->redirect("index.php?option=com_adsmanager&c=categories", JText::_('ADSMANAGER_IMAGETOOBIG'),'message');
				return;
			}
		}
	
		// image1 upload
		if (isset( $files['cat_image']) and !$files['cat_image']['error'] ) {
			
			$ext = strtolower(JFile::getExt($files['cat_image']['name']));
			if ($ext == "jpeg")
				$ext = "jpg";
			
			switch($ext) {
				case "jpg":
				case "png":
				case "gif":
					$path= JPATH_ROOT."/images/com_adsmanager/categories/";
					$model->createImageAndThumb($files['cat_image']['tmp_name'],
										$this->id."cat.$ext",
										$this->id."cat_t.$ext",
										$conf->cat_max_width,
										$conf->cat_max_height,
										$conf->cat_max_width_t,
										$conf->cat_max_height_t,
										"",
										$path,
										$files['cat_image']['name']);
					break;
				default:
					//Not supported
			}
			
			
		}	
    }
    
    function deleteContent($id) {
    	$app = JFactory::getApplication();

		$this->_db->setQuery("SELECT * FROM #__adsmanager_categories \nWHERE id != ".(int)$id." AND parent = ".(int)$id);
		if ($this->_db->loadResult()) 
		{
			$app->redirect("index.php?option=com_adsmanager&c=categories", JText::_('ADSMANAGER_DELETE_CATEGORY_SELECT_CHIDLS'),'message');
		}
		$this->_db->setQuery("DELETE FROM #__adsmanager_categories \nWHERE id = ".(int)$id);
		$this->_db->query();

		$this->_db->setQuery( "SELECT a.id FROM #__adsmanager_ads as a ".
							 "LEFT JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".
							 "WHERE adcat.catid = ".(int)$id);
		
		$idsarray = $this->_db->loadResultArray();
		if (isset($idsarray))
		{
			$content = JTable::getInstance('contents', 'AdsmanagerTable');
			
			foreach($idsarray as $adid)
			{
				$content->deleteContent($adid);
			}
		}
    }
}
	