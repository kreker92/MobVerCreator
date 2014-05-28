<?php
/**
* Modelo ControlCenter para el Componente Securitycheck
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Chequeamos si el archivo está incluído en Joomla!
defined('_JEXEC') or die();
jimport('joomla.html.pagination');

/**
* Modelo Securitycheck
*/
class SecuritychecksModelControlCenter extends SecuritycheckModel
{

/* Genera la clave secreta para cifrar las comunicaciones */
function generateKey() {
	
	// Inicializamos las variables
	$pass = null;
	
	/* Si la función 'openssl_random_pseudo_bytes' existe la usamos para construir la clave porque es criptográficamente segura. Si no
		existe, la construimos nosotros */
		
	if ( function_exists('openssl_random_pseudo_bytes') ) { 
		$bytes =  openssl_random_pseudo_bytes(16);
		$pass   = bin2hex($bytes);		
	} else {
		// Generamos una clave de 32 caracteres de longitud
		$size = 32;
		
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; //available characters
		$pass = '' ;
			
		for ( $i = 1; $i <= $size; $i++ ) {
			$num = rand(1,62);
			$tmp = substr( $chars, $num, 1 );
			$pass = $pass . $tmp;
		}
	}

	return $pass;		
}

}