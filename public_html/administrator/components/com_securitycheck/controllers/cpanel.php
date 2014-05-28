<?php
/**
* Securitycheck Pro Cpanel Controller
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Control Panel controller class
 *
 */
class SecuritychecksControllerCpanel extends JControllerLegacy
{
	public function  __construct() {
		parent::__construct();
		
	}

	/**
	 * Displays the Control Panel 
	 */
	public function display()
	{
		JRequest::setVar( 'view', 'cpanel' );
		
		// Display the panel
		parent::display();
	}

	/* Acciones al pulsar el botón para establecer 'Easy Config' */
	function Set_Easy_Config(){
		$model = $this->getModel("cpanel");
	
		$applied = $model->Set_Easy_Config();
		
		echo $applied;
	}
	
	/* Acciones al pulsar el botón para establecer 'Default Config' */
	function Set_Default_Config(){
		$model = $this->getModel("cpanel");
	
		$applied = $model->Set_Default_Config();
		
		echo $applied;
	}
}