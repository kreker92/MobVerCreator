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
class JFormFieldAdsCategory extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'AdsCategory';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getOptions() 
	{
		 $db =JFactory::getDBO();

	      $query = "SELECT c.name AS text, c.id AS value FROM #__adsmanager_categories as c WHERE c.published = 1 ORDER BY c.ordering ASC";
	      $db->setQuery($query);
	      $options = $db->loadObjectList();
	      array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Category').' -', 'value', 'text'));
	
	      return $options;
	}
}
