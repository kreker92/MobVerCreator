<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/JG/trunk/administrator/components/com_joomgallery/models/fields/cbcategory.php $
// $Id: cbcategory.php 4076 2013-02-12 10:35:29Z erftralle $
/****************************************************************************************\
**   JoomGallery 3                                                                      **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2013  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('joomcategory');

/**
 * Renders a category select box form field with checkbox in front of the label
 *
 * @package JoomGallery
 * @since   2.0
 */
class JFormFieldCbcategory extends JFormFieldJoomCategory
{
  /**
   * The form field type
   *
   * @var     string
   * @since   2.0
   */
  protected $type = 'Cbcategory';

  /**
   * Method to get the field label markup
   *
   * @return  string  The field label markup
   * @since   2.0
   */
  protected function getLabel()
  {
    $label = '';

    $cbname     = $this->element['cbname'] ? $this->element['cbname'] : 'change[]';
    $cbvalue    = $this->element['cbvalue'] ? $this->element['cbvalue'] : $this->name;
    $validate   = $this->element['validate'] ? (string) $this->element['validate'] : '';
    $cbid       = str_replace(array('[', ']'), array('', ''), $cbname.$cbvalue);

    $cbonclick = '';
    if(!empty($validate))
    {
      $cbonclick  = "if($('".$cbid."').checked) { $('".$this->id."').addClass('validate-".$validate."');} else { $('".$this->id."').removeClass('validate-".$validate."');}";

      $js = "
        window.addEvent('domready', function() {
          ".$cbonclick."
        });";
      $doc = JFactory::getDocument();
      $doc->addScriptDeclaration($js);
    }
    $cbhtml     = '<input id="'.$cbid.'" type="checkbox" onclick="'.$cbonclick.'" name="'.$cbname.'" value="'.$cbvalue.'" />';

    $label      = parent::getLabel();
    $insertpos  = strpos($label, '>');
    $label      = substr_replace($label, $cbhtml, $insertpos + 1, 0);

    return $label;
  }
}