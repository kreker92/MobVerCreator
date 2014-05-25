<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($type != "horizontal") { 
if(version_compare(JVERSION, '3.0', '>=')) {
?>
<style>
.adsmanager_search_module select {
	max-width:180px;
	min-width:180px;
	width:180px;
}
.adsmanager_search_module input[type="text"] {
	max-width:166px;
	min-width:166px;
	width:166px;
}
</style>
<?php
} 
}
?>
<?php $link = TRoute::_("index.php?option=com_adsmanager&view=result"); ?>
<div class="adsmanager_search_module">
<form action="<?php echo $link; ?>" method="post">
<?php if ($search_by_text == 1){ ?>
<input class="inputbox" type="text" name="tsearch" value="<?php echo $text_search; ?>" />
<?php } ?>
<?php if ($type != "horizontal") { ?>
<br/>
<?php } ?>
<?php if ($search_by_cat == 1)
{
	if ($type == "horizontal") { ?>
	<span class='mod_adsmanager_search_cats'>
<?php } else { ?>
	<div class='mod_adsmanager_search_cats'>
<?php }?>
	<?php 
	switch(@$conf->single_category_selection_type) {
		default:
		case 'normal':
			JHTMLAdsmanagerCategory::displayNormalCategories("catid",$cats,$catid);break;
		case 'color':
			JHTMLAdsmanagerCategory::displayColorCategories("catid",$cats,$catid);break;
		case 'combobox':
			JHTMLAdsmanagerCategory::displayComboboxCategories("catid",$cats,$catid);break;
			break;
		case 'cascade':
			if ($type == "horizontal") 
				$separator = "";
			else
				$separator = "<br/>";
			JHTMLAdsmanagerCategory::displaySplitCategories("catid",$cats,$catid,array('separator'=>$separator));break;
	}
	?>
<?php if ($type == "horizontal") { ?>	
	</span>
<?php } else { ?>
	</div>
<?php } 
}
if (isset($searchfields)) {
if ($type == "table")
	echo "<table width='100%' border='0'>";
foreach($searchfields as $fsearch) {
	if (($catid == 0)||(strpos($fsearch->catsid, ",$catid,") !== false)||(strpos($fsearch->catsid, ",-1,") !== false))
	{
		$currentvalue = JRequest::getVar($fsearch->name, "" );
		if ($type == "table") {
			echo "<tr><td>";
			$title = $field->showFieldTitle($catid,$fsearch);
			echo htmlspecialchars($title);
			echo "</td><td>";
			$field->showFieldSearch($fsearch,$catid,$defaultvalues);
			echo "</td></tr>";
		}
		else if ($type == "div") {
			echo "<div class='mod_adsmanager_search_field'>";
			$title = $field->showFieldTitle($catid,$fsearch);
			echo htmlspecialchars($title)."&nbsp;";
			$field->showFieldSearch($fsearch,$catid,$defaultvalues);
			echo "</div>";
		} else if ($type == "horizontal") {
			echo "<span>";
			$title = $field->showFieldTitle($catid,$fsearch);
			echo htmlspecialchars($title)."&nbsp;";
			$field->showFieldSearch($fsearch,$catid,$defaultvalues);
			echo "</span>";
			echo "&nbsp;";	
		}	
	}
}
if ($type == "table")
	echo "</table>";
}?>

<input type="hidden" value="1" name="new_search" />
<input type="submit" class="button btn" value="<?php echo JText::_('ADSMANAGER_SEARCH_BUTTON'); ?>"/>

<?php if ($advanced_search == 1)
{
?>
<?php if ($type != "horizontal") { ?> <div> <?php } else { ?></span><?php } ?>
<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=search&catid=$catid");?>"><?php echo JText::_('ADSMANAGER_ADVANCED_SEARCH'); ?></a>
<?php if ($type != "horizontal") { ?> </div> <?php } else { ?></span><?php } ?>
<?php
}
?>
</form>
</div>