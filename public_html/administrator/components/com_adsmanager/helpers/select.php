<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class AdsmanagerHelperSelect
{
	protected static function genericlist($list, $name, $attribs, $selected, $idTag)
	{
		if(empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';
			foreach($attribs as $key=>$value)
			{
				$temp .= $key.' = "'.$value.'"';
			}
			$attribs = $temp;
		}

		return JHTML::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	protected static function genericradiolist($list, $name, $attribs, $selected, $idTag)
	{
		if(empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';
			foreach($attribs as $key=>$value)
			{
				$temp .= $key.' = "'.$value.'"';
			}
			$attribs = $temp;
		}

		return JHTML::_('select.radiolist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	public static function booleanlist( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','','---'),
			JHTML::_('select.option',  '0', JText::_( 'JNo' ) ),
			JHTML::_('select.option',  '1', JText::_( 'JYes' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function published($id = 'published',$selected = null , $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option',null,'- '.sprintf(JText::_('ADSMANAGER_COMMON_SELECT'),JText::_('ADSMANAGER_FIELDSTATE')).' -');
		$options[] = JHTML::_('select.option',0,JText::_('JUNPUBLISHED'));
		$options[] = JHTML::_('select.option',1,JText::_('JPUBLISHED'));

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}
	
	public static function languages($selected = null, $id = 'language', $attribs = array() )
	{
		jimport('joomla.language.helper');
        if(version_compare(JVERSION, '1.6', 'ge')) {
			$languages = JLanguageHelper::getLanguages('lang_code');
            $options = array();

            if(!empty($languages)) foreach($languages as $key => $lang)
            {
                $options[] = JHTML::_('select.option',$key,$lang->title);
            }
		}else {
            $languages = JLanguageHelper::createLanguageList('lang_code');
            $options = array();

            if(!empty($languages)) foreach($languages as $lang)
            {
                $options[] = JHTML::_('select.option',$lang['value'],$lang['text']);
            }
		}
		
		
		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	
	public static function columns($name = 'columnid', $selected = '', $attribs = array())
	{
		$_db = JFactory::getDbo();
		$_db->setQuery("SELECT c.* ".
							"FROM #__adsmanager_columns as c ".
							"ORDER BY c.ordering ");
		$list = $_db->loadObjectList();
							
		$options[] = JHTML::_('select.option','','- '.sprintf(JText::_('ADSMANAGER_COMMON_SELECT'),JText::_('ADSMANAGER_COLUMN')).' -');
		foreach($list as $item) {
			$options[] = JHTML::_('select.option',$item->id,JText::_($item->name));
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		return $html;
	}
	
	public static function positions($name = 'pos', $selected = '', $attribs = array())
	{
		$_db = JFactory::getDbo();
		$_db->setQuery("SELECT c.* ".
				"FROM #__adsmanager_positions as c ".
				"ORDER BY c.name ");
		$list = $_db->loadObjectList();
			
		$options[] = JHTML::_('select.option','','- '.sprintf(JText::_('ADSMANAGER_COMMON_SELECT'),JText::_('ADSMANAGER_POSITION')).' -');
		foreach($list as $item) {
			$options[] = JHTML::_('select.option',$item->id,$item->name." (".TText::_($item->title).")");
		}
	
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		return $html;
	}
	
	public static function categories($name = 'catid', $selected = '', $attribs = array())
	{
		require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/category.php');
		$model  = new AdsmanagerModelCategory();
		$list = $model->getFlatTree(false);
		foreach($list as $key => $cat) {
			$indent = "";
			for($i=0;$i< ($cat->level);$i++) {
				$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if ($cat->level > 0) {
				$indent .= 'L&nbsp;';
			}
			$list[$key]->treename = $indent.TText::_($cat->name);
		}
		$options[] = JHTML::_('select.option','','- '.sprintf(JText::_('ADSMANAGER_COMMON_SELECT'),JText::_('ADSMANAGER_CATEGORY')).' -');
		foreach($list as $item) {
			$options[] = JHTML::_('select.option',$item->id,$item->treename);
		}
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		return $html;
	}
	
	public static function fieldtypes($name = 'type', $selected = '', $attribs = array())
	{
		require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/field.php');
		$model  = new AdsmanagerModelField();
		$plugins = $model->getPlugins();
		
		$types = array();
		$types[] = JHTML::_('select.option','','- '.sprintf(JText::_('ADSMANAGER_COMMON_SELECT'),JText::_('ADSMANAGER_FIELDTYPE')).' -');
		$types[] = JHTML::_('select.option', 'checkbox', 'Check Box (Single)' );
		$types[] = JHTML::_('select.option', 'multicheckbox', 'Check Box (Multiple)' );
		$types[] = JHTML::_('select.option', 'multicheckboximage', 'Check Box (Multiple Images)' );
		$types[] = JHTML::_('select.option', 'date', 'Date' );
		$types[] = JHTML::_('select.option', 'select', 'Drop Down (Single Select)' );
		$types[] = JHTML::_('select.option', 'multiselect', 'Drop Down (Multi-Select)' );
		$types[] = JHTML::_('select.option', 'emailaddress', 'Email Address' );	
		$types[] = JHTML::_('select.option', 'number', 'Number Text' );	
		$types[] = JHTML::_('select.option', 'price', 'Price' );	
		$types[] = JHTML::_('select.option', 'editor', 'Editor Text Area' );
		$types[] = JHTML::_('select.option', 'textarea', 'Text Area' );
		$types[] = JHTML::_('select.option', 'text', 'Text Field' );
		$types[] = JHTML::_('select.option', 'url', 'URL' );
		$types[] = JHTML::_('select.option', 'radio', 'Radio Button' );
		$types[] = JHTMLSelect::option ('radioimage','Radio Button (Image)');
		$types[] = JHTML::_('select.option', 'file', 'File' );

		if(isset($plugins)) {
			foreach($plugins as $key => $plug)
			{
				$types[] = JHTML::_('select.option', $key, $plug->getFieldName() ); 
			}
		}
		$html = self::genericlist($types, $name, $attribs, $selected, $name);
		return $html;
	}
}
