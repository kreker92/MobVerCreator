<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<table class="adsmanager_table">
	<tr>
		<th><?php echo JText::_('ADSMANAGER_CONTENT'); ?></th>
		<?php 
		foreach($columns as $col)
		{
			echo "<th>".JText::_($col->name)."</th>";
		}
		?>
		<th><?php echo JText::_('ADSMANAGER_DATE'); ?></th>
	</tr>
<?php
	foreach($contents as $content) 
	{
		$linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
		if (function_exists('getContentClass')) 
			$classcontent = getContentClass($content,"list");
  	    else
			$classcontent = "adsmanager_table_description";
		?>   
	<tr class="<?php echo $classcontent;?>"> 
		<td class="column_desc">
			<?php
			if (isset($content->images[0])) {
				echo "<a href='".$linkTarget."'><img class='adimage' name='adimage".$content->id."' src='".JURI::base()."images/com_adsmanager/ads/".$content->images[0]->thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>";
			} else {
				echo "<a href='".$linkTarget."'><img class='adimage' src='".ADSMANAGER_NOPIC_IMG."' alt='nopic' /></a>";
			}
			?>
			<div>
			<h2>
				<?php echo '<a href="'.$linkTarget.'">'.$content->ad_headline.'</a>'; ?>
				<span class="adsmanager_cat"><?php echo "(".$content->parent." / ".$content->cat.")"; ?></span>
			</h2>
			<?php 
				$content->ad_text = str_replace ('<br />'," ",$content->ad_text);
				$af_text = JString::substr($content->ad_text, 0, 100)."...";
				echo $af_text;
			?>
			</div>
			
			<?php 
			if (($userid == $content->userid)&&($content->userid != 0))	{
			?>
			<div>
			<?php
				$target = TRoute::_("index.php?option=com_adsmanager&task=write&catid=".$content->catid."&id=$content->id");
				echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_EDIT')."</a>";
				echo "&nbsp;";
				$target = TRoute::_("index.php?option=com_adsmanager&task=delete&catid=".$content->catid."&id=$content->id");
				echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_DELETE')."</a>";
			?>
			</div>
			<?php
			}
			?>			
		</td>
		<?php 
			foreach($columns as $col) {
				echo '<td class="tdcenter column_'.$col->id.'">';
				if (isset($fColumns[$col->id]))
					foreach($fColumns[$col->id] as $f)
					{
						$c = $field->showFieldValue($content,$f);
						if ($c != "") {
							$title = $field->showFieldTitle(@$content->catid,$f);
							if ($title != "")
								echo htmlspecialchars($title).": ";
							echo "$c<br/>";
						}
					}
				echo "</td>";
			}
		?>
		<td class="tdcenter column_date">
			<?php 
			$iconflag = false;
			if (($conf->show_new == true)&&(isNewcontent($content->date_created,$conf->nbdays_new))) {
				echo "<div class='center'><img align='center' src='".$baseurl."/components/com_adsmanager/images/new.gif' /> ";
				$iconflag = true;
			}
			if (($conf->show_hot == true)&&($content->views >= $conf->nbhits)) {
				if ($iconflag == false)
					echo "<div class='center'>";
				echo "<img align='center' src='".$baseurl."/components/com_adsmanager/images/hot.gif' />";
				$iconflag = true;
			}
			if ($iconflag == true)
				echo "</div>";
			echo reorderDate($content->date_created); 
			?>
			<br />
			<?php
			if ($content->userid != 0)
			{
			   echo JText::_('ADSMANAGER_FROM')." "; 
			   $target = TLink::getUserAdsLink($content->userid);
			   echo "<a href='".$target."'>".$content->user."</a><br/>";
			}
			?>
			<?php echo sprintf(JText::_('ADSMANAGER_VIEWS'),$content->views); ?>
		</td>
	</tr>
<?php	
}
?>
</table>