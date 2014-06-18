<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION,'1.6','>=')) {
    //ACL
    if (!JFactory::getUser()->authorise('core.manage', 'com_adsmanager')) {
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    }
}

// Make sure the user is authorized to view this page
$user = JFactory::getUser();
/*if (!$user->authorize( 'com_adsmanager', 'manage' )) {
    $app = JFactory::getApplication();
	$app->redirect('index.php', JText::_('ALERTNOTAUTH'),'message');
}*/

// Component Helper
jimport('joomla.application.component.helper');

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

$controllerName = JRequest::getCmd( 'c', 'configuration' );

require_once( JPATH_COMPONENT."/controllers/$controllerName.php" );
$controllerName = 'AdsmanagerController'.$controllerName;

$lang = JFactory::getLanguage();
$lang->load("com_adsmanager",JPATH_ROOT);

// Create the controller
$controller = new $controllerName();

if(version_compare(JVERSION,'1.6.0','>=')){
	JHtml::_('behavior.framework');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_CONFIGURATION'), 'index.php?option=com_adsmanager&amp;c=configuration');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_FIELDS'), 'index.php?option=com_adsmanager&amp;c=fields');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_COLUMNS'), 'index.php?option=com_adsmanager&amp;c=columns');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_AD_DISPLAY'), 'index.php?option=com_adsmanager&amp;c=positions');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_CATEGORIES'), 'index.php?option=com_adsmanager&amp;c=categories');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_CONTENTS'), 'index.php?option=com_adsmanager&amp;c=contents');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_PLUGINS'), 'index.php?option=com_adsmanager&amp;c=plugins');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_FIELD_IMAGES'), 'index.php?option=com_adsmanager&amp;c=fieldimages');
	JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_TOOLS'), 'index.php?option=com_adsmanager&amp;c=tools');
	//if (ADSMANAGER_SPECIAL == "flatmatch") {
		JSubMenuHelper::addEntry(JText::_('COM_ADSMANAGER_SEARCH_MODULE'), 'index.php?option=com_adsmanager&amp;c=searchmodule');
	//}
}	
		

$db->setQuery("SELECT * FROM #__adsmanager_config LIMIT 1");
$config = $db->loadObject();

// Perform the Request task
$controller->execute(JRequest::getCmd('task', null));
$controller->redirect();

echo "<br/><div align='center'><i>Adsmanager 2.9.12</i></div>";
echo '<div class="alert">Upgrade to a PRO version, to get full features and support : <a href="http://joomprod.com/compare.html">joomprod.com</a></div>';
