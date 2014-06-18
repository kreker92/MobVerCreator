<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class AdsmanagerControllerFieldimages extends TController
{
	var $_view = null;
	var $_model = null;
	
	function init()
	{
		// Set the default view name from the Request
		$this->_view = $this->getView("admin",'html');

		// Push a model into the view
		$this->_model = $this->getModel( "adsmanager");
		if (!JError::isError( $this->_model )) {
			$this->_view->setModel( $this->_model, true );
		}
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$this->init();
		$this->_view->setLayout("listfieldimages");
		$this->_view->display();
	}
	
	function remove()
	{
		$app = JFactory::getApplication();
		
		$cid = JRequest::getVar( 'cid' );
		if (!is_array($cid) || count($cid) < 1) {
			$app->redirect( "index.php?option=com_adsmanager&c=fieldimages");
		}
		foreach($cid as $filename)
		{
			if ($filename != "" && is_file(JPATH_ROOT."/images/com_adsmanager/fields/".$filename))
			{
				JFile::delete(JPATH_ROOT."/images/com_adsmanager/fields/".$filename);
			}
		}
		$app->redirect("index.php?option=com_adsmanager&c=fieldimages");
	}
	
	function upload()
	{
		$app = JFactory::getApplication();
		
		$userfile = JRequest::getVar('userfile', null,"FILES" );
		$filename = $userfile['name'];
		while(file_exists(JPATH_ROOT."/images/com_adsmanager/fields/".$filename)){
			$filename = "copy_".$filename;
		}
		@move_uploaded_file($userfile['tmp_name'],
							JPATH_ROOT."/images/com_adsmanager/fields/".$filename);	

		$app->redirect("index.php?option=com_adsmanager&c=fieldimages");
	}
}
