<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<script type="text/javascript">
function tableOrdering( order, dir, task )
{
        var form = document.adminForm;
 
        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>
<?php
$conf= $this->conf;

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
		if (isset($this->pathlist[0]))
			$pathway .= '<a href="'.$this->pathlist[0]->link.'">'.$this->pathlist[0]->text.'</a>';
	echo $pathway;
	?>
	</div>
<?php } ?>

<h1 class="contentheading">
<?php
	if ($this->list_img != "") {
		echo '<img  class="imgheading" src="'.$this->list_img.'" alt="'.$this->list_img.'" />';
	}
	echo JText::_($this->list_name);
	if ($this->conf->show_rss == 1)
	{
		if (isset($this->listuser))
			$linkrss = TRoute::_("index.php?option=com_adsmanager&view=list&format=feed&user=".$this->listuser);
		else
			$linkrss = TRoute::_("index.php?option=com_adsmanager&view=list&format=feed&catid=".$this->catid);
		echo '<a href="'.$linkrss.'" target="_blank"><img class="imgheading" src="'.$this->baseurl.'components/com_adsmanager/images/rss.png" alt="rss" /></a>';
	}
?>
</h1>

<div class="adsmanager_subcats">
<?php foreach($this->subcats as $key => $subcat) {
	$subcat->link = TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$subcat->id);
	if ($key != 0)
		echo ' | ';
	echo '<a href="'.$subcat->link.'">'.$subcat->name.'</a>';
} 
?>
</div>
<div class="adsmanager_description">
<?php echo $this->list_description; ?>
</div>
<script type="text/JavaScript">
function jumpmenu(target,obj){
  eval(target+".location='"+obj.options[obj.selectedIndex].value+"'");	
  obj.options[obj.selectedIndex].innerHTML="<?php echo JText::_('ADSMANAGER_WAIT');?>";			
}		

jQ(function() {
	jQ('#order').change(function() {
		order = jQ(this).val();
		orderdir = jQ(":selected",this).attr('dir');
		var form= document.createElement('form');
        form.method= 'post';
        <?php if ($this->catid != 0) { ?>
        form.action= '<?php echo TRoute::_("index.php?option=com_adsmanager&view=list&catid=".$this->catid) ?>';  
		<?php } else if ($this->modeuser == 1) {?>
		form.action= '<?php echo TRoute::_("index.php?option=com_adsmanager&view=list&user=".$this->listuser) ?>';  
		<?php } else  {?>
		form.action= '<?php echo TRoute::_("index.php?option=com_adsmanager&view=list") ?>';  
		<?php } ?>  
        var input= document.createElement('input');
        input.type= 'hidden';
        input.name= "order";
        input.value= order;
        form.appendChild(input);
        var input2= document.createElement('input');
        input2.type= 'hidden';
        input2.name= "orderdir";
        input2.value= orderdir;
        form.appendChild(input2);
    	document.body.appendChild(form);
    	form.submit();
	});
});
</script>

<?php if (($conf->display_list_sort == 1)||($conf->display_list_search == 1)) { ?>
<div class="adsmanager_search_box">
<div class="adsmanager_inner_box">
	<?php if ($conf->display_list_search == 1) { ?>
		<?php if ($this->catid != 0) { ?>
		<form action="<?php echo TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$this->catid) ?>" method="post">
		<?php } else if ($this->modeuser == 1) {?>
		<form action="<?php echo TRoute::_('index.php?option=com_adsmanager&view=list&user='.$this->listuser) ?>" method="post">
		<?php } else  {?>
		<form action="<?php echo TRoute::_('index.php?option=com_adsmanager&view=list') ?>" method="post">
		<?php } ?>
		<div align="left">
			<input name="tsearch" id="tsearch" maxlength="20" alt="search" class="inputbox" type="text" size="20" value="<?php echo $this->tsearch;?>"  onblur="if(this.value=='') this.value='';" onfocus="if(this.value=='<?php echo $this->tsearch;?>') this.value='';" />
		</div>
		<div align="left">
			<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=search&catid=".$this->catid);?>"><?php echo JText::_('ADSMANAGER_ADVANCED_SEARCH'); ?></a>
		</div>
		</form> 
	<?php } ?>
	<?php if ($conf->display_list_sort == 1) { ?>
		<?php 
		 if (($this->catid != 0)&&($this->catid != -1)) { 
			$urloptions = "&catid=".$this->catid; 
		 } else if ($this->modeuser == 1) {
			$urloptions = "&user=".$this->listuser;
		 } else  {
		 	$urloptions = "";
		 } ?>
		<?php if (isset($this->orders)) { ?>
		<?php echo JText::_('ADSMANAGER_ORDER_BY_TEXT'); ?>
		<select name="order" size="1" id="order">
				<option value="0" dir="DESC" <?php if ($this->order == "0") { echo "selected='selected'"; } ?>><?php echo JText::_('ADSMANAGER_DATE'); ?></option>
			   <?php foreach($this->orders as $o)
			   {
	               ?>
				<option value="<?php echo $o->fieldid ?>" dir="DESC" <?php if (($this->orderdir == "DESC") && ($this->order == $o->fieldid)) { echo "selected='selected'"; } ?>><?php echo sprintf(JText::_('ADSMANAGER_ORDER_BY_DESC'),JText::_($o->title))?></option>
				<option value="<?php echo $o->fieldid ?>" dir="ASC" <?php if (($this->orderdir == "ASC") && ($this->order == $o->fieldid)) { echo "selected='selected'"; } ?>><?php echo sprintf(JText::_('ADSMANAGER_ORDER_BY_ASC'),JText::_($o->title))?></option>
				<?php
			   }
			 ?>
		</select>	
		<?php } ?>	
	<?php } ?>		  
</div>
</div>
<?php } ?>
<?php $this->general->showGeneralLink() ?>
<?php
if ($this->pagination->total == 0 ) 
{
	echo JText::_('ADSMANAGER_NOENTRIES'); 
}
else
{
	echo $this->pagination->total;
	?>
	<?php 
        echo $this->pagination->getResultsCounter();
        
        if(@$conf->display_map_list == 1){
            echo TTools::loadModule('mod_adsmanager_adsmap', 'AdsMap');
        }
    ?>
	<br/><br/>
	<form name="adminForm" id="adminForm" method="post" action="<?php echo $this->requestURL; ?>" >
	<input type="hidden" id="mode" name="mode" value="<?php echo $this->mode?>"/>
	</form>
	<?php if ($this->conf->display_expand == 2) { ?>
		<script type="text/javascript">
		function changeMode(mode)
		{
			element = document.getElementById("mode");
			element.value = mode;
			form = document.getElementById("adminForm");
			form.submit();
		}
		</script>
		<div class="adsmanager_subtitle">
		<?php 
		/* Display SubTitle */
			echo '<a href="javascript:changeMode(0)">'.JText::_('ADSMANAGER_MODE_TEXT')." ".JText::_('ADSMANAGER_SHORT_TEXT').'</a>';
		    echo " / ";
		    echo '<a href="javascript:changeMode(1)">'.JText::_('ADSMANAGER_EXPAND_TEXT').'</a>';
		?>
		</div>
	<?php } ?>
	<?php if ($this->mode != 1) { ?>
		<table class="adsmanager_table table table-striped">
			<tr>
			  <th><?php echo JText::_('ADSMANAGER_CONTENT'); ?>
			  <?php /*<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=5&orderdir=ASC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_asc.png" alt="+" /></a>
			  <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=5&orderdir=DESC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_desc.png" alt="-" /></a>
			   */?>
			  </th>
			  <?php 
			  	  foreach($this->columns as $col)
				  {
					echo "<th class='hidden-phone'>".JText::_($col->name);
					/*$order = @$this->fColumns[$col->id][0]->fieldid;
					?>
					<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=$order&orderdir=ASC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_asc.png" alt="+" /></a>
				    <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=$order&orderdir=DESC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_desc.png" alt="-" /></a>
				    */?>
                    <?php echo "</th>";
				  }
			  ?>
			  <th class="hidden-phone"><?php echo JText::_('ADSMANAGER_DATE'); ?>
			  <?php /*<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=orderdir=ASC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_asc.png" alt="+" /></a>
			  <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=orderdir=DESC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_desc.png" alt="-" /></a>
			  */?>
              </th>
			</tr>
		<?php
		foreach($this->contents as $content) 
		{
			$linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
			if (function_exists('getContentClass')) 
				$classcontent = getContentClass($content,"list");
	  	    else
				$classcontent = "";
			?>   
			<tr class="adsmanager_table_description <?php echo $classcontent;?> trcategory_<?php echo $content->catid?>"> 
				<td class="column_desc">
					<?php
					if (isset($content->images[0])) {
						echo "<a href='".$linkTarget."'><img class='adimage' name='adimage".$content->id."' src='".$this->baseurl."images/com_adsmanager/ads/".$content->images[0]->thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>";
					} else if ($this->conf->nb_images > 0) {
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
						$af_text = JString::substr($content->ad_text, 0, 100);
						if (strlen($content->ad_text)>100) {
							$af_text .= "[...]";
						}
						echo $af_text;
					?>
					</div>
				</td>
				<?php 
					foreach($this->columns as $col) {
						echo '<td class="tdcenter column_'.$col->id.' hidden-phone">';
						if (isset($this->fColumns[$col->id]))
							foreach($this->fColumns[$col->id] as $field)
							{
								$c = $this->field->showFieldValue($content,$field); 
								if (($c !== "")&&($c !== null)) {
									$title = $this->field->showFieldTitle(@$content->catid,$field);
									if ($title != "")
										echo "<b>".htmlspecialchars($title)."</b>: ";
									echo "$c<br/>";
								}
							}
						echo "</td>";
					}
				?>
				<td class="tdcenter column_date hidden-phone">
					<?php 
					$iconflag = false;
					if (($conf->show_new == true)&&($this->isNewcontent($content->date_created,$conf->nbdays_new))) {
						echo "<div class='center'><img alt='new' src='".$this->baseurl."components/com_adsmanager/images/new.gif' /> ";
						$iconflag = true;
					}
					if (($conf->show_hot == true)&&($content->views >= $conf->nbhits)) {
						if ($iconflag == false)
							echo "<div class='center'>";
						echo "<img alt='hot' src='".$this->baseurl."components/com_adsmanager/images/hot.gif' />";
						$iconflag = true;
					}
					if ($iconflag == true)
						echo "</div>";
					echo $this->reorderDate($content->date_created); 
					?>
					<br />
					<?php
					if ($content->userid != 0)
					{
					   echo JText::_('ADSMANAGER_FROM')." "; 
	
					   $target = TLink::getUserAdsLink($content->userid);
					   
					   if ($conf->display_fullname == 1)
					   		echo "<a href='".$target."'>".$content->fullname."</a><br/>";
					   else
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
	<?php } else { ?>
		<?php foreach($this->contents as $key => $content) 
		{ 
			if ($key == 0)
				$this->loadScriptImage($this->conf->image_display);
			if (function_exists('getContentClass')) 
				$classcontent = getContentClass($content,"details");
	  	    else
				$classcontent = "";
			?>   
			<br/>
			<div class="<?php echo $classcontent?> adsmanager_ads">
			<div class="adsmanager_top_ads">	
				<h2>
				<?php 	
				if (isset($this->fDisplay[1]))
				{
					foreach($this->fDisplay[1] as $field)
					{
						$c = $this->field->showFieldValue($content,$field); 
						if (($c !== "")&&($c !== null)) {
							$title = $this->field->showFieldTitle(@$content->catid,$field);
							if ($title != "")
								echo "<b>".htmlspecialchars($title)."</b>: ";
							echo "$c ";
						}
					}
				} ?>
				</h2>
				<div>
				<?php 
				if ($content->userid != 0)
				{
					echo JText::_('ADSMANAGER_SHOW_OTHERS'); 
					$target = TLink::getUserAdsLink($content->userid);
				    
				    if ($conf->display_fullname == 1)
						echo "<a href='$target'><b>".$content->fullname."</b></a>";
					else
						echo "<a href='$target'><b>".$content->user."</b></a>";
				}
				?>
				</div>
				<div class="addetails_topright">
				<?php $strtitle = "";if (@$this->positions[3]->title) {$strtitle = JText::_($this->positions[3]->title);} ?>
				<?php echo "<h3>".@$strtitle."</h3>"; 
				if (isset($this->fDisplay[4]))
				{
					foreach($this->fDisplay[4] as $field)
					{
						$c = $this->field->showFieldValue($content,$field);
						if (($c !== "")&&($c !== null)) {
							$title = $this->field->showFieldTitle(@$content->catid,$field);
							if ($title != "")
								echo "<b>".htmlspecialchars($title)."</b>: ";
							echo "$c<br/>";
						}
					}
				}
				?>
				</div>
			</div>
			<div class="adsmanager_ads_main">
				<div class="adsmanager_ads_image">
					<?php
					if (count($content->images) == 0)
						$image_found =0;
					else
						$image_found =1;
					foreach($content->images as $img)
					{
						$thumbnail = JURI::base()."images/com_adsmanager/ads/".$img->thumbnail;
						$image = JURI::base()."images/com_adsmanager/ads/".$img->image;
					    switch($this->conf->image_display)
					    {
							case 'popup':
								echo "<a href=\"javascript:popup('$image');\"><img src='".$thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>";
								break;
							case 'lightbox':
							case 'lytebox':
								echo "<a href='".$image."' rel='lytebox[roadtrip".$content->id."]'><img src='".$thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>"; 
								break;
							case 'highslide':
								echo "<a id='thumb".$content->id."' class='highslide' onclick='return hs.expand (this)' href='".$image."'><img src='".$thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>";
								break;
							case 'default':	
							default:
								echo "<a href='".$image."' target='_blank'><img src='".$thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>";
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
					<?php echo "<h3>".@$strtitle."</h3>"; 
					if (isset($this->fDisplay[3]))
					{	
						foreach($this->fDisplay[3] as $field)
						{
							$c = $this->field->showFieldValue($content,$field);
							if (($c !== "")&&($c !== null)) {
								$title = $this->field->showFieldTitle(@$content->catid,$field);
								if ($title != "")
									echo "<b>".htmlspecialchars($title)."</b>: ";
								echo "$c<br/>";
							}
						}
					} ?>
					</div>
					<div class="adsmanager_ads_price">
					<?php $strtitle = "";if (@$this->positions[1]->title) {$strtitle = JText::_($this->positions[1]->title); } ?>
					<?php echo "<h3>".@$strtitle."</h3>"; 
					if (isset($this->fDisplay[2]))
					{
						foreach($this->fDisplay[2] as $field)
						{
							$c = $this->field->showFieldValue($content,$field);
							if (($c !== "")&&($c !== null)) {
								$title = $this->field->showFieldTitle(@$content->catid,$field);
								if ($title != "")
									echo "<b>".htmlspecialchars($title)."</b>: ";
								echo "$c<br/>";
							}
						} 
					}?>
					</div>
					<div class="adsmanager_ads_desc">
					<?php $strtitle = "";if (@$this->positions[5]->title) {$strtitle = JText::_($this->positions[5]->title);} ?>
					<?php echo "<h3>".@$strtitle."</h3>"; 
					if (isset($this->fDisplay[6]))
					{	
						foreach($this->fDisplay[6] as $field)
						{
							$c = $this->field->showFieldValue($content,$field);
							if (($c !== "")&&($c !== null)) {
								$title = $this->field->showFieldTitle(@$content->catid,$field);
								if ($title != "")
									echo "<b>".htmlspecialchars($title)."</b>: ";
								echo "$c<br/>";
							}
						}
					} ?>
					</div>
					<div class="adsmanager_ads_contact">
					<?php $strtitle = "";if (@$this->positions[4]->title) {$strtitle = JText::_($this->positions[4]->title);} ?>
					<?php echo "<h3>".@$strtitle."</h3>";  
					if (($this->userid != 0)||($conf->show_contact == 0)) {		
						if (isset($this->fDisplay[5]))
						{		
							foreach($this->fDisplay[5] as $field)
							{	
								$c = $this->field->showFieldValue($content,$field);
								if (($c !== "")&&($c !== null)) {
									$title = $this->field->showFieldTitle(@$content->catid,$field);
									if ($title != "")
										echo "<b>".htmlspecialchars($title)."</b>: ";
									echo "$c<br/>";
								}
							} 
						}
						if (($content->userid != 0)&&($this->conf->allow_contact_by_pms == 1))
						{
							echo TLink::getPMSLink($content);
							echo '<br />';
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
		<?php } ?>
	<?php } ?>
	<div class="pagelinks"><?php echo $this->pagination->getPagesLinks(); ?></div>
<?php 
} $this->general->endTemplate();