<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewNbads extends TView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$pathway	= $app->getPathway();
		$document	= JFactory::getDocument();
		
		$contentmodel	= $this->getModel( "content" );
		$fieldmodel	    = $this->getModel( "field" );

		$filters = array();
		$filters['publish'] =  1;
		
		$catid = JRequest::getInt( 'catid',	0 );
		if ($catid == -1)
			$catid = 0;
		if ($catid != 0)
			$filters['category'] = $catid;
		
		$searchfields = $fieldmodel->getFields($catid);
		$filters['fields'] = $fieldmodel->getSearchFieldsSql($searchfields);
		
        $total = $contentmodel->getNbContents($filters);
	
		echo json_encode(array("count"=>$total));
	}
}
