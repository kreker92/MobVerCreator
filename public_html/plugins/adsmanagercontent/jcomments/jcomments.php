<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

/**
 * JComments Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.JComments
 * @since		1.5
 */
class plgAdsmanagercontentJComments extends JPlugin
{
	/**
	 * JComments before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	object		The content params
	 */
	public function ADSonContentAfterDisplay($content)
	{
		// add JComments
		$comments = JPATH_ROOT.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
		if (is_file($comments)) {
		  require_once($comments);
		  return JComments::showComments($content->id, 'com_adsmanager', htmlspecialchars($content->ad_headline));
		}
	}
}
