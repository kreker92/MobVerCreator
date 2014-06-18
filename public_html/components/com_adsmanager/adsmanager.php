<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.'/lib/core.php');

// Require the com_content helper library
require_once(JPATH_COMPONENT.'/controller.php');

// Component Helper
jimport('joomla.application.component.helper');



// Create the controller
if(version_compare(JVERSION,'1.6.0','>=')){
	$controller = TController::getInstance('adsmanager');
} else {
	$controller = new AdsmanagerController();
}

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();