<?php
require_once('debug.php');

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public static $translator = null;
	
	protected function _initAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => dirname(__FILE__),
		));
		return $autoloader;
	}

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_STRICT');

		$path = dirname(__FILE__) . '/views/helpers';
		$prefix = 'Default_View_Helper_';
		$view->addHelperPath($path, $prefix);

	}
	
	protected function _initNavigation()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		
		$navigation = new Zend_Navigation(array(
			array(
				'label' => $view->translate('Movies'),
				'controller'=>'movie',
				'pages' => array(
					array(
						'label' => $view->translate('Add movie'),
						'controller'=>'movie',
						'action' => 'add'
					),
					array(
						'label' => $view->translate('Import votes from IMDb'),
						'controller'=>'movie',
						'action' => 'import'
					),
				)
			),
			array(
				'label' => $view->translate('Activity'),
				'controller' => 'activity',
			),
			array(
				'label' => $view->translate('Users'),
				'controller' => 'user',
			),
			array(
				'label' => $view->translate('FAQ'),
				'controller' => 'faq',
			),
		
		));
		
		$view->navigation($navigation);
	}
	
	protected function _initSession()
	{
		Zend_Session::setOptions(array('name' => 'mqueue'));
	}

	protected function _initLanguage()
	{
		$session = new Zend_Session_Namespace();
		
		// handle language switch
		if (isset($_GET['lang']))
		{
			$session->locale = $_GET['lang'];
		}

		if (isset($session->locale))
		{
			$locale = new Zend_Locale($session->locale);
		}
		else
		{
			$locale = new Zend_Locale(); // autodetect browser
		}
		Zend_Registry::set('Zend_Locale', $locale);

		$adapter = new Zend_Translate('gettext', APPLICATION_PATH . '/localization', $locale, array('scan' => Zend_Translate::LOCALE_FILENAME, 'disableNotices' => true));
		Zend_Registry::set('Zend_Translate', $adapter);
		Zend_Form::setDefaultTranslator($adapter);
		self::$translator = $adapter;
	}
	
	protected function _initPagination()
	{
		Zend_Paginator::setDefaultScrollingStyle('Elastic');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
	}
}

/**
 * Global shortcut method that returns localized strings 
 * 
 * @param string $msgId the original string to translate
 * @return string the translated string
 */
function _tr($msgId)
{
	return Bootstrap::$translator->translate($msgId);
}
