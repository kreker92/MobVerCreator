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
class AdsmanagerViewList extends TView
{
	function display()
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$pathway	= $app->getPathway();
		$document	= JFactory::getDocument();
		$contentmodel	=$this->getModel( "content" );
		$catmodel	=$this->getModel( "category" );
		$configurationmodel	=$this->getModel( "configuration" );
		
		$uri = JFactory::getURI();
		$this->requestURL = $uri->toString();

		// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu    = $menus->getActive();
		
		$conf = $configurationmodel->getConfiguration();
		
		if ($conf->show_rss == 0)
			return;
		
		$catid = JRequest::getInt( 'catid',	0 );
		if ($catid != "0") {
			$category = $catmodel->getCategory($catid);
			$category->img = TTools::getCatImageUrl($catid,true);
		}
		else
		{
			$category->name = JText::_("ADSMANAGER_ALL_ADS");
			$category->description = "";
			$category->img = "";
		}
		
		$filters = array();
		$filters['publish'] =  1;
		
		if ($catid != 0)
			$filters['category'] = $catid;
			
		$listuser = JRequest::getInt( 'user',	0 );
		if ($listuser != 0) {
			$filters['user'] = $listuser;
			$category->name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->username;
		}
		
		//$this->assignRef('list_description',$category->description);
		
		//$this->assignRef('contents',$contents);
		
		$document->setTitle( JText::_('ADSMANAGER_PAGE_TITLE'). JText::_($category->name) );
		$document->setDescription("");
		
		//Add 1 $admin to avoid display top ads in the rss
		$contents = $contentmodel->getContents($filters,0, 20,"a.date_created DESC ,a.id ","DESC",1);
		
		jimport('joomla.document.feed.feed');
		require_once(JPATH_ROOT."/libraries/joomla/document/feed/feed.php");
		
		foreach($contents as $row)
		{
		// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $row->ad_headline;
			$item->link 		= TRoute::_('index.php?option=com_adsmanager&view=details&catid='.$row->catid.'&id='.$row->id);
			$item->description 	= $row->ad_text;
			if (count($row->images) > 0) {
				$path = $thumbnail = JURI::base()."images/com_adsmanager/ads/".$row->images[0]->thumbnail;
				$image = '<img class="center" src="'.$path.'" alt="'.htmlspecialchars($row->ad_headline).'"/>';
				$item->description 	= $image.$item->description;
			}	
			$item->date			= $row->date_created;
			$item->category   	= $row->parent." / ".$row->cat;
			$item->author		= $author;
			// loads item info into rss array
			$document->addItem( $item );
		}
	}
}
