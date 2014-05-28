<?php
/**
* Modelo Securitychecks para el Componente Securitycheck
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Chequeamos si el archivo está incluído en Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );
jimport( 'joomla.version' );
jimport( 'joomla.access.rule' );
jimport( 'joomla.application.component.helper' );
jimport( 'joomla.application.component.controller' );

/**
* Modelo Securitycheck
*/
class SecuritychecksModelCpanel extends JModelLegacy
{
/**
* Array de datos
* @var array
*/
var $_data;
/**
/**
* Total items
* @var integer
*/
var $_total = null;
/**
/**
* Objeto Pagination
* @var object
*/
var $_pagination = null;
/**
* Columnas de #__securitycheck
* @var integer
*/
var $_dbrows = null;

function __construct()
{
	parent::__construct();
	
	if ( strstr( strtolower( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ) ), 'apache' ) ) {
		$server = 'apache';
	} else if ( strstr( strtolower( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ) ), 'nginx' ) ) {
		$server = 'nginx';
	}
		
	$mainframe = JFactory::getApplication();
	$mainframe->SetUserState("server",$server);
	
	/* Establecemos el valor de 'max_allowed_packet' a 500MB para permitir que se carguen los escaneos en la BBDD. Si este valor es demasiado bajo, por alguna razón el
		método 'insertobject' de JDatabase no dispara una excepción, por lo que el script se detiene. En Joomla 3.x esto no es necesario porque la excepción se dispara
		sin problemas 
	$db = $this->getDbo();
	$db->setQuery('SET GLOBAL max_allowed_packet=524288000');
	$db->query(); */
	
}

/* Función para determinar si el plugin pasado como argumento ('1' -> Securitycheck Pro)  está habilitado o deshabilitado*/
function PluginStatus($opcion) {
		
	$db = JFactory::getDBO();
	if ( $opcion == 1 ) {
		$query = 'SELECT enabled FROM #__extensions WHERE name="System - Securitycheck"';
	}
	$db->setQuery( $query );
	$db->query();
	$enabled = $db->loadResult();
	
	return $enabled;
}

/* Función que determina el número de logs marcados como "no leido"*/
function LogsPending() {
		
	$db = JFactory::getDBO();
	$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE marked=0';
	$db->setQuery( $query );
	$db->query();
	$enabled = $db->loadResult();
	
	return $enabled;
}

/* Función que obtiene el id del plugin de: '1' -> Securitycheck Pro  */
function get_plugin_id($opcion) {

	$db = JFactory::getDBO();
	if ( $opcion == 1 ) {
		$query = 'SELECT extension_id FROM #__extensions WHERE name="System - Securitycheck" and type="plugin"';
	} 
	$db->setQuery( $query );
	$db->query();
	$id = $db->loadResult();
	
	return $id;
}

/* Función que busca logs por fecha */
function LogsByDate($opcion) {
	
	// Inicializamos la variable
	$query = null;
	
	$db = JFactory::getDBO();
	switch ($opcion){
		case 'last_year':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE YEAR(`time`) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))';
			break;
		case 'this_year':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE YEAR(`time`) = YEAR(CURDATE())';
			break;
		case 'last_month':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE (MONTH(`time`) = MONTH(CURDATE())-1) AND (YEAR(`time`) = YEAR(CURDATE()))';
			break;
		case 'this_month':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE (MONTH(`time`) = MONTH(CURDATE())) AND (YEAR(`time`) = YEAR(CURDATE()))';
			break;
		case 'last_7_days':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE `time` BETWEEN DATE_SUB(NOW(),INTERVAL 1 WEEK) AND NOW()';
			break;
		case 'yesterday':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE (DAYOFMONTH(`time`) = DAYOFMONTH(CURDATE())-1) AND (MONTH(`time`) = MONTH(CURDATE())) AND (YEAR(`time`) = YEAR(CURDATE())) ';
			break;
		case 'today':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE `time` > DATE_SUB(NOW(), INTERVAL 1 DAY)';
			break;
	}
	
	$db->setQuery( $query );
	$db->query();
	$result = $db->loadResult();
	
	return $result;
}

/* Función que busca logs por tipo */
function LogsByType($opcion) {
	
	// Inicializamos la variable
	$query = null;
	
	$db = JFactory::getDBO();
	switch ($opcion){
		case 'total_firewall_rules':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE ( `type` = "XSS" OR `type` = "SQL_INJECTION" OR `type` = "LFI" OR `type` = "SECOND_LEVEL" OR `type` LIKE \'%_BASE64\' )';
			break;
		case 'total_blocked_access':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE ( `type` = "IP_BLOCKED" OR `type` = "IP_BLOCKED_DINAMIC" )';
			break;
		case 'total_user_session_protection':
			$query = 'SELECT COUNT(*) FROM #__securitycheck_logs WHERE ( `type` = "USER_AGENT_MODIFICATION" OR `type` = "REFERER_MODIFICATION" OR `type` = "SESSION_PROTECTION" OR `type` = "SESSION_HIJACK_ATTEMPT" )';
			break;
		
	}
	
	$db->setQuery( $query );
	$db->query();
	$result = $db->loadResult();
	
	return $result;
}

/* Función que modifica los valores del Firewall web para aplicar una configuración básica de los filtros */
function Set_Easy_Config() {
	
	// Inicializamos las variables
	$query = null;
	$applied = true;
	
	$db = JFactory::getDBO();
	
	// Obtenemos el 'extension_id' del Firewall Web, disponible en la tabla '#__extensions'
	$query = $db->getQuery(true)
		->select(array($db->quoteName('extension_id')))
		->from($db->quoteName('#__extensions'))
		->where($db->quoteName('name').' = '.$db->quote('System - Securitycheck'));
	$db->setQuery($query);
	$extension_id = $db->loadResult();
	
	// Obtenemos los valores de las distintas opciones del Firewall Web
	$db = $this->getDbo();
	$query = $db->getQuery(true)
		->select(array($db->quoteName('params')))
		->from($db->quoteName('#__extensions'))
		->where($db->quoteName('name').' = '.$db->quote('System - Securitycheck'));
	$db->setQuery($query);
	$params = $db->loadResult();
	$params = json_decode($params, true);
	
	if(!empty($params)) {
		
		// Guardamos la configuración anterior
		$previous_params = $params;
		
		// Parámetros que se desactivan o cuyo valor se deja en blanco para evitar falsos positivos
		$params['check_header_referer'] = "0";
		$params['duplicate_backslashes_exceptions'] = "*";
		$params['line_comments_exceptions'] = "*";
		$params['using_integers_exceptions'] = "*";
		$params['escape_strings_exceptions'] = "*";
		
		$object = (object) array(
			'extension_id'	=> (int) $extension_id,
			'params'	=> utf8_encode(json_encode($params))			
		);
				 
		try {
			$result = $db->updateObject('#__extensions', $object, 'extension_id');			
		} catch (Exception $e) {	
			$applied = false;
		}
		
		// Actualizamos el valor del campo que contendrá si se ha aplicado o no esta configuración
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__securitycheck_storage'))
			->where($db->quoteName('storage_key').' = '.$db->quote('easy_config'));
		$db->setQuery($query);
		$db->query();
		
		$object = (object)array(
			'storage_key'	=> 'easy_config',
			'storage_value'	=> utf8_encode(json_encode(array(
				'applied'		=> true,
				'previous_config'		=> $previous_params
			)))
		);
			
		try {
			$db->insertObject('#__securitycheck_storage', $object);
		} catch (Exception $e) {		
			$applied = false;
		}
	} else {
		$applied = false;
	}
	
	return $applied;
}

/* Función que obtiene si se ha aplicado la opción 'Easy config' */
function Get_Easy_Config() {
	
	// Inicializamos las variables
	$query = null;
	$result = false;
	
	$db = JFactory::getDBO();
	
	$query = $db->getQuery(true)
		->select(array($db->quoteName('storage_value')))
		->from($db->quoteName('#__securitycheck_storage'))
		->where($db->quoteName('storage_key').' = '.$db->quote('easy_config'));
	$db->setQuery($query);
	$applied = $db->loadResult();
	$applied = json_decode($applied, true);
		
	if( !(empty($applied)) && ($applied['applied']) ) {
		$result = true;
	}
	
	return $result;
}

/* Función que modifica los valores del Firewall web para aplicar la configuración previa de los filtros */
function Set_Default_Config() {
	
	// Inicializamos las variables
	$query = null;
	$applied = true;
	
	$db = JFactory::getDBO();
	
	// Obtenemos el 'extension_id' del Firewall Web, disponible en la tabla '#__extensions'
	$query = $db->getQuery(true)
		->select(array($db->quoteName('extension_id')))
		->from($db->quoteName('#__extensions'))
		->where($db->quoteName('name').' = '.$db->quote('System - Securitycheck'));
	$db->setQuery($query);
	$extension_id = $db->loadResult();
	
	// Obtenemos los valores de las distintas opciones del Firewall Web
	$db = $this->getDbo();
	$query = $db->getQuery(true)
		->select(array($db->quoteName('params')))
		->from($db->quoteName('#__extensions'))
		->where($db->quoteName('name').' = '.$db->quote('System - Securitycheck'));
	$db->setQuery($query);
	$params = $db->loadResult();
	$params = json_decode($params, true);
	
	// Obtenemos los valores de configuración previos
	$db = $this->getDbo();
	$query = $db->getQuery(true)
		->select(array($db->quoteName('storage_value')))
		->from($db->quoteName('#__securitycheck_storage'))
		->where($db->quoteName('storage_key').' = '.$db->quote('easy_config'));
	$db->setQuery($query);
	$previous_params = $db->loadResult();
	$previous_params = json_decode($previous_params, true);
	
	if(!empty($previous_params)) {
		
		// Parámetros que se desactivan o cuyo valor se deja en blanco para evitar falsos positivos
		$params['check_header_referer'] = $previous_params['previous_config']['check_header_referer'];
		$params['duplicate_backslashes_exceptions'] = $previous_params['previous_config']['duplicate_backslashes_exceptions'];
		$params['line_comments_exceptions'] = $previous_params['previous_config']['line_comments_exceptions'];
		$params['using_integers_exceptions'] = $previous_params['previous_config']['using_integers_exceptions'];
		$params['escape_strings_exceptions'] = $previous_params['previous_config']['escape_strings_exceptions'];
		 
		$object = (object) array(
			'extension_id'	=> (int) $extension_id,
			'params'	=> utf8_encode(json_encode($params))
		);
				 
		try {
			$result = $db->updateObject('#__extensions', $object, 'extension_id');			
		} catch (Exception $e) {	
			$applied = false;
		}
		
		// Actualizamos el valor del campo que contendrá si se ha aplicado o no esta configuración
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__securitycheck_storage'))
			->where($db->quoteName('storage_key').' = '.$db->quote('easy_config'));
		$db->setQuery($query);
		$db->query();
		
		$object = (object)array(
			'storage_key'	=> 'easy_config',
			'storage_value'	=> utf8_encode(json_encode(array(
				'applied'		=> false,
				'previous_config'		=> null
			)))
		);
			
		try {
			$db->insertObject('#__securitycheck_storage', $object);
		} catch (Exception $e) {		
			$applied = false;
		}
	} else {
		$applied = false;
	}
	
	return $applied;
}

}