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
$conf= $this->conf;
?>
<h1 class="contentheading">
<?php echo JText::_($this->list_name);?>
</h1>
<script language="JavaScript" type="text/JavaScript">
<!--
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
        form.action= '<?php echo TRoute::_("index.php?option=com_adsmanager&view=myads") ?>';  
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
//-->
</script>

<?php if (($conf->display_list_sort == 1)||($conf->display_list_search == 1)) { ?>
<div class="adsmanager_search_box">
<div class="adsmanager_inner_box">
	<?php if ($conf->display_list_search == 1) { ?>
		<form action="<?php echo TRoute::_('index.php?option=com_adsmanager&view=myads') ?>" method="post">
		<div align="left">
			<input name="tsearch" id="tsearch" maxlength="20" alt="search" class="inputbox" type="text" size="20" value="<?php echo $this->tsearch;?>"  onblur="if(this.value=='') this.value='';" onfocus="if(this.value=='<?php echo $this->tsearch;?>') this.value='';" />
		</div>
		</form> 
	<?php } ?>
	<?php if ($conf->display_list_sort == 1) { ?>
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
	<?php echo $this->pagination->getResultsCounter() ?>
	<br/><br/>
		<table class="adsmanager_table table table-striped">
			<tr>
			  <th><?php echo JText::_('ADSMANAGER_CONTENT'); ?>
			  </th>
			  <th class="hidden-phone"><?php echo JText::_('ADSMANAGER_INFO'); ?>
              </th>
			  <th class="hidden-phone"><?php echo JText::_('ADSMANAGER_ACTIONS'); ?>
              </th>
			</tr>
		<?php
		foreach($this->contents as $content) 
		{
			$linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
			?>   
			<tr class="trcategory_<?php echo $content->catid?>"> 
				<td>
					<?php
					if (isset($content->images[0])) {
						echo "<a href='".$linkTarget."'><img class='adimage' name='adimage".$content->id."' src='".$this->baseurl."images/com_adsmanager/ads/".$content->images[0]->thumbnail."' alt='".htmlspecialchars($content->ad_headline)."' /></a>";
						
					} else  {
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
						$af_text = JString::substr($content->ad_text, 0, 100)."[...]";
						echo $af_text;
					?>
					</div>		
				</td>
				<td class="tdcenter hidden-phone">
					<strong>
					<?php 
					//var_dump($content);
					if ($content->published == 1) 
						echo JText::_('ADSMANAGER_PUBLISHED');
					else
						echo JText::_('ADSMANAGER_NOT_PUBLISHED');?>
					</strong>
					<br/>
					<?php 
					if ($this->conf->expiration == 1) { 
						if ($content->expiration_date != null) {
							echo JText::_('ADSMANAGER_EXPIRATION_DATE').": ".$this->reorderDate($content->expiration_date)."<br/>";
						} else {
							echo JText::_('ADSMANAGER_EXPIRATION_DATE').": ".JText::_('PAIDSYSTEM_NO_EXPIRATION')."<br/>";
						}
					}
					if (@$content->highlight == 1) {
			        	echo "<strong>".JText::_('PAIDSYSTEM_HIGHLIGHT')."</strong>:";
			        	if ($content->highlight_date != null) 
			        		echo $this->reorderDate($content->highlight_date); 
			        	else 
			        		echo JText::_('ADSMANAGER_YES');
			        	echo "<br/>";
					}
					if (@$content->featured == 1) {
						echo "<strong>".JText::_('PAIDSYSTEM_FEATURED')."</strong>:";
			        	if ($content->featured_date != null) 
			        		echo $this->reorderDate($content->featured_date); 
			        	else 
			        		echo JText::_('ADSMANAGER_YES');
			        	echo "<br/>";
			        }
					if (@$content->top == 1) {
						echo "<strong>".JText::_('PAIDSYSTEM_TOP')."</strong>:";
			        	if ($content->top_date != null) 
			        		echo $this->reorderDate($content->top_date); 
			        	else 
			        		echo JText::_('ADSMANAGER_YES');
			        	echo "<br/>";
			        }
			        echo sprintf(JText::_('ADSMANAGER_VIEWS'),$content->views); ?>
				</td>
				<td class="tdcenter hidden-phone">
					<?php
					$target = TRoute::_("index.php?option=com_adsmanager&task=write&catid=".$content->catid."&id=$content->id");
					echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_EDIT')."</a>";
					echo "<br/>";
					$target = TRoute::_("index.php?option=com_adsmanager&task=delete&catid=".$content->catid."&id=$content->id");
					echo "<a onclick='return confirm(\"".htmlspecialchars(JText::_('ADSMANAGER_CONFIRM_DELETE'),ENT_QUOTES)."\")' href='".$target."'>".JText::_('ADSMANAGER_CONTENT_DELETE')."</a>";
					if (ADSMANAGER_SPECIAL == "duplicate"){
                        echo "<br/>";
                        $target = TRoute::_("index.php?option=com_adsmanager&task=duplicate&catid=".$content->catid."&id=$content->id");
                        echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_DUPLICATE')."</a>";
                    }
					if ($this->conf->expiration == 1) { 
						if ($content->expiration_date != null) {
							$expiration_time = strtotime($content->expiration_date);
							$current_time = time();
							if ($expiration_time - $current_time <= ($conf->recall_time * 3600 *24)) {
                                if(function_exists('countRenewDurations') && countRenewDurations() > 1){
								$target = TRoute::_("index.php?option=com_adsmanager&view=expiration&id=$content->id");
                                } else {
                                    $target = TRoute::_("index.php?option=com_adsmanager&task=renew&id=$content->id");
                                }
								echo "<br/><a href='".$target."'>".JText::_('ADSMANAGER_RENEW_CONTENT')."</a>";
							}
						}
					}
					
					/*if (isset($this->topoption)) {
						$target = TRoute::_('index.php?option=com_paidsystem&task=bringtotop&id='.$content->id);
						echo "<br/><a href='".$target."'>".JText::_('PAIDSYSTEM_TOP_ONE_SHOT')."</a>";
					}*/	
					?>
				</td>
			</tr>
		<?php	
		}
		?>
		</table>
	<div class="pagelinks"><?php echo $this->pagination->getPagesLinks(); ?></div>
	</form>
<?php 
} $this->general->endTemplate();