<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
//include (JPATH_ROOT.'/libraries/joomla/html/html/access.php');
include_once (JPATH_ROOT.'/components/com_adsmanager/helpers/usergroups.php');
?>
<script type="text/javascript">
<?php if(version_compare(JVERSION,'1.6.0','>=')){ ?>
Joomla.submitbutton = function(pressbutton) {
<?php } else { ?>
function submitbutton(pressbutton) {
<?php } ?>
	   $editor = JFactory::getEditor(); 
	   echo $editor->save( 'description' );
       submitform(pressbutton);
   }
</script>
<?php JText::_('ADSMANAGER_CATEGORY_EDITION'); ?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_TITLE'); ?></td>
<td><input type="text" size="50"  name="name" value="<?php echo @$this->row->name; ?>" /></td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_PARENT'); ?></td>
<td>
<select name="parent" id="parent">
<option value="0"><?php echo JText::_('ADSMANAGER_ROOT'); ?></option>
<?php $this->selectCategories(0,"Root >> ",$this->cats,@$this->row->parent,@$this->row->id); ?>
</select>
</td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_IMAGE'); ?></td>
<td>
<input type="file" name="cat_image"/>
<?php 
   $a_pic = JPATH_ROOT."/images/com_adsmanager/categories/".@$this->row->id."cat.jpg";
   if (file_exists($a_pic)) 
   {
     echo '<img src="../images/com_adsmanager/categories/'.@$this->row->id.'cat.jpg?time='.time().'"/>';
     echo "<input type='checkbox' name='cb_image' value='delete'>".JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE');
   }
?>
</td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_DESCRIPTION'); ?></td>
<td>
<?php
$editor = JFactory::getEditor();
echo $editor->display('description', @$this->row->description, '100%','350', 75, 20);
?>				
</td>
</tr>

<?php if ($this->config->metadata_mode != 'nometadata') { ?>
<tr>
<td><?php echo JText::_('ADSMANAGER_METADATA_DESCRIPTION'); ?></td>
<td>
<textarea cols="50" rows="10" name="metadata_description"><?php echo htmlspecialchars(@$this->row->metadata_description)?></textarea>			
</td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_METADATA_KEYWORDS'); ?></td>
<td>
<textarea cols="50" rows="10" name="metadata_keywords"><?php echo htmlspecialchars(@$this->row->metadata_keywords)?></textarea>			
</td>
</tr>

<?php } ?>
<?php if(1 == 1){ ?>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_LIMIT_ADS'); ?></td>
<td>
	<?php $limitads = (@$this->row->limitads != "") ? $this->row->limitads : -1;?>
    <input type="text" size="50"  name="limitads" value="<?php echo $limitads ?>" />
</td>
</tr>
<?php } ?>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_PUBLISH'); ?></td>
<td>
<select name="published" id="published">
<option value="1" <?php if (@$this->row->published == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_PUBLISH'); ?></option>
<option value="0" <?php if (@$this->row->published == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO_PUBLISH') ?></option>
</select>
</td>
</tr>
<?php if(version_compare(JVERSION, '1.6', 'ge')) { ?>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_ACL_READ'); ?></td>
<td>
<?php
    echo JHTMLAdsmanagerUserGroups::getUserGroups('usergroupsread[]', empty($this->row->usergroupsread) ? '-1' : explode(',', $this->row->usergroupsread), array('multiple' => 'multiple', 'size' => 10));
?>
</td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_ACL_WRITE'); ?></td>
<td>
<?php
    echo JHTMLAdsmanagerUserGroups::getUserGroups('usergroupswrite[]', empty($this->row->usergroupswrite) ? '-1' : explode(',', $this->row->usergroupswrite), array('multiple' => 'multiple', 'size' => 10));
?>
</td>
</tr>
<?php } ?>
</table>
<input type="hidden" name="id" value="<?php echo @$this->row->id; ?>" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="c" value="categories" />
<input type="hidden" name="task" value="" />
</form>