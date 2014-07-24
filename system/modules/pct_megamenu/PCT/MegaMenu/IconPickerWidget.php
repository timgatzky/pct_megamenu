<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_iconpicker
 * @link		http://contao.org
 */

/**
 * Namespace
 */
namespace PCT\IconPicker;

/**
 * Class file
 * IconPickerWidget
 * Generate the font picker widget
 */
class IconPickerWidget extends \Widget
{
	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget_iconpicker';

	/**
	 * CSS styles found
	 * @var array
	 */
	protected $arrStyles;
	
	/**
	 * CSS selector for icon definitions
	 * @var string
	 */
	protected $arrValidCssSelectors = array('.icon');
	
	/**
	 * Files
	 */
	protected $arrFiles;

	/**
	 * Load the database object
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		$this->import('Database');
		parent::__construct($arrAttributes);
	}
	
	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$this->import('BackendUser', 'User');
		$objSession = \Session::getInstance();

		// Store the keyword
		if (\Input::post('FORM_SUBMIT') == 'item_selector')
		{
			$objSession->set('icon_selector_search', \Input::post('keyword'));
			$this->reload();
		}
		
		// look in cache for icon styles
		
		// Template
		$objTemplate = new \BackendTemplate($this->strTemplate);
		$objTemplate->empty = $GLOBALS['TL_LANG']['MSC']['fontIconPicker_empty'];
			
		$this->arrFiles = array($GLOBALS['PCT_ICONPICKER']['pct_defaultCssIconFile']);
		
		// merge files with custom css files from settings
		if(strlen($GLOBALS['TL_CONFIG']['iconStylesheets']) > 0)
		{
			foreach(deserialize($GLOBALS['TL_CONFIG']['iconStylesheets']) as $v)
			{
				$file = \FilesModel::findByPk($v)->path;
				//ignore default icon file
				if($file == $GLOBALS['PCT_ICONPICKER']['pct_defaultCssIconFile'])
				{
					continue;
				}
				
				$this->arrFiles[] = $file;
			}
		}
		
		if(count($this->arrFiles) < 1)
		{
			return $objTemplate->parse();
		}
		
		// store files in session
		$objSession->set('icon_selector_files', $this->arrFiles);
		
		$objFactory = IconPickerFactory::getInstance();
		// get all styles from the files
		$this->arrStyles = $objFactory->findStylesInFiles($this->arrFiles);
		
		if(count($this->arrStyles) < 1)
		{
			return $objTemplate->parse();
		}
		
		// filter the list by search
		$tmp = array();
		if(strlen($objSession->get('icon_selector_search')) > 0)
		{
			$keyword = $objSession->get('icon_selector_search');
			foreach($this->arrStyles as $file => $styles)
			{
				foreach($styles as $i => $element)
				{
					if(strlen(strpos($element['selector'], $keyword)) > 0)
					{
						$tmp[$file][] = $element;
					}
				}
			}
			$this->arrStyles = $tmp;
		}
		
		// include be styling 
		$GLOBALS['TL_CSS'][] = 'system/modules/pct_iconpicker/assets/css/iconpicker.css';
		
		$objTemplate->files = $this->arrFiles;
		$objTemplate->styles = $this->arrStyles;
		$objTemplate->search = $this->Session->get('icon_selector_search');
		
		return $objTemplate->parse();
	}
	
}