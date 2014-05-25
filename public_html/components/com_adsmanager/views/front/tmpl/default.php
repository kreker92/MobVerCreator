<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<?php if ($this->conf->display_last == 1)
{
	$this->displayContents($this->contents,$this->nbimages); 
}
?>
<h1 class="contentheading"><?php echo JText::_('ADSMANAGER_FRONT_TITLE'); ?></h1>
<div class="adsmanager_fronttext">
	<?php echo $this->conf->fronttext; ?>
</div>
<br />
<?php $this->general->showGeneralLink() ?>
<div id="adshome">
	<?php 
	$nbcatsperrow = $this->conf->display_nb_categories_per_row;
	$classtype = (int) (12 / $nbcatsperrow);
	
	$num = 1;
	$divopen = false;
	
	foreach ($this->cats as $row) {
		//remove the if if you want more than 1 sublevel
		if ($row->level > 1) {
			continue;
		}
		
		
		if ($row->level == 0) {
			if ($divopen == true) {
				$divopen = false;
				?>
							</h3>
						</div>
					</div>
				<?php }
			if ($num == $nbcatsperrow+1) {
				$num = 1;
				echo '</div>';
			}
			if ($num==1) {
				echo '<div class="row-fluid ads-row-fluid">';
			}
			$num++;
		}
		
		if(isset($this->conf->display_nb_ads_per_categories) && $this->conf->display_nb_ads_per_categories)
			$numAds = " (".$row->num_ads.")";
		else
			$numAds = '';
		
		$link = TRoute::_("index.php?option=com_adsmanager&view=list&catid=".$row->id);
			
		
		if ($row->level == 0) {
				?>
				<div class="span<?php echo $classtype ?> ads-span<?php echo $classtype ?>">
					<div class="span6 ads-span6">
					<?php echo '<a href="'.$link.'" title="'.htmlspecialchars($row->name).'"><img class="imgcat" src="'.TTools::getCatImageUrl($row->id).'" alt="'.htmlspecialchars($row->name).'" /></a>';
					?>
					</div>
					<div class="span6 ads-span6">
						<h2 class="adsmanager_main_cat"><a href="<?php echo $link; ?>"  ><?php echo htmlspecialchars($row->name).$numAds; ?></a></h2> 
						<h3 class="adsmanager_sub_cat">
			<?php	
			$divopen = true;
			$firstsubcat = true;
		} else {
			if ($firstsubcat == false)
				echo ' - ';
			echo '<a href="'.$link.'">'.htmlspecialchars($row->name).$numAds.'</a>';
			$firstsubcat = false;
		}
	}
	if ($divopen == true) {
		?>
			</h3>
		</div>
	</div>
	<?php 
	}
	echo "</div>";
	?>
</div>
<?php if ($this->conf->display_last == 2)
{
	$this->displayContents($this->contents,$this->nbimages); 
} $this->general->endTemplate();