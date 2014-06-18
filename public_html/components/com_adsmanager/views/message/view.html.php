<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

require_once(JPATH_BASE."/components/com_adsmanager/helpers/field.php");

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewMessage extends TView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$pathway	= $app->getPathway();
		$document	= JFactory::getDocument();
		$contentmodel	    =$this->getModel( "content" );
		$configurationmodel	=$this->getModel( "configuration" );

		$conf = $configurationmodel->getConfiguration();	

		$this->assignRef('conf',$conf);	
		
		$this->assignRef('user',$user);
		
		$contentid = JRequest::getInt( 'contentid',	0 );
		$content = $contentmodel->getContent($contentid);
		$this->assignRef('content',$content);
		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('adsmanagercontent');
		
		$event = new stdClass();
		$results = $dispatcher->trigger('ADSonMessageAfterForm', array ($content));
		$event->onMessageAfterForm = trim(implode("\n", $results));
		$this->assignRef('event',$event);
		
		$document->setTitle( JText::_('ADSMANAGER_PAGE_TITLE'));
		
		parent::display($tpl);
	}
}
