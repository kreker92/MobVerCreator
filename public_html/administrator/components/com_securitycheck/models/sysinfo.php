<?php
/**
* Modelo SysInfos para el Componente Securitycheck
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Chequeamos si el archivo está incluído en Joomla!
defined('_JEXEC') or die();


/**
* Modelo Securitycheck
*/
class SecuritychecksModelSysinfo extends JModelLegacy
{

/* @var array somme system values  */
protected $info = null;

/**
 * method to get the system information
 *
 * @return array system information values
 */
public function &getInfo()
{
	if (is_null($this->info)){
		$this->info = array();
		$version = new JVersion;
		$platform = new JPlatform;
		$db = JFactory::getDBO();
				
		// Obtenemos el tamaño de la variable 'max_allowed_packet' de Mysql
		$db->setQuery('SHOW VARIABLES LIKE \'max_allowed_packet\'');
		$keys = $db->loadObjectList();
		$array_val = get_object_vars($keys[0]);
		$tamanno_max_allowed_packet = (int) ($array_val["Value"]/1024/1024);
		
		// Obtenemos el tamaño máximo de memoria establecido
		$params = JComponentHelper::getParams('com_securitycheck');
		$memory_limit = $params->get('memory_limit','128M');
		
		$this->info['phpversion']	= phpversion();
		$this->info['version']		= $version->getLongVersion();
		$this->info['platform']		= $platform->getLongVersion();
		$this->info['max_allowed_packet']		= $tamanno_max_allowed_packet;
		$this->info['memory_limit']		= $memory_limit;
	}
	return $this->info;
}

}