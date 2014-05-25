<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

$conf= $this->conf;

$document	= JFactory::getDocument();
if ($conf->metadata_mode != 'nometadata') {
	$document->setMetaData("description", $this->content->metadata_description);
	$document->setMetaData("keywords", $this->content->metadata_keywords);
}
?>
<?php if ($conf->display_inner_pathway == 1) { ?>
<div class="adsmanager_pathway breadcrumb">
<?php 
	$pathway ="";
	$nb = count($this->pathlist);
	for ($i = $nb - 1 ; $i >0;$i--)
	{
		$pathway .= '<a href="'.$this->pathlist[$i]->link.'">'.$this->pathlist[$i]->text.'</a>';
		$pathway .= ' <img src="'.$this->baseurl.'components/com_adsmanager/images/arrow.png" alt="arrow" /> ';
	}
	$pathway .= '<a href="'.$this->pathlist[0]->link.'">'.$this->pathlist[0]->text.'</a>';
echo $pathway;

if (function_exists('getContentClass')) 
	$classcontent = getContentClass($this->content,"details");
else
	$classcontent = "";
?>   
</div>
<?php } ?>
<?php echo $this->content->event->onContentBeforeDisplay; ?>
<?php if (@$conf->print==1) {?>
<div align='right'>
<?php if (JRequest::getInt('print',0) == 1) {
	echo TTools::print_screen();
} else {
	$url = "index.php?option=com_adsmanager&view=details&catid=".$this->content->catid."&id=".$this->content->id;
	echo TTools::print_popup($url); 
}?>
</div>
<?php } ?>
<div class="<?php echo $classcontent;?> addetails">	
		<h1>	
		<?php 
		if (isset($this->fDisplay[1]))
		{
			foreach($this->fDisplay[1] as $field)
			{
				$c = $this->field->showFieldValue($this->content,$field); 
				if (($c !== "")&&($c !== null)) {
					$title = $this->field->showFieldTitle(@$this->content->catid,$field);
					if ($title != "")
						echo "<b>".htmlspecialchars($title)."</b>: ";
					echo "$c ";
				}
			}
		} ?>
		</h1>
		<?php echo $this->content->event->onContentAfterTitle; ?>
		<div>
		<?php 
		if ($this->content->userid != 0)
		{
			echo JText::_('ADSMANAGER_SHOW_OTHERS'); 
			$target = TLink::getUserAdsLink($this->content->userid);
			
		    if ($conf->display_fullname == 1)
				echo "<a href='$target'><b>".$this->content->fullname."</b></a>";
			else
				echo "<a href='$target'><b>".$this->content->user."</b></a>";
			
			if ($this->userid == $this->content->userid)	{
			?>
			<div>
			<?php
				$target = TRoute::_("index.php?option=com_adsmanager&task=write&catid=".$this->content->category."&id=".$this->content->id);
				echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_EDIT')."</a>";
				echo "&nbsp;";
				$target = TRoute::_("index.php?option=com_adsmanager&task=delete&catid=".$this->content->category."&id=".$this->content->id);
				echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_DELETE')."</a>";
			?>
			</div>
			<?php
			}
		}
		?>
		</div>
		<div class="addetails_topright">
		<?php $strtitle = "";if (@$this->positions[3]->title) {$strtitle = JText::_($this->positions[3]->title); } ?>
			<?php if (@$strtitle != "") echo "<h2>".@$strtitle."</h2>"; 
			if (isset($this->fDisplay[4]))
			{
				foreach($this->fDisplay[4] as $field)
				{
					$c = $this->field->showFieldValue($this->content,$field); 
					if (($c !== "")&&($c !== null)) {
						$title = $this->field->showFieldTitle(@$this->content->catid,$field);
						if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
					}
				} 
			}?>
		</div>
	<div class="addetailsmain">
		<div class="adsmanager_ads_image">
			<?php
			$this->loadScriptImage($this->conf->image_display);
			if (count($this->content->images) == 0)
				$image_found = 0;
			else
				$image_found = 1;
			foreach($this->content->images as $img)
			{
				$thumbnail = JURI::base()."images/com_adsmanager/ads/".$img->thumbnail;
				$image = JURI::base()."images/com_adsmanager/ads/".$img->image;
				switch($this->conf->image_display)
			    {
					case 'popup':
						echo "<a href=\"javascript:popup('$image');\"><img src='".$thumbnail."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>";
						break;
					case 'lightbox':
					case 'lytebox':
						echo "<a href='".$image."' rel='lytebox[roadtrip".$this->content->id."]'><img src='".$thumbnail."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>"; 
						break;
					case 'highslide':
						echo "<a id='thumb".$this->content->id."' class='highslide' onclick='return hs.expand (this)' href='".$image."'><img src='".$thumbnail."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>";
						break;
					case 'default':	
					default:
						echo "<a href='".$image."' target='_blank'><img src='".$thumbnail."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>";
						break;
				}
			}
			if (($image_found == 0)&&($conf->nb_images >  0))
			{
				echo '<img src="'.ADSMANAGER_NOPIC_IMG.'" alt="nopic" />'; 
			}
			?>
		</div>
		<div class="adsmanager_ads_body">
			<div class="adsmanager_ads_desc">
			<?php $strtitle = "";if (@$this->positions[2]->title) {$strtitle = JText::_($this->positions[2]->title);} ?>
			<?php echo "<h2>".@$strtitle."</h2>"; 
			if (isset($this->fDisplay[3]))
			{	
				foreach($this->fDisplay[3] as $field)
				{
					$c = $this->field->showFieldValue($this->content,$field); 
					if (($c !== "")&&($c !== null)) {
						$title = $this->field->showFieldTitle(@$this->content->catid,$field);
						if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
					}
				}
			} ?>
			</div>
			<div class="adsmanager_ads_price">
			<?php $strtitle = "";if (@$this->positions[1]->title) {$strtitle = JText::_($this->positions[1]->title);} ?>
			<?php echo "<b>".@$strtitle."</b>"; 
			if (isset($this->fDisplay[2]))
			{
				foreach($this->fDisplay[2] as $field)
				{
					$c = $this->field->showFieldValue($this->content,$field); 
					if (($c !== "")&&($c !== null)) {
						$title = $this->field->showFieldTitle(@$this->content->catid,$field);
						if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
					}
				}
			}
			?>
			</div>
			<div class="adsmanager_ads_desc">
			<?php $strtitle = "";if (@$this->positions[5]->title) {$strtitle = JText::_($this->positions[5]->title);} ?>
			<?php echo "<h2>".@$strtitle."</h2>"; 
			if (isset($this->fDisplay[6]))
			{	
				foreach($this->fDisplay[6] as $field)
				{
					$c = $this->field->showFieldValue($this->content,$field); 
					if (($c !== "")&&($c !== null)) {
						$title = $this->field->showFieldTitle(@$this->content->catid,$field);
						if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
					}
				}
			} ?>
			</div>
			<div class="adsmanager_ads_contact">
			<?php $strtitle = "";if (@$this->positions[4]->title) {$strtitle = JText::_($this->positions[4]->title);} ?>
			<?php echo "<h2>".@$strtitle."</h2>"; 
			if (($this->userid != 0)||($conf->show_contact == 0)) {		
				if (isset($this->fDisplay[5]))
				{		
					foreach($this->fDisplay[5] as $field)
					{	
						$c = $this->field->showFieldValue($this->content,$field); 
						if(($c !== "")&&($c !== null)) {
							$title = $this->field->showFieldTitle(@$this->content->catid,$field);
							if ($title != "")
								echo "<b>".htmlspecialchars($title)."</b>: ";
							echo "$c<br/>";
						}
					} 
				}
				if (($this->content->userid != 0)&&($conf->allow_contact_by_pms == 1))
				{
					echo TLink::getPMSLink($this->content);
				}
			}
			else
			{
				echo JText::_('ADSMANAGER_CONTACT_NOT_LOGGED');
			}
			?>
			</div>
	    </div>
		<div class="adsmanager_spacer"></div>
	</div>
</div>
<?php echo $this->content->event->onContentAfterDisplay; ?>
<div class="back_button">
<a href='javascript:history.go(-1)'>
<?php echo JText::_('ADSMANAGER_BACK_TEXT'); ?>
</a>
</div>