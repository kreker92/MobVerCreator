<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if (isset($this->warning_text))
	echo $this->warning_text;

if (isset($this->error_text))
	$this->error_text;
echo JText::_('ADSMANAGER_RULESREAD');

$app = JFactory::getApplication();

$nbimages = $this->conf->nb_images;
if (function_exists("getMaxPaidSystemImages")) {
	$nbimages += getMaxPaidSystemImages();
}
if ($nbimages > 0) {
	$withImages = true;
} else {
	$withImages = false;
}
?>
<div id="adsmanager_writead_header">
	<div id="writead_header1"><?php echo JText::_('ADSMANAGER_HEADER1'); ?></div>
	<div id="writead_header2"><?php echo JText::_('ADSMANAGER_HEADER2'); ?></div>
</div>
<fieldset id="adsmanager_fieldset">
	<!-- titel -->
	<legend>
	<?php
	 if($this->isUpdateMode) {
	   echo JText::_('ADSMANAGER_CONTENT_EDIT');
	 }
	 else {
	   echo JText::_('ADSMANAGER_CONTENT_WRITE');
	 }
	 ?>
	</legend>
	<!-- titel -->
  <!-- form -->
   <!-- category -->
   <table border='0' id="adformtable">
   <tr name='category'>
	<td width="100"><?php echo JText::_('ADSMANAGER_FORM_CATEGORY'); ?></td>
	<td>
	<?php
	  $target = TRoute::_("index.php?option=com_adsmanager&task=save"); 
	  if ($this->nbcats == 1)
	  {
	  	switch($this->conf->single_category_selection_type) {
	  		default:
	  		case 'normal':
	  			JHTMLAdsmanagerCategory::displayNormalCategories("categoryselect",$this->cats,$this->catid,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true));break;
	  		case 'color':
	  			JHTMLAdsmanagerCategory::displayColorCategories("categoryselect",$this->cats,$this->catid,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true));break;
	  		case 'combobox':
	  			JHTMLAdsmanagerCategory::displayComboboxCategories("categoryselect",$this->cats,$this->catid,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true));break;
	  			break;
	  		case 'cascade':
	  			JHTMLAdsmanagerCategory::displaySplitCategories("categoryselect",$this->cats,$this->catid,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true));break;
	  	}
		?>
		</td></tr></table>
		<?php if (@$this->content->id) { 
			$write_url = TRoute::_('index.php?option=com_adsmanager&task=write&id='.$this->content->id);
		} else {
			$write_url = TRoute::_('index.php?option=com_adsmanager&task=write');
		}?>
		<script type="text/javascript">
		jQ(document).ready(function() {
			jQ('#categoryselect').change(function() {
				if (jQ(this).val() != "") {
					url = "<?php echo $write_url?>";
					if (url.indexOf("?") != -1) {
						location.href = "<?php echo $write_url?>&catid="+jQ(this).val();
					} else {
						location.href = "<?php echo $write_url?>?catid="+jQ(this).val();
					}
				}
			});
		});
		</script>
		<form action="<?php echo $target;?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" onkeypress="return checkEnter(event)" onsubmit="return submitbutton(this)">
		<?php
		echo "<input type='hidden' name='category' value='$this->catid' />";
		?>
		<table border='0' id="adformtable">
		
		<?php
	  }
	  else
	  {
		?>
		</td></tr></table>
   		<form action="<?php echo $target;?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" onsubmit="return submitbutton(this)">
   		<table border='0' id="adformtable">
   		<tr name='category'>
		<td colspan="2">
		<?php
		if (isset($this->content->catsid)) {
			$catids = $this->content->catsid;
		} else {
			$catids = array();
		}
		$nbcats = $this->conf->nbcats;
		if (function_exists("getMaxCats")) {
			$nbcats = getMaxCats($nbcats);
		}
		JHTMLAdsmanagerCategory::displayMultipleCategories("cats",$this->cats,$catids,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true),$nbcats);
		?>
		<?php 
	  	if (function_exists("displayPaidCat"))
	  	{
	  		displayPaidCat($this->conf->nbcats);
	  	}
	  	else
	  	{
	  		echo sprintf(JText::_('ADSMANAGER_NBCATS_LEGEND'),$this->conf->nbcats);
	  	}
	  	?></td></tr><?php 
	  }
	?>
	<!-- fields -->
	<?php
	if (($this->nbcats != 1) ||
		(($this->submit_allow == 1)&&( (!isset($this->catid))||($this->catid != 0) ) )
	   )
	{
		/* Submission_type == 0 -> Account Creation with ad posting */
		if ($this->account_creation == 1)
		{
			echo "<tr><td colspan='2'>".JText::_('ADSMANAGER_AUTOMATIC_ACCOUNT')."</td></tr>";
			echo "<tr><td>".JText::_('ADSMANAGER_UNAME')."</td>\n";
			if (isset($this->content->username))
			{
				$username = $this->content->username;
				$password = $this->content->password;
				$email = $this->content->email;
				$name = $this->content->name;
				$style = 'style="background-color:#ff0000"';
			}
			else
			{
				$username = "";
				$password = "";
				$email = "";
				$name =  "";
				$style = "";
			}
								
			if (isset($this->content->firstname))
				$firstname = $this->content->firstname;
			else
				$firstname = "";
			
			if (isset($this->content->middlename))
				$middlename = $this->content->middlename;
			else
				$middlename = "";
			
			if (COMMUNITY_BUILDER == 1)
			{
				include_once( JPATH_BASE .'/administrator/components/com_comprofiler/ue_config.php' );
				$namestyle = $ueConfig['name_style'];
			}
			else
				$namestyle = 1;
				
			echo "<td><input $style class='adsmanager_required' mosReq='1' id='username' type='text' mosLabel='".htmlspecialchars(JText::_('ADSMANAGER_UNAME'),ENT_QUOTES)."' name='username' size='20' maxlength='255' value='$username' /></td></tr>\n"; 
			
			echo "<tr><td>".JText::_('ADSMANAGER_PASSWORD')."</td>\n";
			echo "<td><input $style class='adsmanager_required' mosReq='1' id='password' type='password' mosLabel='".htmlspecialchars(JText::_('ADSMANAGER_PASS'),ENT_QUOTES)."' name='password' size='20' maxlength='255' value='$password' />\n</td></tr>"; 
			$emailField = false;
			$nameField = false;
			foreach($this->fields as $field) 
			{
				if (($field->name == "email")&&((strpos($field->catsid, ",$this->catid,") !== false)||(strpos($field->catsid, ",-1,") !== false)))
				{
					$emailField = true;
					// Force required 
					$field->required = 1;
				}
				else if (($field->name == "name")&&((strpos($field->catsid, ",$this->catid,") !== false)||(strpos($field->catsid, ",-1,") !== false)))
				{
					$nameField = true;
					// Force required 
					$field->required = 1;
				}
				else if (($namestyle >= 2)&&($field->name == "firstname")&&((strpos($field->catsid, ",$this->catid,") !== false)||(strpos($field->catsid, ",-1,") !== false)))
				{
					$firstnameField = true;
					// Force required 
					$field->required = 1;
				}
				else if( ($namestyle == 3)&&($field->name == "middlename")&&((strpos($field->catsid, ",$this->catid,") !== false)||(strpos($field->catsid, ",-1,") !== false)))
				{
					$middlenameField = true;
					// Force required 
					$field->required = 1;
				}			
			}
			if (($namestyle >= 2)&&($firstnameField == false))
			{
				echo "<tr><td>".JText::_('ADSMANAGER_FNAME')."</td>\n";
				echo "<td><input $style class='adsmanager_required' mosReq='1' id='firstname' type='text' mosLabel='".htmlspecialchars(JText::_('ADSMANAGER_FNAME'),ENT_QUOTES)."' name='firstname' size='20' maxlength='255' value='$firstname' /></td></tr>\n"; 
			}
			if ( ($namestyle == 3)&&($middlenameField == false))
			{
				echo "<tr><td>".JText::_('ADSMANAGER_MNAME')."</td>\n";
				echo "<td><input $style class='adsmanager_required' mosReq='1' id='middlename' type='text' mosLabel='".htmlspecialchars(JText::_('ADSMANAGER_MNAME'),ENT_QUOTES)."' name='middlename' size='20' maxlength='255' value='$middlename' /></td></tr>\n"; 
			}
			if ($nameField == false)
			{
				echo "<tr><td>".JText::_('ADSMANAGER_FORM_NAME')."</td>\n";
				echo "<td><input $style class='adsmanager_required' mosReq='1' id='name' type='text' mosLabel='".htmlspecialchars(JText::_('_NAME'),ENT_QUOTES)."' name='name' size='20' maxlength='255' value='$name' /></td></tr>\n"; 
			}
			if ($emailField == false)
			{
				echo "<tr><td>".JText::_('ADSMANAGER_FORM_EMAIL')."</td>\n";
				echo "<td><input $style class='adsmanager_required' mosReq='1' id='email' type='text' mosLabel='".htmlspecialchars(JText::_('_EMAIL'),ENT_QUOTES)."' name='email' size='20' maxlength='255' value='$email' /></td></tr>\n"; 
			}
		}
		
		/* Display Fields */
		foreach($this->fields as $field)
		{
			if (@$field->options->edit_admin_only == 0) {
				$fieldform = $this->field->showFieldForm($field,$this->content,$this->default);
				if ($fieldform != "") {
					echo "<tr id=\"tr_{$field->name}\"><td>".$this->field->showFieldLabel($field,$this->content,$this->default)."</td>\n";
					echo "<td>".$fieldform."</td></tr>\n";
				}
			} 
		}	
		//echo $this->field->showFieldForm($this->fields['ad_price'],$this->content,$this->default);
		?>
		<!-- fields -->
		<!-- image -->
		<?php 
        
		if ($withImages) {?>
				<tr id='tr_images'><td><?php echo JText::_('ADSMANAGER_FORM_AD_PICTURE')?></td><td id="uploader_td"><div id="imagesupload">&nbsp;</div>
				<div><?php echo JText::_('ADSMANAGER_MAX_NUMBER_OF_PICTURES')?>: <span id="maximum"><?php echo $this->conf->nb_images?></span> / <span id="totalcount"><?php echo $this->conf->nb_images?></span></div>
				<style>
				<?php
				$width = $this->conf->max_width_t + 10; 
				$height = $this->conf->max_height_t + 40; 
				?>
				#currentimages li { width: <?php echo $width ?>px; height: <?php echo $height ?>px; }
				#currentimages li img {
				   height: auto;
				   max-height: <?php echo $width-40 ?>px;
				   max-width: <?php echo $width-10 ?>px;
				   width: auto;
				}
				</style>
				<ul id="currentimages">
				<?php 
				$currentnbimages = 0;
				if (@$this->content->pending == 1) {
					$i=1;
					$ad_id = $this->content->id;
					foreach($this->content->images as $img) {
						$dir = JURI::root()."images/com_adsmanager/ads/waiting/";
						$thumb = $dir.$img->thumbnail;
						$index = $img->index;
						echo "<li class='ui-state-default' id='li_img_$i'><img src='".$thumb."?time=".time()."' align='top' border='0' alt='image".$ad_id."' />";
						echo "<br/><input type='checkbox' name='cb_image$i' onClick='removeImage($index,$index)' value='".$index."' />".JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE').'</li>';
						$currentnbimages++;
						$i++;
					}
				} else if ($this->isUpdateMode) {
					$i=0;
					foreach($this->content->images as $img) {
						$i++;
						$index = $img->index;
						$currentnbimages++;
						echo "<li class='ui-state-default' id='li_img_$index' ><img src='".$this->baseurl."images/com_adsmanager/ads/".$img->thumbnail."?time=".time()."' align='top' border='0' alt='image".$this->content->id."' />";
						echo "<br/><input type='checkbox' name='cb_image$i' onClick='removeImage($index,$index)' value='delete' />".JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE').'</li>';
					}
				}
			?>
			</ul>
			<input type="hidden" name="deleted_images" id="deleted_images" value=""/>
			<input type="hidden" name="orderimages" id="orderimages" value="" />
		<script type="text/javascript">
		var current_uploaded_files_count = <?php echo $currentnbimages?>;
		var nb_files_in_queue = 0;
		var max_total_file_count =  <?php echo ($this->conf->nb_images)?>;
		
		function removeTmpImage(fileid){
			if (confirm(<?php echo json_encode(JText::_('ADSMANAGER_CONFIRM_DELETE_IMAGE'))?>)) {
				jQ('#li_img_'+fileid).remove();
				var uploader = jQ('#imagesupload').pluploadQueue();
				jQ.each(uploader.files, function(i, file) {

                    if (file != undefined) {
                        if (file.id == fileid)
                            uploader.removeFile(file);
                    }
				});
				var inputCount = 0, inputHTML= "";
				jQ.each(uploader.files, function(i, file) {
					if (file.status == plupload.DONE) {
						if (file.target_name) {
							inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_tmpname" value="' + plupload.xmlEncode(file.target_name) + '" />';
						}
	
						inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_id" value="' + plupload.xmlEncode(file.id) + '" />';
						inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_name" value="' + plupload.xmlEncode(file.name) + '" />';
						inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_status" value="' + (file.status == plupload.DONE ? 'done' : 'failed') + '" />';
	
						inputCount++;
	
						jQ('#uploader_count').val(inputCount);
					} 
				});
				jQ('#pluploadfield').html(inputHTML);
				if (typeof updatePaidCurrentFileCount != "undefined") {
			    	updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
			    							   current_uploaded_files_count+nb_files_in_queue-1);
			    }
				nb_files_in_queue = uploader.files.length;
				setCurrentFileCount();
			} else {
				jQ('#li_img_'+fileid+' input:checkbox').attr('checked',false);
			}
		}
		
		function removeImage(id,index) {
			if (confirm(<?php echo json_encode(JText::_('ADSMANAGER_CONFIRM_DELETE_IMAGE'))?>)) {
				deleted_images = jQ('#deleted_images').val();
				if (deleted_images == "")
					deleted_images = index;
				else
					deleted_images = deleted_images+","+index;
				jQ('#deleted_images').val(deleted_images);
				
				jQ('#li_img_'+id).remove();
				if (typeof updatePaidCurrentFileCount != "undefined") {
			    	updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
			    							   current_uploaded_files_count+nb_files_in_queue-1);
			    }
				current_uploaded_files_count -= 1;
				setCurrentFileCount();
			} else {
				jQ('#li_img_'+id+' input:checkbox').attr('checked',false);
			}
		}
		
		function setCurrentFileCount() {
			jQ('#maximum').html(current_uploaded_files_count+nb_files_in_queue);
			jQ( "#currentimages" ).sortable(
				{
				 placeholder: "ui-state-highlight",
				 stop: function(event, ui) { 
					 jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
				 },
				 create:function(event,ui) {
					 jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
				}
				}
				 );
			
			jQ( "#currentimages" ).disableSelection();
			jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
		}
		
		function setTotalFileCount(number) {
			jQ('#totalcount').html(number);
			if (number == 0) {
				$('#tr_images').remove();
			}
		}
		
		setCurrentFileCount();
		// Convert divs to queue widgets when the DOM is ready
		jQ(function() {
			jQ("#imagesupload").pluploadQueue({
				// General settings
				runtimes : 'html5,flash,html4',
				url : '<?php echo TRoute::_('index.php?option=com_adsmanager&task=upload&tmpl=component')?>',
				max_file_size : '10mb',
				chunk_size : '1mb',
				unique_names : true,
		
				// Resize images on clientside if we can
				resize : {width : <?php echo $this->conf->max_width?>, height : <?php echo $this->conf->max_height?>, quality : 100},
		
				// Specify what files to browse for
				filters : [
					{title : "Image files", extensions : "jpeg,jpg,gif,png"}
				],
		
				// Flash settings
				flash_swf_url : '<?php echo $this->baseurl?>components/com_adsmanager/js/plupload/plupload.flash.swf',

				init : {
		            FilesAdded: function(up, files) {
						maxnewimages = max_total_file_count - current_uploaded_files_count;
						// Check if the size of the queue is bigger than max_file_count
					    if(up.files.length > maxnewimages)
					    {
					        // Removing the extra files
					        while(up.files.length > maxnewimages)
					        {
					            if(up.files.length > maxnewimages)
					            	up.removeFile(up.files[maxnewimages]);
					        }
					        alert('<?php echo JText::_(sprintf("Max %s Files",$this->conf->nb_images))?>');
					    }

					    if (typeof updatePaidCurrentFileCount != "undefined") {
					    	updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
					    							   current_uploaded_files_count+up.files.length);
					    }
					    nb_files_in_queue = up.files.length;
				        setCurrentFileCount();
					},
					FilesRemoved: function(up, files) {
						if (typeof updatePaidCurrentFileCount != "undefined") {
							updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
	    							   				   current_uploaded_files_count+up.files.length);
					    }
						nb_files_in_queue = up.files.length;
				        setCurrentFileCount();
					},
					FileUploaded: function(up, file,info) {
						maxheight = <?php echo $this->conf->max_height_t ?>;
						name = '<?php echo JURI::root() ?>images/com_adsmanager/ads/uploaded/'+file.target_name;
						html = "<li class='ui-state-default' id='li_img_"+file.id+"'><img height='"+maxheight+"' src='"+name+"' align='top' border='0' alt='' />";
						html += "<br/><input type='checkbox' onClick='removeTmpImage(\""+file.id+"\")' value='' /><?php echo JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE')?></li>";
						jQ('#currentimages').append(html);
						setCurrentFileCount();
					}
				}
			});
			
            <?php if (ADSMANAGER_SPECIAL == "duplicate"){ ?>
                var uploader = jQ('#imagesupload').pluploadQueue();
                var toto = 1245;
                <?php
                    foreach($this->content->images as $image){
                ?>
                 file = new plupload.File();
                file.status = plupload.DONE;
                file.name = '<?php echo $image->image; ?>';
                file.type = 'image/jpeg';
                file.size = 0;
                file.id = toto++;
                file.target_name = '<?php echo $image->image; ?>';

                uploader.files.push(file);
                nb_files_in_queue++;

                uploader.trigger('FileUploaded',file);
                <?php
                    }
                ?>
                setCurrentFileCount();
            <?php } ?>
           
		});
		</script>
		<!--[if IE]>
		<style>
				.plupload_start {
					display : none;
				}
				</style>
		<![endif]-->
		
		</td></tr>
		<?php } ?>
		<?php
		if ($this->conf->metadata_mode == 'frontendbackend') {
		
		echo "<tr id='tr_metadata'><td colspan='2'>".JText::_('ADSMANAGER_METADATA')."</td></tr>";
		?>
		<tr>
		<td><?php echo JText::_('ADSMANAGER_METADATA_DESCRIPTION'); ?></td>
		<td>
		<textarea cols="50" rows="10" name="metadata_description"><?php echo htmlspecialchars(@$this->content->metadata_description)?></textarea>			
		</td>
		</tr>
		
		<tr>
		<td><?php echo JText::_('ADSMANAGER_METADATA_KEYWORDS'); ?></td>
		<td>
		<textarea cols="50" rows="10" name="metadata_keywords"><?php echo htmlspecialchars(@$this->content->metadata_keywords)?></textarea>			
		</td>
		</tr>
		
		<?php } ?>
		
		<?php
		
		if (function_exists("editPaidAd")){
			editPaidAd($this->content,$this->isUpdateMode,$this->conf);
		}
		?>
        <?php
            if($this->isUpdateMode == 0){
                if(isset($this->conf->publication_date) && $this->conf->publication_date == 1) {
        ?>
        <tr>
		<td><?php echo JText::_('ADSMANAGER_PUBLICATION_DATE'); ?></td>
		<td>
            <?php
                if (isset($this->content->publication_date) && $this->content->publication_date != '0000-00-00 00:00:00'){
                    $publication_date = $this->content->publication_date;
                }else{
                    $publication_date = "";
                }
            
                $options = array();
				$options['size'] = 25;
				$options['maxlength'] = 19;
                $options['class'] = 'adsmanager_required';
                $options['mosReq'] = '1';
                $options['mosLabel'] = htmlspecialchars(JText::_('ADSMANAGER_PUBLICATION_DATE'),ENT_QUOTES);
				$return = JHTML::_('behavior.calendar');
				$return .= JHTML::_('calendar', $publication_date, "publication_date", "publication_date", '%Y-%m-%d', $options);
//              $return .= "<script type='text/javascript'>jQ(document).ready(function() {jQ('#publication_date').val(".json_encode($publication_date).");});</script>";
                
                echo $return;
            ?>
		</td>
		</tr>
        <?php
                }
            }
        ?>
		<?php echo $this->event->onContentAfterForm ?>	
		<!-- buttons -->
		<?php if ((@$this->content->id == 0)&&(@$this->conf->show_accept_rules == 1)) {?>
		<tr><td colspan="2"><input type="checkbox" id="acceptrules" />
		<a href="<?php echo TRoute::_('index.php?option=com_adsmanager&view=rules')?>" target="_blank" >
		<?php echo htmlspecialchars(JText::_('ADSMANAGER_ACCEPT_RULES_CHECKBOX'))?>
		</a></td></tr>
		<?php } ?>
		<?php
		if (isset($this->content->date_created))
			echo "<input type='hidden' name='date_created' value='".$this->content->date_created."' />";	
			
		echo "<input type='hidden' name='isUpdateMode' value='".$this->isUpdateMode."' />";
		echo "<input type='hidden' name='id' value='".@$this->content->id."' />";
		echo "<input type='hidden' name='pending' value='".@$this->content->pending."' />";
		?>
		<tr>
		<td>
		<input type="button" class="button btn" onclick='window.location="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list"); ?>"' value="<?php echo JText::_('ADSMANAGER_FORM_CANCEL_TEXT'); ?>" />
		</td>
		<td>
		<input type="submit" class="button btn" value="<?php echo JText::_('ADSMANAGER_FORM_SUBMIT_TEXT'); ?>" />
		</td>
		</tr>
		<!-- buttons -->
	<?php
	}
	?>
  <?php echo JHTML::_( 'form.token' ); ?>
</table>
</form>
<!-- form -->
</fieldset>
<script type="text/javascript">	
function CaracMax(text, max)
{
	if (text.value.length >= max)
	{
		text.value = text.value.substr(0, max - 1) ;
	}
}

function checkEnter(e){
	 e = e || event;
	 if(e.keyCode == 13 && e.target.nodeName!='TEXTAREA')
     {
       e.preventDefault();
       return false;
     }
}

function submitbutton(mfrm) {
	
	var me = mfrm.elements;
	var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
	var r_num = new RegExp("[^0-9\., ]", "i");
	var r_email = new RegExp("^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$" ,"i");

	var errorMSG = '';
	var iserror=0;
	
	<?php 
	if (function_exists("loadEditFormCheck")){
		loadEditFormCheck();
	}
	?>
	
	<?php if ($this->nbcats > 1)
	{
	?>
		var form = document.adminForm;
		var srcList = eval( 'form.selected_cats' );
		var srcLen = srcList.length;
		if (srcLen == 0)
		{
			errorMSG += <?php echo json_encode(JText::_('ADSMANAGER_FORM_CATEGORY')); ?>+" : "+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_ERROR')); ?>+'\n';
			srcList.style.background = "red";
			iserror=1;
		}
		else
		{
			for (var i=0; i < srcLen; i++) {
				srcList.options[i].selected = true;
			}
		}
	<?php
	}
	?>
	
	if (mfrm.username && (r.exec(mfrm.username.value) || mfrm.username.value.length < 3)) {
		errorMSG += mfrm.username.getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(sprintf( JText::_('ADSMANAGER_VALID_AZ09'), JText::_('ADSMANAGER_PROMPT_UNAME'), 4 )); ?>+'\n';
		mfrm.username.style.background = "red";
		iserror=1;
	} 
	if (mfrm.password && r.exec(mfrm.password.value)) {
		errorMSG += mfrm.password.getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(sprintf( JText::_('ADSMANAGER_VALID_AZ09'), JText::_('ADSMANAGER_REGISTER_PASS'), 6 )); ?>+'\n';
		mfrm.password.style.background = "red";
		iserror=1;
	}
	
	if (mfrm.email && !r_email.exec(mfrm.email.value) && mfrm.email.getAttribute('mosReq')) {
		errorMSG += mfrm.email.getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_EMAIL')); ?>+'\n';
		mfrm.email.style.background = "red";
		iserror=1;
	}
				
	// loop through all input elements in form
	for (var i=0; i < me.length; i++) {
	
		if ((me[i].getAttribute('test') == 'number' ) && (r_num.exec(me[i].value))) {
			errorMSG += me[i].getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_NUMBER')); ?>+'\n';
			iserror=1;
		}
		
		// check if element is mandatory; here mosReq="1"
		if ((me[i].getAttribute('mosReq') == 1)&&(me[i].type == 'hidden')&&(me[i].value == '')) {
			// add up all error messages
			errorMSG += me[i].getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_ERROR')); ?>+'\n';
			// notify user by changing background color, in this case to red
			el = me[i].getAttribute('mosElem');

			elem = document.getElementById(el);
			elem.style.background = "red";
			iserror=1;
		} else if ((me[i].getAttribute('mosReq') == 1)&&(me[i].style.visibility != 'hidden')&&(me[i].style.display != 'none')) {
			if (me[i].type == 'radio' || me[i].type == 'checkbox') {
				var rOptions = me[me[i].getAttribute('name')];
				var rChecked = 0;
				if(rOptions.length > 1) {
					for (var r=0; r < rOptions.length; r++) {
						if (rOptions[r].checked) {
							rChecked=1;
						}
					}
				} else {
					if (me[i].checked) {
						rChecked=1;
					}
				}
				if(rChecked==0) {
					// add up all error messages
					errorMSG += me[i].getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_ERROR')); ?>+'\n';
					// notify user by changing background color, in this case to red
					me[i].style.background = "red";
					iserror=1;
				} 
			}
			if (me[i].value == '') {
				// add up all error messages
				errorMSG += me[i].getAttribute('mosLabel').replace('&nbsp;',' ') + ' : '+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_ERROR')); ?>+'\n';
				// notify user by changing background color, in this case to red
				me[i].style.background = "red";
				iserror=1;
			} 
		}
	}

	<?php if ((@$this->content->id == 0)&&($this->conf->show_accept_rules == 1)) {?>
	if (!jQ('#acceptrules').is(':checked')) {
		iserror=1;
		errorMSG += <?php echo json_encode(JText::_('ADSMANAGER_YOU_MUST_ACCEPT_RULES')) ?>;
	}
	<?php } ?>
	
	
	if(iserror==1) {
		alert(errorMSG);
		return false;
	} else {
		<?php if ($withImages) {?>
		 var uploader = jQ('#imagesupload').pluploadQueue();
			
        // Files in queue upload them first
        if (uploader.files.length !== (uploader.total.uploaded + uploader.total.failed)) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                	//Little hack to be able to return the selected_cats
            		<?php if ($this->nbcats > 1) { ?>
            		var srcList = eval( 'form.selected_cats' );
            		srcList.name = "selected_cats[]"; 
            		<?php } ?>
            		jQ('#adminForm')[0].submit();
                }
            });
                
            uploader.start();
            return false;
        }  
        <?php } ?>
	        
		//Little hack to be able to return the selected_cats
		<?php if ($this->nbcats > 1) { ?>
			srcList.name = "selected_cats[]"; 
		<?php } ?>
		return true;
	}
}

function updateFields() {
	var form = document.adminForm;
	var singlecat = 0;
	var length = 0;
	
	if ( typeof(document.adminForm.category ) != "undefined" ) {
		singlecat = 1;
		length = 1;
	}
	else if (typeof(document.adminForm.selected_cats ) != "undefined")
	{
		length = form.selected_cats.length;
	} else {
		length = 1;
	}
	
	<?php
	foreach($this->fields as $field)
	{ 
		if (strpos($field->catsid, ",-1,") === false)
		{
			$name = $field->name;
			if (($field->type == "multicheckbox")||($field->type == "multiselect"))
				$name .= "[]";
		?>
		var input = document.getElementById('<?php echo $name;?>');
		var trzone = document.getElementById('tr_<?php echo $field->name;?>');
		if (((singlecat == 0)&&(length == 0))||
		    ((singlecat == 1)&&(document.adminForm.category.value == 0)))
		{
			if (input != null)
				input.style.visibility = 'hidden';
			if (trzone != null) {
				trzone.style.visibility = 'hidden';
				trzone.style.display = 'none';
			}
		}
		else
		{
			for (var i=0; i < length; i++) {
				var field_<?php echo $field->name;?> = '<?php echo $field->catsid;?>';
				var temp;
				if (singlecat == 0)
					temp = form.selected_cats.options[i].value;
				else
					temp = document.adminForm.category.value;
					
				var test = field_<?php echo $field->name;?>.indexOf( ","+temp+",", 0 );
				if (test != -1)
				{
					if (input != null)
						input.style.visibility = 'visible';
					if (trzone != null) {
						trzone.style.visibility = 'visible';
						trzone.style.display = '';
					}
					break;
				}
				else
				{
					if (input != null)
						input.style.visibility = 'hidden';
					if (trzone != null) {
						trzone.style.visibility = 'hidden';
						trzone.style.display = 'none';
					}
				}
			}
		}
	<?php
		}
	} 
	?>
}

function checkdependency(child,parentname,parentvalue) {
	//Simple checkbox
	if (jQ('input[name="'+parentname+'"]').is(':checkbox')) {
		//alert("test");
		if (jQ('input[name="'+parentname+'"]').attr('checked')) {
			jQ('#adminForm #'+child).show();
			jQ('#adminForm #tr_'+child).show();
		}
		else {
			jQ('#adminForm #'+child).hide();
			jQ('#adminForm #tr_'+child).hide();
			
			//cleanup child field 
			if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
				jQ('#adminForm #'+child).attr('checked', false);
			}
			else {
				jQ('#adminForm #'+child).val('');
			}
		} 
	}
	//If checkboxes or radio buttons, special treatment
	else if (jQ('input[name="'+parentname+'"]').is(':radio')  || jQ('input[name="'+parentname+'[]"]').is(':checkbox')) {
		var find = false;
		var allVals = [];
		jQ("input:checked").each(function() {
			if (jQ(this).val() == parentvalue) {	
				jQ('#adminForm #'+child).show();
				jQ('#adminForm #tr_'+child).show();
				find = true;
			}
		});
		
		if (find == false) {
			jQ('#adminForm #'+child).hide();
			jQ('#adminForm #tr_'+child).hide();
			
			//cleanup child field 
			if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
				jQ('#adminForm #'+child).attr('checked', false);
			}
			else {
				jQ('#adminForm #'+child).val('');
			}
		}

	}
	//simple text
	else if (jQ('#adminForm #'+parentname).val() == parentvalue) {
		jQ('#adminForm #'+child).show();
		jQ('#adminForm #tr_'+child).show();
	} 
	else {
		jQ('#adminForm #'+child).hide();
		jQ('#adminForm #tr_'+child).hide();
		
		//cleanup child field 
		if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
			jQ('#adminForm #'+child).attr('checked', false);
		}
		else {
			jQ('#adminForm #'+child).val('');
		}
	}
}
function dependency(child,parentname,parentvalue) {
	//if checkboxes
	jQ('input[name="'+parentname+'[]"]').change(function() {
		checkdependency(child,parentname,parentvalue);
	});
	//if buttons radio
	jQ('input[name="'+parentname+'"]').change(function() {
		checkdependency(child,parentname,parentvalue);
	});
	jQ('#'+parentname).click(function() {
		checkdependency(child,parentname,parentvalue);
	});
	checkdependency(child,parentname,parentvalue);
}

jQ(document).ready(function() {
	updateFields();

	<?php foreach($this->fields as $field) { 
		if (@$field->options->is_conditional_field == 1) { ?>
	dependency('<?php echo $field->name?>',
			   '<?php echo $field->options->conditional_parent_name?>',
			   '<?php echo $field->options->conditional_parent_value?>');
		<?php } 
	}?>
});
</script>