<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.filter.output' );

require_once(JPATH_ROOT.'/components/com_adsmanager/lib/core.php');

function getAdsmanagerRouteCategory($id)
{
	$db =JFactory::getDBO();
	$sql = "SELECT name FROM #__adsmanager_categories WHERE id = ".(int)$id;
	$db->setQuery($sql);
	$result = $db->loadResult();
	$result= TTools::stringURLSafe($result);
	$result = JString::substr($result,0,30);
	return $result;
}

function getAdsmanagerRouteContent($id)
{
	$db =JFactory::getDBO();
	$sql = "SELECT ad_headline FROM #__adsmanager_ads WHERE id = ".(int)$id;
	$db->setQuery($sql);
	$result = $db->loadResult();
	$result= TTools::stringURLSafe($result);
	$result = JString::substr($result,0,30);
	return $result;
}

function getAdsmanagerUser($userid)
{
	$db =JFactory::getDBO();
	$sql = "SELECT username FROM #__users WHERE id = ".(int)$userid;
	$db->setQuery($sql);
	$result = $db->loadResult();
	$result= TTools::stringURLSafe($result);
	$result = JString::substr($result,0,30);
	return $result;
}


function AdsmanagerBuildRoute(&$query)
{
	$segments = array();
	
	if (!isset($query['task']))
		$t = "";
	else
		$t = $query['task'];
		
	switch($t)
	{
		case "display":
		case "":
			if (!isset($query['view']))
				$v = "";
			else
				$v = $query['view'];
			switch($v)
			{
				case "preview":
					$segments[] = $query["id"]."-".getAdsmanagerRouteContent($query["id"]);
					$segments[] = JText::_('ADSMANAGER_SEF_PREVIEW');
					unset($query["id"]);
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "details":
					$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
					$segments[] = $query["id"]."-".getAdsmanagerRouteContent($query["id"]);
					unset($query["id"]);
					unset($query["catid"]);
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "expiration":
					$segments[] = $query["id"]."-".getAdsmanagerRouteContent($query["id"]);
					$segments[] = JText::_('ADSMANAGER_SEF_EXPIRATION');
					unset($query["id"]);
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "front":
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "list":
					if (isset($query["catid"])&&($query["catid"] != 0)) {
						$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
					}
					else if (isset($query["user"])&&($query["user"] != 0)) {
						$segments[] = JText::_('ADSMANAGER_SEF_USER');
						$segments[] = $query["user"]."-".getAdsmanagerUser($query["user"]);
					}
					else if (isset($query["user"])) {
						$segments[] = JText::_('ADSMANAGER_SEF_USER');
					}
					else {
						$segments[] = JText::_('ADSMANAGER_SEF_ALL_ADS');
					}
					unset($query["user"]);
					unset($query["catid"]);
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "myads":
					$segments[] = JText::_('ADSMANAGER_SEF_MY_ADS');
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "message":
					$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
					$segments[] = $query["contentid"]."-".getAdsmanagerRouteContent($query["contentid"]);
					$segments[] = JText::_('ADSMANAGER_SEF_CONTACT');
					unset($query["contentid"]);
					unset($query["catid"]);
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "profile":
					$segments[] = JText::_('ADSMANAGER_SEF_PROFILE');
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "result":
					$segments[] = JText::_('ADSMANAGER_SEF_RESULT');
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "rules":
					$segments[] = JText::_('ADSMANAGER_SEF_RULES');
					unset($query["task"]);
					unset($query["view"]);
					break;
				case "search":
					if (isset($query["catid"])&&($query["catid"] != 0)) {
						$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
					}
					$segments[] = JText::_('ADSMANAGER_SEF_SEARCH');
					unset($query["task"]);
					unset($query["view"]);
					unset($query["catid"]);
					break;
				case "edit":
					if (isset($query["id"])&&($query["id"] != 0))
					{
						if (!isset($query["catid"]))
							$query["catid"] = 0;
						$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
						$segments[] = $query["id"]."-".getAdsmanagerRouteContent($query["id"]);
						$segments[] = JText::_('ADSMANAGER_SEF_EDIT');
					}
					else
					{
						if (isset($query["catid"])) {
							$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
						}
						$segments[] = JText::_('ADSMANAGER_SEF_WRITE');
					}
			}
			break;
		case "write":
			if (isset($query["id"])&&($query["id"] != 0))
			{	
				if (!isset($query["catid"]))
					$query["catid"] = 0;
				$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
				$segments[] = $query["id"]."-".getAdsmanagerRouteContent($query["id"]);
				$segments[] = JText::_('ADSMANAGER_SEF_EDIT');
			}
			else
			{
				if (isset($query["catid"])) {
					$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
				}
				$segments[] = JText::_('ADSMANAGER_SEF_WRITE');
			}
			
			unset($query["id"]);
			unset($query["catid"]);
			unset($query["task"]);
			unset($query["view"]);
			break;
			break;
		case "delete":
			$segments[] = $query["catid"]."-".getAdsmanagerRouteCategory($query["catid"]);
			$segments[] = $query["id"]."-".getAdsmanagerRouteContent($query["id"]);
			$segments[] = JText::_('ADSMANAGER_SEF_DELETE');
			unset($query["id"]);
			unset($query["catid"]);
			unset($query["task"]);
			unset($query["view"]);
			break;
			break;
		case "save":
		
			break;
		case "saveprofile":
		
			break;
		case "sendmessage":
		
			break;
		case "renew":
		
			break;
	}
	
	//unset($query["task"]);
	//unset($query["view"]);

	return $segments;
}

function AdsmanagerParseRoute($segments)
{
	$app = JFactory::getApplication();
	
	$vars = array();

	//Get the active menu item
	$menu = $app->getMenu();
	$item = $menu->getActive();
	
	$nbsegments = count($segments); 
	
	if (in_array(JText::_('ADSMANAGER_SEF_RESULT'),$segments))
	{
		$vars["view"] = "result";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_SEARCH'),$segments))
	{
		$vars["view"] = "search";
		$catid = explode( ':', $segments[0] );
	    $vars['catid'] = (int) $catid[0];
	    $vars['task'] = "display";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_EDIT'),$segments))
	{
		$vars["view"] = "edit";
		$catid = explode( ':', $segments[0] );
	    $vars['catid'] = (int) $catid[0];
	    $id = explode( ':', $segments[1] );
	    $vars['id'] = (int) $id[0];
	    $vars['task'] = "write";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_PREVIEW'),$segments))
	{
		$vars["view"] = "preview";
		$id = explode( ':', $segments[1] );
		$vars['id'] = (int) $id[0];
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_WRITE'),$segments))
	{
		$vars["view"] = "edit";
	    $vars['task'] = "write";
	    $catid = explode( ':', $segments[0] );
	    $vars['catid'] = (int) $catid[0];
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_DELETE'),$segments))
	{
		$vars["view"] = "edit";
		$catid = explode( ':', $segments[0] );
	    $vars['catid'] = (int) $catid[0];
	    $id = explode( ':', $segments[1] );
	    $vars['id'] = (int) $id[0];
	    $vars['task'] = "delete";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_EXPIRATION'),$segments))
	{
		$vars["view"] = "expiration";
	    $id = explode( ':', $segments[0] );
	    $vars['id'] = (int) $id[0];
	    $vars['task'] = "display";
		
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_RULES'),$segments))
	{
		$vars["view"] = "rules";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_PROFILE'),$segments))
	{
		$vars["view"] = "profile";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_ALL_ADS'),$segments))
	{
		$vars["view"] = "list";		
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_MY_ADS'),$segments))
	{
		$vars["view"] = "myads";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_CONTACT'),$segments))
	{
		$vars["view"] = "message";
		$catid = explode( ':', $segments[0] );
	    $vars['catid'] = (int) $catid[0];
	    $id = explode( ':', $segments[1] );
	    $vars['contentid'] = (int) $id[0];
	    $vars['task'] = "display";
	}
	else if (in_array(JText::_('ADSMANAGER_SEF_USER'),$segments))
	{
		$userid = explode( ':', $segments[1] );
		$vars['user'] = (int) $userid[0];
		$vars['task'] = "display";
		$vars['view'] = "list";
	}
	else
	{
		if ($nbsegments == 2)
		{
			$catid = explode( ':', $segments[0] );
		    $vars['catid'] = (int) $catid[0];
		    $id = explode( ':', $segments[1] );
		    $vars['id'] = (int) $id[0];
		    $vars["view"] = "details";
		}
		else
		{
			$catid = explode( ':', $segments[0] );
	    	$vars['catid'] = (int) $catid[0];
	    	$vars["view"] = "list";
		}
		$vars['task'] = "display";
	}

	return $vars;
}
