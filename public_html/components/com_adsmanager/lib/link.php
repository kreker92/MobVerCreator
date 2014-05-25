<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );

class TLink {
	
	static function getPMSLink($content,$pmsText=null) {
		if ($pmsText == null) {
			$conf = TConf::getConfig();
			if ($conf->display_fullname == 1)
				$pmsText= sprintf(JText::_('ADSMANAGER_PMS_FORM'),$content->fullname);
			else
				$pmsText= sprintf(JText::_('ADSMANAGER_PMS_FORM'),$content->user);
		}
		if (is_dir(JPATH_ROOT.'/components/com_uddeim')) {
			$pmsLink = JRoute::_("index.php?option=com_uddeim&task=new&recip=".$content->userid);
			return '<a href="'.$pmsLink.'">'.$pmsText.'</a><br />';
		} else if (is_dir(JPATH_ROOT.'/components/com_community')) {
			include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
			include_once JPATH_ROOT.'/components/com_community/libraries/messaging.php';
			$pmsLink = CMessaging::getPopup($content->userid);
			return '<a onclick="'.$pmsLink.';return false;" href="#">'.$pmsText.'</a><br />';
		} else {
			return JText::_('ADSMANAGER_PMS_ERROR');
		}
	}
	
	static function getProfileLink()
	{
		if (COMMUNITY_BUILDER == 1) {
			return TRoute::_("index.php?option=com_comprofiler&task=userDetails");
		} else if (JOMSOCIAL == 1) {
			include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
			// Get CUser object
			$link = CRoute::_('index.php?option=com_community&view=profile&task=edit');
			return $link;
		} else {
			return TRoute::_("index.php?option=com_adsmanager&view=profile");
		}
	}
	
	static function getMyAdsLink()
	{
		if (COMMUNITY_BUILDER_ADSTAB == 1)
			return TRoute::_('index.php?option=com_comprofiler&tab=AdsManagerTab');
		else if (JOMSOCIAL_ADSTAB == 1) {
			include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
			// Get CUser object
			$link = CRoute::_('index.php?option=com_community&view=profile');
			return $link;
		}
		else
			return TRoute::_('index.php?option=com_adsmanager&view=myads');
	}
	

	static function getUserAdsLink($userid)
	{
		if (COMMUNITY_BUILDER_ADSTAB == 1)
			return JRoute::_("index.php?option=com_comprofiler&tab=adsmanagerTab&user=".$userid);
		else if (JOMSOCIAL_ADSTAB == 1) {
			include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
			// Get CUser object
			$link = CRoute::_('index.php?option=com_community&view=profile&userid='.$userid);
			return $link;
		}
		else
			return TRoute::_("index.php?option=com_adsmanager&view=list&user=".$userid);
	}
}