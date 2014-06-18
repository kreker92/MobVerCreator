<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

class AdsmanagerTableAdsconfiguration extends JTable
{
	var $id = null;
	var $version = null;
	var $ads_per_page = null;
	var $max_image_size = null;
	var $max_width = null; 
	var $max_height = null;
	var $max_width_t = null;
	var $max_height_t = null; 
	var $send_email_on_new = null;
	var $send_email_on_update = null;
	var $auto_publish = null;
	var $tag = null;
	var $fronttext = null;
	var $nb_images = null;
	var $show_contact = null;
	var $root_allowed = null;
	var $comprofiler = null;
	var $email_display = null;
	var $rules_text = null;
	var $display_last = null;
	var $display_expand = null;
	var $display_fullname = null;
	var $expiration = null;
	var $ad_duration = null;
	var $recall = null;
	var $recall_time = null;
	var $recall_text = null;
	var $image_display = null;
	var $cat_max_width = null;
	var $cat_max_height = null;
	var $cat_max_width_t = null;
	var $cat_max_height_t = null;
	var $submission_type = null;
	var $nb_ads_by_user = null;
	var $allow_attachement= null;
	var $allow_contact_by_pms = null;
	var $show_rss = null;
	var $nbcats = null;
	var $show_new = null;
	var $nbdays_new = null;
	var $show_hot = null;
	var $nbhits = null;
	var $bannedwords = null;
	var $replaceword = null;
	var $after_expiration = null;
	var $archive_catid = null;
	var $metadata_mode = null;
	var $autocomplete = null;
	var $jquery = null;
	var $jqueryui = null;
	
	var $nb_last_cols;	
	var $nb_last_rows;	
	var $display_general_menu;	
	var $display_list_sort;	
	var $display_list_search;	
	var $display_inner_pathway;	
	var $display_front;	
	
	var $send_email_on_new_to_user;	
	var $send_email_on_update_to_user;	
	var $send_email_on_validation_to_user;	
	var $send_email_on_expiration_to_user;	
	
	var $new_text;	
	var $update_text;	
	var $admin_new_text;	
	var $admin_update_text;	
	var $waiting_validation_text;	
	var $validation_text;	
	var $expiration_text;
		
	var $new_subject;	
	var $update_subject;	
	var $admin_new_subject;	
	var $admin_update_subject;	
	var $waiting_validation_subject;	
	var $validation_subject;	
	var $expiration_subject;
	
	var $params;
	var $special;
	
    function __construct(&$db)
    {
    	parent::__construct( '#__adsmanager_config', 'id', $db );
    }
    
    function bind($data,$ignore=array()) {
    	parent::bind($data,$ignore=array());
    	
    	// All post values starting with params_ should be saved in json format in params
    	$tmp_params = array();
    	foreach($data as $key => $d) {
    		if (strpos($key,"params_") === 0) {
    			$k = substr($key,7);
    			$tmp_params[$k] = $d;
    		}
    	}
    	$this->params = json_encode($tmp_params);
    	return true;
    }
}
