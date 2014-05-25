<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<?php
if ($image == 1)
{
?>
	<ul class="adsmanager_ver_list">
	<?php
	if(isset($contents[0])) {
	foreach($contents as $row) {
	?>
		<li>
		<?php	
		$linkTarget = TRoute::_("index.php?option=com_adsmanager&view=details&id=".$row->id."&catid=".$row->catid);			

		if (isset($row->images[0])) {
			echo "<div class='center'><div class='center adimg'><a href='".$linkTarget."'><img src='".$baseurl."images/com_adsmanager/ads/".$row->images[0]->$imagetype."' alt='".htmlspecialchars($row->ad_headline)."' border='0' /></a></div>";
		}
		else
		{
			echo "<div class='center'><div class='center adimg'><a href='".$linkTarget."'><img src='".ADSMANAGER_NOPIC_IMG."' alt='noimage' border='0' /></a></div>"; 
		}   
		echo "<div class='center'><a href='$linkTarget'>".$row->ad_headline."</a></div>"; 
		$iconflag = false;
		if (($conf->show_new == true)&&(isNewContent($row->date_created,$conf->nbdays_new))) {
			echo "<div class='center'><img align='center' src='".$baseurl."components/com_adsmanager/images/new.gif' /> ";
			$iconflag = true;
		}
		if (($conf->show_hot == true)&&($row->views >= $conf->nbhits)) {
			if ($iconflag == false)
				echo "<div class='center'>";
			echo "<img align='center' src='".$baseurl."components/com_adsmanager/images/hot.gif' />";
			$iconflag = true;
		}
		if ($iconflag == true)
			echo "</div>";
			
		if ($displaycategory == 1)
		{
			echo "<div class='center '><span class=\"adsmanager_cat\">(".$row->parent." / ".$row->cat.")</span></div>";
		}
		if ($displaydate == 1)
		{
			echo "<div class='center addate'>".reorderDate($row->date_created)."</div>";
			$iconflag = true;
		}
		foreach($adfields as $f) {
			$fieldname = $f->name;
			if ($row->$fieldname != null) {
				$value = $field->showFieldValue($row,$f);
				echo "<div class='center adfield'>$value</div>";
			}
		}
		echo "</div>";
		?>
		</li>
<?php
	} }
	?>
	</ul>
	<?php
}
else
{
	?>
	<ul class="mostread">
	<?php
	if (isset($contents[0])){
	foreach($contents as $row) {
	?>
		<li class="mostread">
		<?php	
			$linkTarget = TRoute::_("index.php?option=com_adsmanager&view=details&id=".$row->id."&catid=".$row->catid);
			echo "<a href='$linkTarget'>".$row->ad_headline."</a>";
			if ($displaycategory == 1)
				echo "&nbsp;<span class=\"adsmanager_cat\">(".$row->parent." / ".$row->cat.")";
			if ($displaydate == 1)
				echo "&nbsp;".reorderDate($row->date_created)."</span>"; 
		?>
		</li>
<?php
	}}
	?>
	</ul>
	<?php
}	