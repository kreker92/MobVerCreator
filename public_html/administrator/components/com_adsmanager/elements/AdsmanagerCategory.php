<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

class JElementAdsmanagerCategory extends JElement
{
   var   $_name = 'AdsmanagerCategory';

   function fetchElement($name, $value, &$node, $control_name)
   {
      $db =JFactory::getDBO();

      $query = "SELECT c.name AS text, c.id AS value FROM #__adsmanager_categories as c WHERE c.published = 1 ORDER BY c.ordering ASC";
      $db->setQuery($query);
      $options = $db->loadObjectList();
      array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Category').' -', 'value', 'text'));

      return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );   
   }
}
?>