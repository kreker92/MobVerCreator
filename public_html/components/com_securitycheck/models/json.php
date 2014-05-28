<?php
/**
* @ author Jose A. Luque
* @ Copyright (c) 2013 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Protect from unauthorized access
defined('_JEXEC') or die();

class SecuritychecksModelJson extends SecuritycheckModel
{

	const	STATUS_OK					= 200;	// Normal reply
	const	STATUS_NOT_AUTH				= 401;	// Invalid credentials
	const	STATUS_NOT_ALLOWED			= 403;	// Not enough privileges
	const	STATUS_NOT_FOUND			= 404;  // Requested resource not found
	const	STATUS_INVALID_METHOD		= 405;	// Unknown JSON method
	const	STATUS_ERROR				= 500;	// An error occurred
	const	STATUS_NOT_IMPLEMENTED		= 501;	// Not implemented feature
	const	STATUS_NOT_AVAILABLE		= 503;	// Remote service not activated

	const	CIPHER_RAW			= 1;	// Data in plain-text JSON
	const	CIPHER_AESCBC128		= 2;	// Data in AES-128 standard (CBC) mode encrypted JSON
	const	CIPHER_AESCBC256		= 3;	// Data in AES-256 standard (CBC) mode encrypted JSON

	private	$json_errors = array(
		'JSON_ERROR_NONE' => 'No error has occurred (probably emtpy data passed)',
		'JSON_ERROR_DEPTH' => 'The maximum stack depth has been exceeded',
		'JSON_ERROR_CTRL_CHAR' => 'Control character error, possibly incorrectly encoded',
		'JSON_ERROR_SYNTAX' => 'Syntax error'
	);
	
	// Inicializamos las variables
	private	$status = 200;  // Estado de la petición
	private $cipher = 2;	// Método usado para cifrar los datos
	private $clear_data = '';		// Datos enviados en la petición del cliente (ya en claro)
	private $data = '';		// Datos devueltos al cliente
	private $password = null;
	private $method_name = null;

	/* Función que realiza una determinada función según los parámetros especificados en la variable pasada como argumento */
	public function execute($json)
	{
				
		// Comprobamos si el frontend está habilitado
		$config = $this->Config('controlcenter');
		if ( !array_key_exists('control_center_enabled', $config) ) {
			$enabled = false;
		} else {
			$enabled = $config['control_center_enabled'];
		}
		
		if ( array_key_exists('secret_key', $config) ) {
			$this->password = $config['secret_key'];
		} else {
			$this->data = 'Remote password not configured';
			$this->status = self::STATUS_NOT_AUTH;
			$this->cipher = self::CIPHER_RAW;
			return $this->sendResponse();
		}
		
		// Si el frontend no está habilitado, devolvemos un error 503
		if(!$enabled)
		{
			$this->data = 'Access denied';
			$this->status = self::STATUS_NOT_AVAILABLE;
			$this->cipher = self::CIPHER_RAW;
			return $this->sendResponse();
		}
		
		// Decodificamos la petición
		$request = json_decode($json, false);
		
		if(is_null($request))
		{
			// Si no podemos decodificar la petición JSON, devolvemos un error
			$this->data = 'JSON decoding error';
			$this->status = self::STATUS_ERROR;
			$this->cipher = self::CIPHER_RAW;
			return $this->sendResponse();			
		}
		
		// Decodificamos el 'body' de la petición
		if( isset($request->cipher) && isset($request->body) )
		{
		
			switch( $request->cipher )
			{
				case self::CIPHER_RAW:
					if ( ($request->body->task == "getStatus") || ($request->body->task == "checkVuln") || ($request->body->task == "checkLogs") || ($request->body->task == "checkPermissions") || ($request->body->task == "deleteBlocked") || ($request->body->task == "update") ) {
						/* Los resultados de todas las tareas se devuelven cifrados; si recibimos una petición para devolverlos sin cifrar, la rechazamos
							porque será fraudulenta */
						$this->data = 'Go away, hacker!';
						$this->status = self::STATUS_NOT_ALLOWED;
						$this->cipher = self::CIPHER_RAW;
						return $this->sendResponse();
					}
					break;
					
				case self::CIPHER_AESCBC128:
					if ( !is_null($request->body->data) ) {
						$this->clear_data = $this->mc_decrypt_128($request->body->data, $this->password);
					}
					break;

				case self::CIPHER_AESCBC256:
					if ( !is_null($request->body->data) ) {
						$this->clear_data = $this->mc_decrypt_256($request->body->data, $this->password);
					}					
					break;
			}
			
			$this->cipher = self::CIPHER_AESCBC128;
			switch( $request->body->task ) {
				case "getStatus":
					$this->getStatus();
					break;
					
				case "checkVuln":
					$this->checkVuln();
					break;
					
				case "checkLogs":
					$this->checkLogs();
					break;
					
				case "checkPermissions":
					$this->checkPermissions();
					break;
					
				case "deleteBlocked":
					$this->deleteBlocked();
					break;
					
				case "update":
					$this->Update();
					break;
					
				case "LatestReleaseInfo":
					$this->LatestReleaseInfo();
					break;
					
				case "UpdateCore":
					$this->UpdateCore();
					break;

				case self::CIPHER_AESCBC256:
									
					break;
				default:
					$this->data = 'Method not configured';
					$this->status = self::STATUS_NOT_FOUND;
					$this->cipher = self::CIPHER_RAW;
					return $this->sendResponse();
			}
			return $this->sendResponse();		
		}
	}
	
	/* Función que empaqueta una respuesta en formato JSON codificado, cifrando los datos si es necesario */
	private function sendResponse()
	{
		// Inicializamos la respuesta
		$response = array(
			'cipher'	=> $this->cipher,
			'body'		=> array(
				'status'		=> $this->status,
				'data'			=> null
			)
		);
		
			
		// Codificamos los datos enviados en formato JSON
		$data = json_encode($this->data);
		
		// Ciframos o no los datos según el método establecido en la petición
		switch($this->cipher)
		{
			case self::CIPHER_RAW:
				break;

			case self::CIPHER_AESCBC128:
				$data = $this->mc_encrypt_128($data, $this->password);
				break;

			case self::CIPHER_AESCBC256:
				$data = $this->mc_encrypt_256($data, $this->password);
				break;
		}

		// Guardamos los datos...
		$response['body']['data'] = $data;

		// ... y los devolvemos al cliente
		return '###' . json_encode($response) . '###';		
	}
	
	/* Extraemos los parámetros del componente */
	private function Config($key_name)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query 
			->select($db->quoteName('storage_value'))
			->from($db->quoteName('#__securitycheck_storage'))
			->where($db->quoteName('storage_key').' = '.$db->quote($key_name));
		$db->setQuery($query);
		$res = $db->loadResult();
		$res = json_decode($res, true);
			
		return $res;
	}

	/* Función que devuelve el estado de la extensión remota  */
	private function getStatus() {
	
		// Import Securitychecks model
		JLoader::import('joomla.application.component.model');
		JLoader::import('cpanel', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_securitycheck' . DIRECTORY_SEPARATOR . 'models');
		JLoader::import('filemanager', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_securitycheck' . DIRECTORY_SEPARATOR . 'models');
		if ( version_compare(JVERSION, '3.0', 'ge') ) {
			$cpanel_model = JModelLegacy::getInstance( 'cpanel', 'SecuritychecksModel');
			$filemanager_model = JModelLegacy::getInstance( 'filemanager', 'SecuritychecksModel');
		} else {
			$cpanel_model = JModel::getInstance( 'cpanel', 'SecuritychecksModel');
			$filemanager_model = JModel::getInstance( 'filemanager', 'SecuritychecksModel');
		}
		
		// Vulnerable components
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(*) FROM #__securitycheck WHERE Vulnerable="Si"';
		$db->setQuery( $query );
		$db->query();	
		$vuln_extensions = $db->loadResult();
		
		// Check for unread logs
		(int) $logs_pending = $cpanel_model->LogsPending();
		
		// Get files with incorrect permissions from database
		$files_with_incorrect_permissions = $filemanager_model->loadStack("filemanager_resume","files_with_incorrect_permissions");
		
		// If permissions task has not been launched, we set a '0' value.
		if ( is_null($files_with_incorrect_permissions) ) {
			$files_with_incorrect_permissions = 0;
		}
		
		// FileManager last check
		$last_check = $filemanager_model->loadStack("filemanager_resume","last_check");
		
		// Get files with incorrect permissions from database
		$files_with_bad_integrity = 0;
		
		// If permissions task has not been launched, whe seet a '0' value.
		if ( is_null($files_with_bad_integrity) ) {
			$files_with_bad_integrity = 0;
		}
		
		// FileIntegrity last check
		$last_check_integrity = 0;
	
		/* Verificamos si el cliente está actualizado */
		require_once JPATH_ROOT.'/administrator/components/com_securitycheck/liveupdate/liveupdate.php';
		$updateInformation = LiveUpdate::getUpdateInformation(1);
		
		/* Verificamos si el core está actualizado */
		require_once JPATH_ROOT.'/administrator/components/com_joomlaupdate/models/default.php';
		JoomlaupdateModelDefault::refreshUpdates();
		$coreInformation = JoomlaupdateModelDefault::getUpdateInformation();
	
		$this->data = array(
			'vuln_extensions'		=> $vuln_extensions,
			'logs_pending'	=> $logs_pending,
			'files_with_incorrect_permissions'		=> $files_with_incorrect_permissions,
			'last_check' => $last_check,
			'files_with_bad_integrity'		=> $files_with_bad_integrity,
			'last_check_integrity' => $last_check_integrity,
			'installed_version'	=> $updateInformation->extInfo->version,
			'hasUpdates'	=> $updateInformation->hasUpdates,
			'coreinstalled'	=>	$coreInformation['installed'],
			'corelatest'	=>	$coreInformation['latest']
		);
	
	}
	
	/* Función que comprueba si existen extensiones vulnerables  */
	private function checkVuln() {
		// Import Securitycheckpros model
		JLoader::import('joomla.application.component.model');
		JLoader::import('securitychecks', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_securitycheck' . DIRECTORY_SEPARATOR . 'models');
		if ( version_compare(JVERSION, '3.0', 'ge') ) {
			$securitycheckpros_model = JModelLegacy::getInstance( 'securitychecks', 'SecuritychecksModel');
		} else {
			$securitycheckpros_model = JModel::getInstance( 'securitychecks', 'SecuritychecksModel');
		}
			
		// Hacemos una nueva comprobación de extensiones vulnerables
		$securitycheckpros_model->chequear_vulnerabilidades();
		
		// Vulnerable components
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(*) FROM #__securitycheck WHERE Vulnerable="Si"';
		$db->setQuery( $query );
		$db->query();	
		$vuln_extensions = $db->loadResult();
		
		$this->data = array(
			'vuln_extensions'		=> $vuln_extensions			
		);
	}
	
	/* Función que comprueba si existen logs por leer  */
	private function checkLogs() {
		// Import Securitycheckpros model
		JLoader::import('joomla.application.component.model');
		JLoader::import('cpanel', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_securitycheck' . DIRECTORY_SEPARATOR . 'models');
		if ( version_compare(JVERSION, '3.0', 'ge') ) {
			$cpanel_model = JModelLegacy::getInstance( 'cpanel', 'SecuritychecksModel');			
		} else {
			$cpanel_model = JModel::getInstance( 'cpanel', 'SecuritychecksModel');			
		}
		
		// Check for unread logs
		(int) $logs_pending = $cpanel_model->LogsPending();
		
		$this->data = array(
			'logs_pending'	=> $logs_pending			
		);
		
	}
	
	/* Función que lanza un chequeo de permisos  */
	private function checkPermissions() {
		// Import Securitycheckpros model
		JLoader::import('joomla.application.component.model');
		JLoader::import('filemanager', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_securitycheck' . DIRECTORY_SEPARATOR . 'models');
		if ( version_compare(JVERSION, '3.0', 'ge') ) {
			$filemanager_model = JModelLegacy::getInstance( 'filemanager', 'SecuritychecksModel');
		} else {
			$filemanager_model = JModel::getInstance( 'filemanager', 'SecuritychecksModel');
		}
		
		$filemanager_model->set_campo_filemanager('files_scanned',0);
		$filemanager_model->set_campo_filemanager('last_check',date('Y-m-d H:i:s'));
		$filemanager_model->set_campo_filemanager('estado','IN_PROGRESS');
		$filemanager_model->scan("permissions");
		
		// Get files with incorrect permissions from database
		$files_with_incorrect_permissions = $filemanager_model->loadStack("filemanager_resume","files_with_incorrect_permissions");
		
		// If permissions task has not been launched, we set a '0' value.
		if ( is_null($files_with_incorrect_permissions) ) {
			$files_with_incorrect_permissions = 0;
		}
		
		// FileManager last check
		$last_check = $filemanager_model->loadStack("filemanager_resume","last_check");
		
		$this->data = array(
			'files_with_incorrect_permissions'		=> $files_with_incorrect_permissions,
			'last_check' => $last_check
		);
	
	}
	
	/* Borra los logs pertenecientes a intentos de acceso bloqueados */
	private function deleteBlocked() {
		// Import Securitycheckpros model
		JLoader::import('joomla.application.component.model');
		JLoader::import('cpanel', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_securitycheck' . DIRECTORY_SEPARATOR . 'models');
		if ( version_compare(JVERSION, '3.0', 'ge') ) {
			$cpanel_model = JModelLegacy::getInstance( 'cpanel', 'SecuritychecksModel');			
		} else {
			$cpanel_model = JModel::getInstance( 'cpanel', 'SecuritychecksModel');			
		}
	
		// Vulnerable components
		$db = JFactory::getDBO();
		$query = 'DELETE FROM #__securitycheck_logs WHERE ( `type` = "IP_BLOCKED" OR `type` = "IP_BLOCKED_DINAMIC" )';
		$db->setQuery( $query );
		$db->query();	
				
		// Check for unread logs
		(int) $logs_pending = $cpanel_model->LogsPending();
		
		$this->data = array(
			'logs_pending'	=> $logs_pending			
		);
	}
	
	private function Update()
	{
		// Download 
		require_once JPATH_ROOT.'/administrator/components/com_securitycheck/liveupdate/liveupdate.php';
		require_once JPATH_ROOT.'/administrator/components/com_securitycheck/liveupdate/classes/model.php';

		// Do we need to update?
		$updateInformation = LiveUpdate::getUpdateInformation();
		if(!$updateInformation->hasUpdates) {
			return (object)array(
				'download'	=> 0
			);
		}

		$model = new LiveupdateModel();
		$ret = $model->download();

		$session = JFactory::getSession();
		$target		= $session->get('target', '', 'liveupdate');
		$tempdir	= $session->get('tempdir', '', 'liveupdate');

		if(!$ret) {
			// An error ocurred :(
			$this->data = 'Could not download the update package';
			$this->status = self::STATUS_ERROR;
			$this->cipher = self::CIPHER_RAW;
			return $this->sendResponse();			
		} else {
			// Extract
			$ret = $model->extract();

			JLoader::import('joomla.filesystem.file');
			JFile::delete($target);
			
			if(!$ret) {
				// An error ocurred :(
				$this->data = 'Could not extract the update package';
				$this->status = self::STATUS_ERROR;
				$this->cipher = self::CIPHER_RAW;
				return $this->sendResponse();
			} else {
				// Install
				$ret = $model->install();

				if(!$ret) {
					// An error ocurred :(
					$this->data = 'Could not install the update package';
					$this->status = self::STATUS_ERROR;
					$this->cipher = self::CIPHER_RAW;
					return $this->sendResponse();					
				} else {
					// Update cleanup
					$ret = $model->cleanup();

					JLoader::import('joomla.filesystem.file');
					JFile::delete($target);
					
					// Update product info
					$this->getStatus();
				}
			}
		}
	}
	
	/* Obtiene información de la última versión publicada */
	private function LatestReleaseInfo() {
		/* Preguntamos por la información de la última versión */
		require_once JPATH_ROOT.'/administrator/components/com_securitycheck/liveupdate/liveupdate.php';
		$updateInformation = LiveUpdate::getUpdateInformation(1);
		
		$this->data = array(
			'latest_version'	=> $updateInformation->version,
			'release_notes'	=> $updateInformation->releasenotes
		);
	
	}
	
	/* Función queactualiza el Core de Joomla a la última versión disponible  */
	private function UpdateCore() {
		
		// Cargamos las librerías necesarias
		require_once JPATH_ROOT.'/administrator/components/com_joomlaupdate/models/default.php';
				
		// Refrescamos la información de las actualizaciones
		JoomlaupdateModelDefault::refreshUpdates();
		
		// Extraemos la url de descarga
		$coreInformation = JoomlaupdateModelDefault::getUpdateInformation();
		// Realizamos la instalación pasando la url de descarga
		$result = $this->install($coreInformation['object']->downloadurl->_data);
		JoomlaupdateModelDefault::finaliseUpgrade();
		
		if ( !$result ) {
			$this->status = self::STATUS_ERROR;			
		} else {
			$this->data = array(
				'coreinstalled'	=> $coreInformation['latest']
			);
		}		
	
	}
	
	
	/**
	 * Install an extension from either folder, url or upload.
	 *
	 * @return  boolean result of install
	 *
	 * @since   1.5
	 */
	public function install($url)
	{
		$this->setState('action', 'install');

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');
		$app = JFactory::getApplication();

		// Load installer plugins for assistance if required:
		JPluginHelper::importPlugin('installer');
		$dispatcher = JEventDispatcher::getInstance();

		$package = null;

		// This event allows an input pre-treatment, a custom pre-packing or custom installation (e.g. from a JSON description)
		$results = $dispatcher->trigger('onInstallerBeforeInstallation', array($this, &$package));

		if (in_array(true, $results, true))
		{
			return true;
		}
		elseif (in_array(false, $results, true))
		{
			return false;
		}

		$installType = 'url';

		if ($package === null)
		{
			switch ($installType)
			{
				case 'folder':
					// Remember the 'Install from Directory' path.
					$app->getUserStateFromRequest($this->_context . '.install_directory', 'install_directory');
					$package = $this->_getPackageFromFolder();
					break;

				case 'upload':
					$package = $this->_getPackageFromUpload();
					break;

				case 'url':
					$package = $this->_getPackageFromUrl($url);
					break;

				default:
					$app->setUserState('com_installer.message', JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND'));

					return false;
					break;
			}
		}

		// This event allows a custom installation of the package or a customization of the package:
		$results = $dispatcher->trigger('onInstallerBeforeInstaller', array($this, &$package));

		if (in_array(true, $results, true))
		{
			return true;
		}
		elseif (in_array(false, $results, true))
		{
			if (in_array($installType, array('upload', 'url')))
			{
				JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
			}

			return false;
		}

		// Was the package unpacked?
		if (!$package || !$package['type'])
		{
			if (in_array($installType, array('upload', 'url')))
			{
				JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
			}

			$app->setUserState('com_installer.message', JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			return false;
		}

		// Get an installer instance
		$installer = JInstaller::getInstance();

		// Install the package
		if (!$installer->install($package['dir']))
		{
			// There was an error installing the package
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
			$result = false;
		}
		else
		{
			// Package installed sucessfully
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
			$result = true;
		}

		// This event allows a custom a post-flight:
		$dispatcher->trigger('onInstallerAfterInstaller', array($this, &$package, $installer, &$result, &$msg));

		// Set some model state values
		$app	= JFactory::getApplication();
		$app->enqueueMessage($msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$app->setUserState('com_installer.message', $installer->message);
		$app->setUserState('com_installer.extension_message', $installer->get('extension_message'));
		$app->setUserState('com_installer.redirect_url', $installer->get('redirect_url'));

		// Cleanup the install files
		if (!is_file($package['packagefile']))
		{
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		return $result;
	}
	
	
	/**
	 * Install an extension from a URL
	 *
	 * @return  Package details or false on failure
	 *
	 * @since   1.5
	 */
	protected function _getPackageFromUrl($url)
	{
		$input = JFactory::getApplication()->input;

		// Get the URL of the package to install
		//$url = $input->getString('install_url');

		// Did you give us a URL?
		if (!$url)
		{
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
			return false;
		}

		// Handle updater XML file case:
		if (preg_match('/\.xml\s*$/', $url))
		{
			jimport('joomla.updater.update');
			$update = new JUpdate;
			$update->loadFromXML($url);
			$package_url = trim($update->get('downloadurl', false)->_data);
			if ($package_url)
			{
				$url = $package_url;
			}
			unset($update);
		}

		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file)
		{
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}

		$config   = JFactory::getConfig();
		$tmp_dest = $config->get('tmp_path');

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file, true);

		return $package;
	}
}