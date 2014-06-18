<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');
jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class AdsmanagerControllerColumns extends TController
{
	function __construct($config= array()) {
		parent::__construct($config);
	
		// Apply, Save & New
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}
	
	function init()
	{
		// Set the default view name from the Request
		$this->_view = $this->getView("admin",'html');

		// Push a model into the view
		$this->_model = $this->getModel( "column");
		if (!JError::isError( $this->_model )) {
			$this->_view->setModel( $this->_model, true );
		}
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$this->init();

		$fieldmodel = $this->getModel( "field");
		$this->_view->setModel($fieldmodel);
		
		$this->_view->setLayout("columns");
		$this->_view->display();
	}
	
	function edit()
	{
		$this->init();
		
		$categorymodel = $this->getModel( "category");
		$this->_view->setModel($categorymodel);
		
		$this->_view->setLayout("editcolumn");
		$this->_view->display();
	}
	
	function add()
	{
		$this->init();
		
		$categorymodel = $this->getModel( "category");
		$this->_view->setModel($categorymodel);
		
		$this->_view->setLayout("editcolumn");
		$this->_view->display();
	}
	
	function globalsave()
	{
		$app = JFactory::getApplication();
	
		$model = $this->getModel("Column");
		
		$model->cleanFieldsByColumn();
	
		$columns = $model->getColumns(null,true);
		foreach($columns as $key => $col) {
			$model->setColumn($col->id,
					JRequest::getVar('title_column_'.$col->id),
					JRequest::getVar('listfields_'.$col->id));
		}
	
		$app->redirect( 'index.php?option=com_adsmanager&c=columns', JText::_('ADSMANAGER_COLUMNS_UPDATED'),'message' );
	}
	
	function save()
	{
		$app = JFactory::getApplication();
		
		$column = JTable::getInstance('column', 'AdsmanagerTable');

		// bind it to the table
		if (!$column -> bind(JRequest::get( 'post' ))) {
			return JError::raiseWarning( 500, $column->getError() );
		}
		
		$field_catsid = JRequest::getVar("catsid", array() );
		$column->catsid = ",".implode(',', $field_catsid).",";
	
		// store it in the db
		if (!$column -> store()) {
			return JError::raiseWarning( 500, $column->getError() );
		}	
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
		
		// Redirect the user and adjust session state based on the chosen task.
		$task = JRequest::getCmd('task');
		switch ($task)
		{
			case 'apply':
				$app->redirect( 'index.php?option=com_adsmanager&c=columns&task=edit&cid[]='.$column->id, JText::_('ADSMANAGER_COLUMN_SAVED') ,'message');
				break;
		
			case 'save2new':
				$app->redirect( 'index.php?option=com_adsmanager&c=columns&task=add', JText::_('ADSMANAGER_COLUMN_SAVED'),'message' );
				break;
		
			default:
				$app->redirect( 'index.php?option=com_adsmanager&c=columns', JText::_('ADSMANAGER_COLUMN_SAVED') ,'message');
			break;
		}
		
	}
	
	function remove()
	{
		$app = JFactory::getApplication();
		
		$ids = JRequest::getVar( 'cid', array(0));
		if (!is_array($ids)) {
			$table = array();
			$table[0] = $ids;
			$ids = $table;
		}
		
		$column = JTable::getInstance('column', 'AdsmanagerTable');
			
		foreach($ids as $id) {
			$column->delete($id);
		}	
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
		
		$app->redirect( 'index.php?option=com_adsmanager&c=columns', JText::_('ADSMANAGER_COLUMN_REMOVED'),'message' );
	}
}
