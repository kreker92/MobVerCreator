<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<script type="text/javascript">
<?php if(version_compare(JVERSION,'1.6.0','>=')){ ?>
Joomla.submitbutton = function(pressbutton) {
<?php } else { ?>
function submitbutton(pressbutton) {
<?php } ?>
		<?php 
		$editor	= JFactory::getEditor(); 
		echo $editor->save( 'fronttext' );
		echo $editor->save( 'rules_text' );
		echo $editor->save( 'recall_text' );
		?>
       submitform(pressbutton);
   }
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php 
$tabs	= new TPane();
echo $tabs->startPane('config');
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_GENERAL'), "general-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
    <tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ADMIN'); ?></td>
		<td>
		    <input id='email_admin' name='params_email_admin' value="<?php echo @$this->conf->email_admin; ?>" />
		</td>
		<td>&nbsp;</td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_NAME_ADMIN'); ?></td>
		<td>
		    <input id='name_admin' name='params_name_admin' value="<?php echo @$this->conf->name_admin; ?>" />
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_AUTO_PUBLISH'); ?></td>
		<td>
		 <select id='auto_publish' name='auto_publish'>
			<option value='1' <?php if ($this->conf->auto_publish == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->auto_publish == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_AUTO_PUBLISH_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_UPDATE_VALIDATION'); ?></td>
		<td>
		 <select id='update_validation' name='params_update_validation'>
			<option value='1' <?php if (@$this->conf->update_validation == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if (@$this->conf->update_validation == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td>&nbsp;<?php echo JText::_('ADSMANAGER_UPDATE_VALIDATION_LONG'); ?></td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_PUBLICATION_DATE'); ?></td>
		<td>
		 <select id='publication_date' name='params_publication_date'>
            <option value='0' <?php if (@$this->conf->publication_date == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
			<option value='1' <?php if (@$this->conf->publication_date == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
		  </select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SUBMISSION_TYPE'); ?></td>
		<td>
		<select id='submission_type' name='submission_type'>
			<option value='0' <?php if ($this->conf->submission_type == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SUBMISION_WITH_ACCOUNT_CREATION'); ?></option>
			<option value='1' <?php if ($this->conf->submission_type == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SUBMISSION_ALLOWED_ONLY_FOR_REGISTERS'); ?></option>
			<option value='2' <?php if ($this->conf->submission_type == 2) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SUBMISSION_ALLOWED_FOR_VISITORS'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_ROOT_SUBMIT'); ?></td>
		<td>
		<select id='root_allowed' name='root_allowed'>
			<option value='1' <?php if ($this->conf->root_allowed == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_ROOT_SUBMIT_ALLOWED'); ?></option>
			<option value='0' <?php if ($this->conf->root_allowed == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_ROOT_SUBMIT_NOT_ALLOWED'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_ROOT_SUBMIT_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_NBCATS'); ?></td>
		<td><input type="text" name="nbcats" value=<?php echo $this->conf->nbcats; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_NBCATS_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_NB_ADS_BY_USER'); ?></td>
		<td><input type="text" name="nb_ads_by_user" value=<?php echo $this->conf->nb_ads_by_user; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_NB_ADS_BY_USER_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_METADATA'); ?></td>
		<td>
		<select id='metadata_mode' name='metadata_mode'>
			<option value='nometadata' <?php if ($this->conf->metadata_mode == 'nometadata') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_METADATA_NO_METADATA'); ?></option>
			<option value='frontendbackend' <?php if ($this->conf->metadata_mode == 'frontendbackend') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_METADATA_FRONTEND_BACKEND'); ?></option>
			<option value='backendonly' <?php if ($this->conf->metadata_mode == 'backendonly') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_METADATA_BACKENDONLY'); ?></option>
			<option value='automatic' <?php if ($this->conf->metadata_mode == 'automatic') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_METADATA_AUTOMATIC'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SHOW_RSS'); ?></td>
		<td>
		 <select id='show_rss' name='show_rss'>
			<option value='1' <?php if ($this->conf->show_rss == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->show_rss == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td>&nbsp;<?php echo JText::_('ADSMANAGER_SHOW_RSS_LONG'); ?></td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_PRINT'); ?></td>
		<td>
		 <select id='show_print' name='params_print'>
			<option value='1' <?php if (@$this->conf->print == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if (@$this->conf->print == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td>&nbsp;<?php echo JText::_('ADSMANAGER_PRINT_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SHOW_ACCEPT_RULES'); ?></td>
		<td>
		 <select id='show_accept_rules' name='params_show_accept_rules'>
			<option value='1' <?php if (@$this->conf->show_accept_rules == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if (@$this->conf->show_accept_rules == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td>&nbsp;<?php echo JText::_('ADSMANAGER_SHOW_ACCEPT_RULES_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_GLOBAL_FILTER_FIELDNAME'); ?></td>
		<td><input type="text" name="params_globalfilter_fieldname" value=<?php echo $this->conf->globalfilter_fieldname; ?> /></td>
		<td>&nbsp;<?php echo JText::_('ADSMANAGER_GLOBAL_FILTER_FIELDNAME_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_GLOBAL_FILTER_USER_VIEW'); ?></td>
		<td>
		  <select id='globalfilter_user' name='params_globalfilter_user'>
			<option value='1' <?php if (@$this->conf->globalfilter_user == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if (@$this->conf->globalfilter_user == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td>&nbsp;<?php echo JText::_('ADSMANAGER_GLOBAL_FILTER_USER_VIEW_LONG'); ?></td>
	</tr>
</table>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_CATEGORIES'), "categories-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SINGLE_CATEGORY_SELECTION_TYPE'); ?></td>
		<td>
		<select id='single_category_selection_type' name='params_single_category_selection_type'>
			<option value='normal' <?php if (@$this->conf->single_category_selection_type == 'normal') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SINGLE_CATEGORY_SELECTION_TYPE_NORMAL'); ?></option>
			<option value='color' <?php if (@$this->conf->single_category_selection_type == 'color') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SINGLE_CATEGORY_SELECTION_TYPE_COLOR'); ?></option>
			<option value='combobox' <?php if (@$this->conf->single_category_selection_type == 'combobox') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SINGLE_CATEGORY_SELECTION_TYPE_AUTOCOMPLETE'); ?></option>
			<option value='cascade' <?php if (@$this->conf->single_category_selection_type == 'cascade') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SINGLE_CATEGORY_SELECTION_TYPE_CASCADE'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_DISPLAY'), "display-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<?php /*<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_FRONT'); ?></td>
		<td>
		 <select id='display_front' name='display_front'>
			<option value='1' <?php if ($this->conf->display_front == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->display_front == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select
		</td>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_FRONT_LONG'); ?></td>
	</tr>
	*/?>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_ADS_PER_PAGE');?></td>
		<td><input type="text" name="ads_per_page" value=<?php echo $this->conf->ads_per_page; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_ADS_PER_PAGE_LONG');?></td>
	</tr>	
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_MODE'); ?></td>
		<td>
		<select id='display_expand' name='display_expand'>
			<option value='2' <?php if ($this->conf->display_expand == 2) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SHORT_AND_EXPAND_MODE'); ?></option>
			<option value='1' <?php if ($this->conf->display_expand == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_EXPAND_MODE'); ?></option>
			<option value='0' <?php if ($this->conf->display_expand == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SHORT_MODE'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_MODE_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_NB_CATEGORIES_PER_ROW'); ?></td>
		<td>
		<select id='display_nb_categories_per_row' name='params_display_nb_categories_per_row'>
			<option value='1' <?php if (@$this->conf->display_nb_categories_per_row == 1) { echo "selected"; } ?>>1</option>
			<option value='2' <?php if (@$this->conf->display_nb_categories_per_row == 2) { echo "selected"; } ?>>2</option>
			<option value='3' <?php if (@$this->conf->display_nb_categories_per_row == 3) { echo "selected"; } ?>>3</option>
			<option value='4' <?php if (@$this->conf->display_nb_categories_per_row == 4) { echo "selected"; } ?>>4</option>
			<option value='6' <?php if (@$this->conf->display_nb_categories_per_row == 6) { echo "selected"; } ?>>6</option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_NB_ADS_PER_CATEGORIES'); ?></td>
		<td>
		<select id='display_nb_ads_per_categories' name='params_display_nb_ads_per_categories'>
			<option value='1' <?php if (@$this->conf->display_nb_ads_per_categories == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if (@$this->conf->display_nb_ads_per_categories == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_LAST_ADS'); ?></td>
		<td>
		<select id='display_last' name='display_last'>
			<option value='2' <?php if ($this->conf->display_last == 2) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_LAST_BOTTOM');?></option>
			<option value='1' <?php if ($this->conf->display_last == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_LAST_TOP');  ?></option>
			<option value='0' <?php if ($this->conf->display_last == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_LAST_NONE'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_LAST_ADS_NB_COLS');?></td>
		<td><input type="text" name="nb_last_cols" value=<?php echo $this->conf->nb_last_cols; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_LAST_ADS_NB_COLS_LONG');?></td>
	</tr>	
	<tr>
		<td><?php echo JText::_('ADSMANAGER_LAST_ADS_NB_ROWS');?></td>
		<td><input type="text" name="nb_last_rows" value=<?php echo $this->conf->nb_last_rows; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_LAST_ADS_NB_ROWS_LONG');?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_GENERAL_MENU'); ?></td>
		<td>
		 <select id='display_general_menu' name='display_general_menu'>
			<option value='1' <?php if ($this->conf->display_general_menu == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->display_general_menu == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td></td>
	</tr>	
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_LIST_SORT'); ?></td>
		<td>
		 <select id='display_list_sort' name='display_list_sort'>
			<option value='1' <?php if ($this->conf->display_list_sort == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->display_list_sort == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td></td>
	</tr>	
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_LIST_SEARCH'); ?></td>
		<td>
		 <select id='display_list_search' name='display_list_search'>
			<option value='1' <?php if ($this->conf->display_list_search == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->display_list_search == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td></td>
	</tr>	
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_INNER_PATHWAY'); ?></td>
		<td>
		 <select id='display_inner_pathway' name='display_inner_pathway'>
			<option value='1' <?php if ($this->conf->display_inner_pathway == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->display_inner_pathway == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td></td>
	</tr>	
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SHOW_NEW'); ?></td>
		<td>
		 <select id='show_new' name='show_new'>
			<option value='1' <?php if ($this->conf->show_new == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->show_new == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_SHOW_NEW_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_NBDAYS_NEW'); ?></td>
		<td><input type="text" name="nbdays_new" value=<?php echo $this->conf->nbdays_new; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_NBDAYS_NEW_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SHOW_HOT'); ?></td>
		<td>
		 <select id='show_hot' name='show_hot'>
			<option value='1' <?php if ($this->conf->show_hot == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->show_hot == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_SHOW_HOT_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_NBHITS'); ?></td>
		<td><input type="text" name="nbhits" value=<?php echo $this->conf->nbhits; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_NBHITS_LONG'); ?></td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_MAP_LIST'); ?></td>
		<td>
            <select id='display_map_list' name='params_display_map_list'>
                <option value='0' <?php if (@$this->conf->display_map_list == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
                <option value='1' <?php if (@$this->conf->display_map_list == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
            </select>
        </td>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_MAP_LIST_LONG'); ?></td>
	</tr>
</table>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_EMAIL'), "email-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_COPY_TO_ADMIN'); ?></td>
		<td>
		 <select id='copy_to_admin' name='params_copy_to_admin'>
            <option value='0' <?php if (@$this->conf->copy_to_admin == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
			<option value='1' <?php if (@$this->conf->copy_to_admin == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
        </select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_COPY_TO_ADMIN_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_NEW'); ?></td>
		<td>
		 <select id='send_email_on_new' name='send_email_on_new'>
			<option value='1' <?php if ($this->conf->send_email_on_new == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->send_email_on_new == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_NEW_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_UPDATE'); ?></td>
		<td>
		 <select id='send_email_on_update' name='send_email_on_update'>
			<option value='1' <?php if ($this->conf->send_email_on_update == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->send_email_on_update == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_UPDATE_LONG'); ?></td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_ADMIN_EMAIL_ON_WAITING_VALIDATION'); ?></td>
		<td>
		 <select id='email_on_waiting_validation' name='params_email_on_waiting_validation'>
			<option value='1' <?php if ($this->conf->email_on_waiting_validation == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->email_on_waiting_validation == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_ADMIN_EMAIL_ON_WAITING_VALIDATION_LONG'); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo JText::_('ADSMANAGER_POSSIBLE_TAGS')?></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_admin_new_subject">
		<td><?php echo JText::_('ADSMANAGER_ADMIN_NEW_SUBJECT'); ?></td>
		<td><input type="text" name="admin_new_subject" value="<?php echo htmlspecialchars($this->conf->admin_new_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_admin_new_text">
		<td><?php echo JText::_('ADSMANAGER_ADMIN_NEW_TEXT'); ?></td>
		<td><?php echo $editor->display( 'admin_new_text', $this->conf->admin_new_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_admin_update_subject">
		<td><?php echo JText::_('ADSMANAGER_ADMIN_UPDATE_SUBJECT'); ?></td>
		<td><input type="text" name="admin_update_subject" value="<?php echo htmlspecialchars($this->conf->admin_update_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_admin_update_text">
		<td><?php echo JText::_('ADSMANAGER_ADMIN_UPDATE_TEXT'); ?></td>
		<td><?php echo $editor->display( 'admin_update_text', $this->conf->admin_update_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
    <tr id="tr_admin_waiting_validation_subject">
		<td><?php echo JText::_('ADSMANAGER_ADMIN_WAITING_VALIDATION_SUBJECT'); ?></td>
		<td><input type="text" name="params_admin_waiting_validation_subject" value="<?php echo htmlspecialchars(@$this->conf->admin_waiting_validation_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_admin_waiting_validation_text">
		<td><?php echo JText::_('ADSMANAGER_ADMIN_WAITING_VALIDATION_TEXT'); ?></td>
		<td><?php echo $editor->display( 'params_admin_waiting_validation_text', @$this->conf->admin_waiting_validation_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_NEW_TO_USER'); ?></td>
		<td>
		 <select id='send_email_on_new_to_user' name='send_email_on_new_to_user'>
			<option value='1' <?php if ($this->conf->send_email_on_new_to_user == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->send_email_on_new_to_user == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_UPDATE_TO_USER'); ?></td>
		<td>
		 <select id='send_email_on_update_to_user' name='send_email_on_update_to_user'>
			<option value='1' <?php if ($this->conf->send_email_on_update_to_user == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->send_email_on_update_to_user == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td></td>
	</tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_WAITING_VALIDATION_TO_USER'); ?></td>
		<td>
		 <select id='send_email_on_waiting_validation_to_user' name='params_send_email_waiting_validation_to_user'>
			<option value='1' <?php if (@$this->conf->send_email_waiting_validation_to_user == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if (@$this->conf->send_email_waiting_validation_to_user == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_VALIDATION_TO_USER'); ?></td>
		<td>
		 <select id='send_email_on_validation_to_user' name='send_email_on_validation_to_user'>
			<option value='1' <?php if ($this->conf->send_email_on_validation_to_user == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->send_email_on_validation_to_user == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_ON_EXPIRATION_TO_USER'); ?></td>
		<td>
		 <select id='send_email_on_expiration_to_user' name='send_email_on_expiration_to_user'>
			<option value='1' <?php if ($this->conf->send_email_on_expiration_to_user == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->send_email_on_expiration_to_user == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td></td>
	</tr>
	<tr id="tr_new_subject">
		<td><?php echo JText::_('ADSMANAGER_NEW_SUBJECT'); ?></td>
		<td><input type="text" name="new_subject" value="<?php echo htmlspecialchars($this->conf->new_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_new_text">
		<td><?php echo JText::_('ADSMANAGER_NEW_TEXT'); ?></td>
		<td><?php echo $editor->display( 'new_text', $this->conf->new_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_update_subject">
		<td><?php echo JText::_('ADSMANAGER_UPDATE_SUBJECT'); ?></td>
		<td><input type="text" name="update_subject" value="<?php echo htmlspecialchars($this->conf->update_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr  id="tr_update_text">
		<td><?php echo JText::_('ADSMANAGER_UPDATE_TEXT'); ?></td>
		<td><?php echo $editor->display( 'update_text', $this->conf->update_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_waiting_validation_subject">
		<td><?php echo JText::_('ADSMANAGER_WAITING_VALIDATION_SUBJECT'); ?></td>
		<td><input type="text" name="waiting_validation_subject" value="<?php echo htmlspecialchars($this->conf->waiting_validation_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_waiting_validation_text">
		<td><?php echo JText::_('ADSMANAGER_WAITING_VALIDATION_TEXT'); ?></td>
		<td><?php echo $editor->display( 'waiting_validation_text', $this->conf->waiting_validation_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_validation_subject">
		<td><?php echo JText::_('ADSMANAGER_VALIDATION_SUBJECT'); ?></td>
		<td><input type="text" name="validation_subject" value="<?php echo htmlspecialchars($this->conf->validation_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_validation_text">
		<td><?php echo JText::_('ADSMANAGER_VALIDATION_TEXT'); ?></td>
		<td><?php echo $editor->display( 'validation_text', $this->conf->validation_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_RECALL_SUBJECT'); ?></td>
		<td><input type="text" name="recall_subject" value="<?php echo htmlspecialchars($this->conf->recall_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_RECALL_TEXT'); ?></td>
		<td><?php echo $editor->display( 'recall_text', $this->conf->recall_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>	
	<tr id="tr_expiration_subject">
		<td><?php echo JText::_('ADSMANAGER_EXPIRATION_SUBJECT'); ?></td>
		<td><input type="text" name="expiration_subject" value="<?php echo htmlspecialchars($this->conf->expiration_subject); ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tr_expiration_text">
		<td><?php echo JText::_('ADSMANAGER_EXPIRATION_TEXT'); ?></td>
		<td><?php echo $editor->display( 'expiration_text', $this->conf->expiration_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
</table>
<script>
    jQ(document).ready(function(){
        function displayTr(idSelect, idTr) {
            v = jQ('#'+idSelect+' option:selected').val();
            if(v == 0) {
                jQ('#'+idTr+'_subject').hide();
                jQ('#'+idTr+'_text').hide();
            }else{
                jQ('#'+idTr+'_subject').show();
                jQ('#'+idTr+'_text').show();
            }
        }
        
        displayTr('send_email_on_new', 'tr_admin_new');
        displayTr('send_email_on_update', 'tr_admin_update');
        displayTr('send_email_on_new_to_user', 'tr_new');
        displayTr('send_email_on_update_to_user', 'tr_update');
        displayTr('send_email_on_waiting_validation_to_user', 'tr_waiting_validation');
        displayTr('send_email_on_validation_to_user', 'tr_validation');
        displayTr('send_email_on_expiration_to_user', 'tr_expiration');
        
        jQ('#send_email_on_new').change(function(){
            displayTr('send_email_on_new', 'tr_admin_new');
        });
        jQ('#send_email_on_update').change(function(){
            displayTr('send_email_on_update', 'tr_admin_update');
        });
        jQ('#send_email_on_new_to_user').change(function(){
            displayTr('send_email_on_new_to_user', 'tr_new');
        });
        jQ('#send_email_on_update_to_user').change(function(){
            displayTr('send_email_on_update_to_user', 'tr_update');
        });
        jQ('#send_email_on_waiting_validation_to_user').change(function(){
            displayTr('send_email_on_waiting_validation_to_user', 'tr_waiting_validation');
        });
        jQ('#send_email_on_validation_to_user').change(function(){
            displayTr('send_email_on_validation_to_user', 'tr_validation');
        });
        jQ('#send_email_on_expiration_to_user').change(function(){
            displayTr('send_email_on_expiration_to_user', 'tr_expiration');
        });
    });
</script>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_CONTACT'), "contact-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_SHOW_CONTACT'); ?></td>
		<td>
		<select id='show_contact' name='show_contact'>
			<option value='1' <?php if ($this->conf->show_contact == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SHOW_CONTACT_LOGGED_ONLY'); ?></option>
			<option value='0' <?php if ($this->conf->show_contact == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_SHOW_CONTACT_ALL'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_SHOW_CONTACT_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_FULLNAME'); ?></td>
		<td>
		<select id='display_fullname' name='display_fullname'>
			<option value='1' <?php if ($this->conf->display_fullname == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->display_fullname == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_DISPLAY_FULLNAME_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_ALLOW_ATTACHMENT'); ?></td>
		<td>
		<select id='allow_attachement' name='allow_attachement'>
			<option value='1' <?php if ($this->conf->allow_attachement == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->allow_attachement == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_ALLOW_ATTACHMENT_LONG'); ?></td>
	</tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_NUMBER_ALLOW_ATTACHEMENT');?></td>
	  <td><input type="text" name="params_number_allow_attachement" value=<?php echo $this->conf->number_allow_attachement; ?> /></td>
	  <td></td>
    </tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_CONTACT_BY_PMS'); ?></td>
		<td>
		<select id='allow_contact_by_pms' name='allow_contact_by_pms'>
			<option value='1' <?php if ($this->conf->allow_contact_by_pms == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->allow_contact_by_pms == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_CONTACT_BY_PMS_LONG'); ?></td>
	</tr>
    
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_DISPLAY'); ?></td>
		<td>
		<select id='email_display' name='email_display'>
			<option value='2' <?php if ($this->conf->email_display == 2) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_EMAIL_DISPLAY_FORM');?></option>
			<option value='1' <?php if ($this->conf->email_display == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_EMAIL_DISPLAY_IMAGE');?></option>
			<option value='0' <?php if ($this->conf->email_display == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_EMAIL_DISPLAY_LINK');?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_EMAIL_DISPLAY_LONG'); ?></td>
	</tr>
</table>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_IMAGE'), "image-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_NB_IMAGES'); ?></td>
		<td><input type="text" name="nb_images" value=<?php echo $this->conf->nb_images; ?> /></td>
		<td><?php echo JText::_('ADSMANAGER_NB_IMAGES_LONG'); ?></td>
	</tr>
	<tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_IMAGE_SIZE');?></td>
	  <td><input type="text" name="max_image_size" value=<?php echo $this->conf->max_image_size; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_IMAGE_SIZE_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_IMAGE_WIDTH');?></td>
	  <td><input type="text" name="max_width" value=<?php echo $this->conf->max_width; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_IMAGE_WIDTH_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_IMAGE_HEIGHT');?></td>
	  <td><input type="text" name="max_height" value=<?php echo $this->conf->max_height; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_IMAGE_HEIGHT_LONG');?></td>
    </tr>
     <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_MEDIUM_IMAGE_WIDTH');?></td>
	  <td><input type="text" name="params_max_width_m" value=<?php echo @$this->conf->max_width_m; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_MEDIUM_IMAGE_WIDTH_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_MEDIUM_IMAGE_HEIGHT');?></td>
	  <td><input type="text" name="params_max_height_m" value=<?php echo @$this->conf->max_height_m; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_MEDIUM_IMAGE_HEIGHT_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_THUMBNAIL_WIDTH');?></td>
	  <td><input type="text" name="max_width_t" value=<?php echo $this->conf->max_width_t; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_THUMBNAIL_WIDTH_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_THUMBNAIL_HEIGHT');?></td>
	  <td><input type="text" name="max_height_t" value=<?php echo $this->conf->max_height_t; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_THUMBNAIL_HEIGHT_LONG');?></td>
    </tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_IMAGE_TAG'); ?></td>
		<td><input type="text" name="tag" value="<?php echo $this->conf->tag; ?>" /></td>
		<td><?php echo JText::_('ADSMANAGER_IMAGE_TAG_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_IMAGE_DISPLAY'); ?></td>
		<td>
		<select id='image_display' name='image_display'>
			<option value='default' <?php if ($this->conf->image_display == 'default') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_IMAGE_DISPLAY_DEFAULT'); ?></option>
			<option value='lytebox' <?php if ($this->conf->image_display == 'lytebox') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_IMAGE_DISPLAY_LYTEBOX'); ?></option>
			<option value='highslide' <?php if ($this->conf->image_display == 'highslide') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_IMAGE_DISPLAY_HIGHSLIDE'); ?></option>
			<option value='popup' <?php if ($this->conf->image_display == 'popup') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_IMAGE_DISPLAY_POPUP'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_IMAGE_DISPLAY_LONG'); ?></td>
	</tr>
	<tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATIMAGE_WIDTH');?></td>
	  <td><input type="text" name="cat_max_width" value=<?php echo $this->conf->cat_max_width; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATIMAGE_WIDTH_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATIMAGE_HEIGHT');?></td>
	  <td><input type="text" name="cat_max_height" value=<?php echo $this->conf->cat_max_height; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATIMAGE_HEIGHT_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATTHUMBNAIL_WIDTH');?></td>
	  <td><input type="text" name="cat_max_width_t" value=<?php echo $this->conf->cat_max_width_t; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATTHUMBNAIL_WIDTH_LONG');?></td>
    </tr>
    <tr>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATTHUMBNAIL_HEIGHT');?></td>
	  <td><input type="text" name="cat_max_height_t" value=<?php echo $this->conf->cat_max_height_t; ?> /></td>
	  <td><?php echo JText::_('ADSMANAGER_MAX_CATTHUMBNAIL_HEIGHT_LONG');?></td>
    </tr>
    <tr>
		<td><?php echo JText::_('ADSMANAGER_IMAGE_SCALING'); ?></td>
		<td>
		<select id='image_scaling' name='params_image_scaling'>
			<option value='0' <?php if (@$this->conf->image_scaling == '0') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FILL_INTO_AREA'); ?></option>
			<option value='1' <?php if (@$this->conf->image_scaling == '1') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_CROP_TO_FIT'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_IMAGE_SCALING_DESC'); ?></td>
	</tr>
</table>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_TEXT'), "text-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_FRONTPAGE'); ?></td>
		<td><?php echo $editor->display( 'fronttext',  $this->conf->fronttext, '100%', '350', '75', '20' ); ?></td>
		<td><?php echo JText::_('ADSMANAGER_FRONTPAGE_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_RULES'); ?></td>
		<td><?php echo $editor->display( 'rules_text', $this->conf->rules_text , '100%', '350', '75', '20' ) ; ?></td>
		<td>&nbsp;</td>
	</tr>
</table>

<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JTEXT::_('ADSMANAGER_BANNEDWORDS'), "banned-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_BANNEDWORDS'); ?></td>
		<td><textarea name="bannedwords" rows="20"><?php echo $this->conf->bannedwords; ?></textarea></td>
		<td><?php echo JText::_('ADSMANAGER_BANNEDWORDS_LONG'); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_REPLACEWORD'); ?></td>
		<td><input type="text" name="replaceword" value="<?php echo $this->conf->replaceword; ?>" /></td>
		<td>&nbsp;</td>
	</tr>
</table>

<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_THIRD_PARTY'), "thirdparty-page");
?>

<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_PROFILE_COMPONENT'); ?></td>
		<td>
		<select id='comprofiler' name='comprofiler'>
			<?php if (file_exists (JPATH_ADMINISTRATOR ."/components/com_community")) { ?>
			<option value='4' <?php if ($this->conf->comprofiler == 4) { echo "selected"; } ?>><?php echo "Jomsocial - ".JText::_('ADSMANAGER_FULL'); ?></option>
			<option value='3' <?php if ($this->conf->comprofiler == 3) { echo "selected"; } ?>><?php echo "Jomsocial - ".JText::_('ADSMANAGER_PROFILE'); ?></option>
			<?php } ?>
			<?php if (file_exists (JPATH_ADMINISTRATOR ."/components/com_comprofiler")) { ?>
				<option value='2' <?php if ($this->conf->comprofiler == 2) { echo "selected"; } ?>><?php echo "Community Builder - ".JText::_('ADSMANAGER_FULL'); ?></option>
				<option value='1' <?php if ($this->conf->comprofiler == 1) { echo "selected"; } ?>><?php echo "Community Builder - ".JText::_('ADSMANAGER_PROFILE'); ?></option>
			<?php } ?>
			<option value='0' <?php if ($this->conf->comprofiler == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_COMMUNITY_BUILDER_LONG'); ?></td>
	</tr>
</table>
<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JTEXT::_('ADSMANAGER_JAVASCRIPT'), "javascript-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_JQUERY'); ?></td>
		<td>
		 <select id='jquery' name='jquery'>
			<option value='1' <?php if ($this->conf->jquery == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->jquery == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		  </select>
		</td>
		<td></td>
	</tr>
</table>


<?php   
echo $tabs->endPanel();
echo $tabs->startPanel(JText::_('ADSMANAGER_TAB_EXPIRATION'), "Expiration-page");
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr>
		<td><?php echo JText::_('ADSMANAGER_EXPIRATION'); ?></td>
		<td>
		<select id='expiration' name='expiration'>
			<option value='1' <?php if ($this->conf->expiration == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->expiration == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_AD_DURATION'); ?></td>
		<td><input type="text" name="ad_duration" value="<?php echo $this->conf->ad_duration; ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_RECALL'); ?></td>
		<td>
		<select id='recall' name='recall'>
			<option value='1' <?php if ($this->conf->recall == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_YES'); ?></option>
			<option value='0' <?php if ($this->conf->recall == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_RECALL_TIME'); ?></td>
		<td><input type="text" name="recall_time" value="<?php echo $this->conf->recall_time; ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_AFTER_EXPIRATION'); ?></td>
		<td>
		<select id='after_expiration' name='after_expiration'>
			<option value='delete' <?php if ($this->conf->after_expiration == 'delete') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_DELETE'); ?></option>
			<option value='unpublish' <?php if ($this->conf->after_expiration == 'unpublish') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_UNPUBLISH'); ?></option>
			<option value='archive' <?php if ($this->conf->after_expiration == 'archive') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_ARCHIVE'); ?></option>
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo JText::_('ADSMANAGER_ARCHIVE_CATEGORY'); ?></td>
		<td>
		<select name="archive_catid" id="archive_catid">
		<?php $this->selectCategories(0,"Root >> ",$this->cats,$this->conf->archive_catid,0); ?>
		</select>
		</td>
		<td><?php echo JText::_('ADSMANAGER_ARCHIVE_CATEGORY_LONG'); ?></td>
	</tr>
</table>   
<?php
echo $tabs->endPanel();
echo $tabs->endPane();
?>

<input type="hidden" name="option" value="com_adsmanager" />

<input type="hidden" name="task" value="" />

<input type="hidden" name="id" value=<?php echo $this->conf->id ?> />

<input type="hidden" name="c" value="configuration" />
</form> 