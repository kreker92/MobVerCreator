<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\Redirect;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class Redirect extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Redirect';
	static $group = array('utilities' => 'Utilities');

	function execute(&$form, $action_id){
		$config = !empty($form->actions_config[$action_id]) ? $form->actions_config[$action_id] : array();
		$config = new \GCore\Libs\Parameter($config);

		if(strlen($config->get('url', ''))){
			\GCore\Libs\Env::redirect($config->get('url', ''));
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config redirect_action_config', 'redirect_action_config_{N}');
		echo \GCore\Helpers\Html::formSecStart();

		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][url]', array('type' => 'text', 'label' => l_('CF_REDIRECT_URL'), 'class' => 'XL', 'sublabel' => l_('CF_REDIRECT_URL_DESC')));

		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}