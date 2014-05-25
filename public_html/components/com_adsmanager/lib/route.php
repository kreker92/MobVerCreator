<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TRoute 
{
	var $menuname = 'mainmenu';

	/**
	 * Method to wrap around getting the correct links within the email
	 * 
	 * @return string $url
	 * @param string $url
	 * @param boolean $xhtml
	 */	
	static function getExternalURL( $url , $xhtml = false )
	{
		$uri	= JURI::getInstance();
		$base	= $uri->toString( array('scheme', 'host', 'port'));
		
		return $base . TRoute::_( $url , $xhtml );
	}
	
	/**
	 * Wrapper to JRoute to handle itemid
	 * We need to try and capture the correct itemid for different view	 
	 */	 	
	static function _($url, $xhtml = true, $ssl = null) 
	{	
		static $itemid = array();

		parse_str( JString::str_ireplace( 'index.php?' , '' , $url  ) );

		if( isset( $option ) && $option != 'com_adsmanager' && $option != 'com_paidsystem' )
		{			
			return JRoute::_( $url , $xhtml , $ssl );
		}
		
		if (!isset($option)) {
			return JRoute::_( $url , $xhtml , $ssl );
		}
		
		
		$urloption = $option;
		
		if($urloption == 'com_adsmanager' && empty($view))
		{
			$view = 'front';
		}
		if($urloption == 'com_paidsystem' && empty($view))
		{
			$view = 'form';
		}
		
		if(!empty($task) && $task ='write')
		{
			$view = 'edit';
		}
		
		if (ADSMANAGER_SPECIAL == "abrivac") {
			if ($view == "list")
				$view ="result";
			if ($view == "preview")
				$view ="edit";
		}
		
		if ($view == "details") {
			$view = "details".$catid;
			$originalview = "details";
		} else {
			$originalview = $view;
		}
		
		if(empty($itemid[$view]))
		{
			$currentItemid = JRequest::getInt('Itemid', 0);
			$isValid = false;
			
			$currentView 	= JRequest::getVar('view', 'front');
			$currentOption 	= JRequest::getVar('option');

 			// If the current Itemid match the expected Itemid based on view
 			// we'll just use it
 			$db		= JFactory::getDBO();
			
			if ($originalview == "details") {
				$viewId =TRoute::_getDetailsItemid($catid,$urloption);	
			} else {
				$viewId =TRoute::_getViewItemid($view,$urloption);	
			}
				
			// if current itemid 
			if(($currentOption == $urloption) && $currentView == $view && $currentItemid!=0)
			{
				$itemid[$view] = $currentItemid;
				$isValid = true;
			} 
			else if($viewId === $currentItemid && !is_null($currentItemid) && $currentItemid!=0)
			{
				$itemid[$view] = $currentItemid;
				$isValid = true;
			}
			else if($viewId !== 0 && !is_null($viewId))
			{
				$itemid[$view] = $viewId;
				$isValid = true;
			}
			
			
			if(!$isValid)
			{
				$id = TRoute::_getDefaultItemid($urloption);
				if($id !== 0 && !is_null($id))
				{
					$itemid[$view] =$id;
				}
				$isValid = true;
			}
			
			// Search the mainmenu for the 1st itemid of adsmanager we can find
			if(!$isValid)
			{
				$db		= JFactory::getDBO();
				$query	= 'SELECT id FROM #__menu WHERE '
						. 'link LIKE ' . $db->Quote('%'.$urloption.'%') 
						. 'AND published =' . $db->Quote( 1 ) . ' '
						//. 'AND menutype =' . $db->Quote('{TRoute::menuname}')
						. 'AND menutype !=' . $db->Quote( $config->get( 'toolbar_menutype' ) ) . ' '
						. 'AND type =' . $db->Quote( 'component' );
				$db->setQuery($query);
				$isValid = $db->loadResult();
				
				if(!empty($isValid))
				{
					$itemid[$view] = $isValid;
				}
			}			
			
			// If not in mainmenu, seach in any menu
			if(!$isValid)
			{
				$query	= 'SELECT id FROM #__menu WHERE '
						. ' link LIKE ' . $db->Quote('%'.$urloption.'%')
						. ' AND published =' . $db->Quote( 1 ) . ' '
						. ' AND menutype !=' . $db->Quote( $config->get( 'toolbar_menutype' ) ) . ' '
						. ' AND type =' . $db->Quote( 'component' );
				$db->setQuery($query);
				$isValid = $db->loadResult();	
				if(!empty($isValid))
					$itemid[$view] = $isValid;
			}
			
			
		}
		
		$pos = strpos($url, '#');
		if ($pos === false)
		{
			if( isset( $itemid[$view] ) ){
            	if(strpos($url, 'Itemid=')=== false && strpos($url,$urloption) !== false){
                	$url .= '&Itemid='.$itemid[$view];
                }
        	}
		}
		else 
		{
			if( isset( $itemid[$view] ) )
				$url = str_ireplace('#', '&Itemid='.$itemid[$view].'#', $url);
		}		
		
		$data =  JRoute::_($url, $xhtml, $ssl);
        return $data;
	}
	
	/**
	 * Return the Itemid specific for the given view. 
	 */	 	
	static function _getViewItemid($view,$urloption='com_adsmanager')
	{
		static $itemid = array();
		
		$lg = JFactory::getLanguage();
		$lang =  $lg->getTag();
		
		if(empty($itemid[$view]))
		{
			$db		= JFactory::getDBO();
			//$config	= CFactory::getConfig();
			$url 	= $db->quote('%option='.$urloption.'&view=' . $view . '%');
			$type = $db->quote('component');
			
			if (version_compare(JVERSION,'2.5','<')) {
				$query	= 'SELECT id FROM #__menu '
						. 'WHERE link LIKE ' . $url . ' '
						. 'AND published =' . $db->Quote( 1 ) . ' '
					 	. 'AND type =' . $db->Quote( 'component' );
				$db->setQuery($query);
				$val = $db->loadResult();
			} else {
				$query	= 'SELECT id,language FROM #__menu '
					. 'WHERE link LIKE ' . $url . ' '
					. 'AND published =' . $db->Quote( 1 ) . ' '
				 	. 'AND type =' . $db->Quote( 'component' );
				$db->setQuery($query);
				$list = $db->loadObjectList();
				$val = null;
				foreach($list as $m) {
					if (($m->language=="*")||($m->language=="$lang")) {
						$val = $m->id;
						break;
					}
				}
			}
			
			$itemid[$view] = $val;
		} else{
			$val = $itemid[$view];
		}
		return $val;
	}
	
	/**
	 * Return the Itemid specific for the given view. 
	 */	 	
	static function _getDetailsItemid($catid,$urloption='com_adsmanager')
	{
		static $detailsitemid = array();
		
		$lg = JFactory::getLanguage();
		$lang =  $lg->getTag();
		
		if(empty($detailsitemid[$catid]))
		{
			$db		= JFactory::getDBO();
			//$config	= CFactory::getConfig();
			$url 	= $db->quote('%option='.$urloption.'&view=details%');
			$type = $db->quote('component');
			
			if (version_compare(JVERSION,'2.5','<')) {
				$query	= 'SELECT id FROM #__menu '
						. 'WHERE link LIKE ' . $url . ' '
						. 'AND published =' . $db->Quote( 1 ) . ' '
					 	. 'AND type =' . $db->Quote( 'component' );
				$db->setQuery($query);
				$val = $db->loadResult();
			} else {
				$query	= 'SELECT id,language FROM #__menu '
					. 'WHERE link LIKE ' . $url . ' '
					. 'AND published =' . $db->Quote( 1 ) . ' '
				 	. 'AND type =' . $db->Quote( 'component' );
				$db->setQuery($query);
				$list = $db->loadObjectList();
				$val = null;
				foreach($list as $m) {
					if (($m->language=="*")||($m->language=="$lang")) {
						$val = $m->id;
						break;
					}
				}
			}
			if ($val == null) {
				$url 	= $db->quote('%option='.$urloption.'&view=list%');
				if (version_compare(JVERSION,'2.5','<')) {
					$query	= 'SELECT id FROM #__menu '
						. 'WHERE link LIKE '. $url . " AND link LIKE '%catid=".(int)$catid."%' "
						. 'AND published =' . $db->Quote( 1 ) . ' '
						. 'AND type =' . $db->Quote( 'component' );
					$db->setQuery($query);
					$val = $db->loadResult();
				} else {
					$query	= 'SELECT id,language FROM #__menu '
						. 'WHERE link LIKE '. $url . " AND link LIKE '%catid=".(int)$catid."%' "
						. 'AND published =' . $db->Quote( 1 ) . ' '
						. 'AND type =' . $db->Quote( 'component' );
					$db->setQuery($query);
					$list = $db->loadObjectList();
					$val = null;
					foreach($list as $m) {
						if (($m->language=="*")||($m->language=="$lang")) {
							$val = $m->id;
							break;
						}
					}
				}
			}
			
			$detailsitemid[$catid] = $val;
		} else{
			$val = $detailsitemid[$catid];
		}
		return $val;
	}
	
	/**
	 * Return the Itemid for default view, frontpage
	 */	 	
	static function _getDefaultItemid($urloption = 'com_adsmanager')
	{
		static $defaultId = null ;
		
		if($defaultId != null)
			return $defaultId;
			
		$db		= JFactory::getDBO();
		
		if ($urloption == 'com_adsmanager')
			$url = $db->quote("index.php?option=com_adsmanager&view=front");
		else 	
			$url = $db->quote("index.php?option=com_paidsystem&view=form");
		$type = $db->quote('component');
		
		$query  = 'SELECT id FROM #__menu'
					.' WHERE link = ' . $url .' AND published =' . $db->Quote(1) . ' '
					. 'AND type =' . $db->Quote( 'component' );					
		$db->setQuery($query);
		$val = $db->loadResult();
		
		if(!$val)
		{
			$url = $db->quote("%option=".$urloption."%");
			
			$query  = 'SELECT id FROM #__menu ' 
				.' WHERE link LIKE ' . $url . ' AND published =' . $db->Quote(1) . ' '
				. 'AND type =' . $db->Quote( 'component' );
			$db->setQuery($query);
			$val = $db->loadResult();
		}
		
		$defaultId = $val;
		return $val;
	}
}
