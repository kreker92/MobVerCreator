<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TTools {
	
	/**
	 * This function will redirect the current page to the joomla login page
	 * @param URL $returnurl, after login redirect to this url
	 */
	static function redirectToLogin($returnurl="") {
		$app = JFactory::getApplication();
		$returnurl = base64_encode(TRoute::_($returnurl,false));
		if (COMMUNITY_BUILDER == 1) {
			$app->redirect(JRoute::_("index.php?option=com_comprofiler&task=registers"));
		} else {
		if(version_compare(JVERSION,'1.6.0','>=')){
			//joomla 1.6 format
                $app->redirect( JRoute::_("index.php?option=com_users&view=login&return=$returnurl"));
		} else {
			//joomla 1.5 format
                $app->redirect( JRoute::_("index.php?option=com_user&view=login&return=$returnurl"));
            }
		}
	}
	
    static function print_popup($url)
	{
		$url .= '&tmpl=component&print=1';
	
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
	
		// checks template image directory for image, if non found default are loaded
		$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
	
		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']		= 'nofollow';
	
		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}
	
	static function print_screen()
	{
		// checks template image directory for image, if non found default are loaded
		$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a><script>jQ(function() {window.print();});</script>';
	}
    
	static function getCatImageUrl($catid,$thumb=false) {
		$extensions = array("jpg","png","gif");
		$image_name = ($thumb == true) ? "cat_t":"cat";
		
		foreach($extensions as $ext) {
			if (file_exists(JPATH_ROOT."/images/com_adsmanager/categories/".$catid."$image_name.$ext"))
				return JURI::root()."images/com_adsmanager/categories/".$catid."$image_name.$ext";
		}
		return JURI::root().'components/com_adsmanager/images/default.gif';
	}
    
    static function loadModule($module, $title, $style = 'none')
	{
        $mods[$module] = '';
        $document	= JFactory::getDocument();
        $renderer	= $document->loadRenderer('module');
        $mod		= JModuleHelper::getModule($module, $title);
        // If the module without the mod_ isn't found, try it with mod_.
        // This allows people to enter it either way in the content
        if (!isset($mod)){
            $name = 'mod_'.$module;
            $mod  = JModuleHelper::getModule($name, $title);
        }
        $params = array('style' => $style);
        ob_start();

        echo $renderer->render($mod, $params);

        $mods[$module] = ob_get_clean();
            
		return $mods[$module];
	}
	
	/**
	 * This method transliterates a string into an URL
	 * safe string or returns a URL safe UTF-8 string
	 * based on the global configuration
	 *
	 * @param   string  $string  String to process
	 * @param   boolean $forcetransliterate set to true to force transliterate
	 * 
	 * @return  string  Processed string
	 *
	 * @since   11.1
	 */
	static public function stringURLSafe($string,$unicodesupport=false)
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
			if ($unicodesupport == false && JFactory::getConfig()->get('unicodeslugs') == 1)
			{
				$output = JFilterOutput::stringURLUnicodeSlug($string);
			}
			else
			{
				$output = JFilterOutput::stringURLSafe($string);
			}
		} else {
			$output = JFilterOutput::stringURLSafe($string);
		}
	
		return $output;
	}
	
}