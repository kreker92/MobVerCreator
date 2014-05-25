<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
$conf= $this->conf;
?>

<?php echo $this->content->event->onContentBeforeDisplay; ?>
<div class="<?php echo $classcontent;?> adsmanager_ads">
	<div class="adsmanager_top_ads">	
		<h2 class="adsmanager_ads_title">	
		<?php if (@$this->positions[0]->title) {$strtitle = JText::_($this->positions[0]->title);} ?>
		<?php echo "<b>".@$strtitle."</b>";
		if (isset($this->fDisplay[1]))
		{
			foreach($this->fDisplay[1] as $field)
			{
				$title = $this->field->showFieldTitle(@$this->content->catid,$field);
				if ($title != "")
					echo htmlspecialchars($title).": ";
				echo $this->field->showFieldValue($this->content,$field)."<br/>";
			}
		} ?>
		</h2>
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
		}
		?>
		</div>
		<div class="adsmanager_ads_kindof">
		<?php if (@$this->positions[1]->title) {$strtitle = JText::_($this->positions[1]->title);} ?>
		<?php echo "<b>".@$strtitle."</b>"; 
		if (isset($this->fDisplay[2]))
		{
			foreach($this->fDisplay[2] as $field)
			{
				$title = $this->field->showFieldTitle(@$this->content->catid,$field);
				if ($title != "")
					echo htmlspecialchars($title).": ";
				echo $this->field->showFieldValue($this->content,$field)."<br/>";
			}
		}
		?>
		</div>
	</div>
	<div class="adsmanager_ads_main">
		<div class="adsmanager_ads_body">
			<div class="adsmanager_ads_desc">
			<?php if (@$this->positions[2]->title) {$strtitle = JText::_($this->positions[2]->title);} ?>
			<?php echo "<b>".@$strtitle."</b>"; 
			if (isset($this->fDisplay[3]))
			{	
				foreach($this->fDisplay[3] as $field)
				{
					$title = $this->field->showFieldTitle(@$this->content->catid,$field);
					if ($title != "")
						echo htmlspecialchars($title).": ";
					echo $this->field->showFieldValue($this->content,$field)."<br/>";
				}
			} ?>
			</div>
			<div class="adsmanager_ads_desc">
			<?php if (@$this->positions[5]->title) {$strtitle = JText::_($this->positions[5]->title);} ?>
			<?php echo "<b>".@$strtitle."</b>"; 
			if (isset($this->fDisplay[6]))
			{	
				foreach($this->fDisplay[6] as $field)
				{
					$title = $this->field->showFieldTitle(@$this->content->catid,$field);
					if ($title != "")
						echo htmlspecialchars($title).": ";
					echo $this->field->showFieldValue($this->content,$field)."<br/>";
				}
			} ?>
			</div>
			<div class="adsmanager_ads_price">
			<?php if (@$this->positions[3]->title) {$strtitle = JText::_($this->positions[3]->title); } ?>
			<?php echo "<b>".@$strtitle."</b>"; 
			if (isset($this->fDisplay[4]))
			{
				foreach($this->fDisplay[4] as $field)
				{
					$title = $this->field->showFieldTitle(@$this->content->catid,$field);
					if ($title != "")
						echo htmlspecialchars($title).": ";
					echo $this->field->showFieldValue($this->content,$field)."<br/>";
				} 
			}?>
			</div>
			<div class="adsmanager_ads_contact">
			<?php if (@$this->positions[4]->title) {$strtitle = JText::_($this->positions[4]->title);} ?>
			<?php echo "<b>".@$strtitle."</b>"; 
			if (($this->userid != 0)||($conf->show_contact == 0)) {		
				if (isset($this->fDisplay[5]))
				{		
					foreach($this->fDisplay[5] as $field)
					{	
						$title = $this->field->showFieldTitle(@$this->content->catid,$field);
						if ($title != "")
							echo htmlspecialchars($title).": ";
						echo $this->field->showFieldValue($this->content,$field)."<br/>";
					} 
				}
				if (($this->content->userid != 0)&&($conf->allow_contact_by_pms == 1))
				{
					if ($conf->display_fullname == 1)
						$pmsText= sprintf(JText::_('ADSMANAGER_PMS_FORM'),$this->content->fullname);
					else
						$pmsText= sprintf(JText::_('ADSMANAGER_PMS_FORM'),$this->content->user);
					$pmsForm = TRoute::_("index.php?option=com_uddeim&task=new&recip=".$this->content->userid);
					echo '<a href="'.$pmsForm.'">'.$pmsText.'</a><br />';
				}
			}
			else
			{
				echo JText::_('ADSMANAGER_CONTACT_NOT_LOGGED');
			}
			?>
			</div>
	    </div>
		<div class="adsmanager_ads_image">
			<?php
			$this->loadScriptImage($this->conf->image_display);
			if (isset($this->content->images)) {
			$i=0;
			foreach($this->content->images as $image)
			{
				if ($i == 0) {
					$piclink 	= $image;
					$thumb = str_replace(".jpg","_t.jpg",$piclink);
					switch($this->conf->image_display)
					{
						case 'popup':
							echo "<a href=\"javascript:popup('$piclink');\"><img src='".$thumb."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>";
							break;
						case 'lightbox':
						case 'lytebox':
							echo "<a href='".$piclink."' rel='lytebox[roadtrip".$this->content->id."]'><img src='".$thumb."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>"; 
							break;
						case 'highslide':
							echo "<a id='thumb".$this->content->id."' class='highslide' onclick='return hs.expand (this)' href='".$piclink."'><img src='".$thumb."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>";
							break;
						case 'default':	
						default:
							echo "<a href='".$piclink."' target='_blank'><img src='".$thumb."' alt='".htmlspecialchars($this->content->ad_headline)."' /></a>";
							break;
					}
					$i=1;
				} else {	
					$i=0;
				}
			}
			}
			if (@count($this->content->images) == 0) 
			{
				echo '<img src="'.ADSMANAGER_NOPIC_IMG.'" alt="nopic" />'; 
			}
			?>
		</div>
		<div class="adsmanager_spacer"></div>
	</div>
</div>
<?php echo $this->content->event->onContentAfterDisplay; ?>
<div align='center'>
	<input type="button" class="button btn" onclick='window.location="<?php echo TRoute::_("index.php?option=com_adsmanager&view=edit&id=".$this->content->id); ?>"' value="<?php echo JText::_('ADSMANAGER_FORM_EDIT_TEXT'); ?>" />	
	&nbsp;
	<input type="button" class="button btn" onclick='window.location="<?php echo TRoute::_("index.php?option=com_adsmanager&task=valid&id=".$this->content->id); ?>"' value="<?php echo JText::_('ADSMANAGER_FORM_VALID_TEXT'); ?>" />
</div>