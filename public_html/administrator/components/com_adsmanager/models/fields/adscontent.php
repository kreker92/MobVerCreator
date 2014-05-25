<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');


/**
 * Supports a modal contact picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_contact
 * @since		1.6
 */
class JFormFieldAdsContent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'AdsContent';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getOptions() 
	{
		 $db =JFactory::getDBO();

	      $query = "SELECT a.ad_headline AS text, a.id AS value FROM #__adsmanager_ads as a WHERE a.published = 1 ORDER BY a.ad_headline ASC";
	      $db->setQuery($query);
	      $options = $db->loadObjectList();
	      array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Content').' -', 'value', 'text'));
	
	      return $options;
	}
}
