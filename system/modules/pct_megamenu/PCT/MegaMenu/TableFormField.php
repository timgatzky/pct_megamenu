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
 * TableFormField
 */
class TableFormField extends \Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
		
		// add the stylesheets to the page
		$objPicker = new IconPicker();
		$objPicker->addStyleSheetsToPage();
	}

	/**
	 * Overwrite the backend output of a form field row and inject font icon class
	 * @param array
	 * @return string
	 */
	public function listFormFields($arrRow)
	{
		$objHelper = new \tl_form_field();
		
		$arrClass = explode(' ', $arrRow['class']);
		
		if(!in_array($arrRow['fontIcon'], $arrClass))
		{
			$arrClass[] = $arrRow['fontIcon'];
		}
		
		$arrRow['class'] = trim(implode(' ', $arrClass));
		if(in_array('pct_autogrid', \Config::getInstance()->getActiveModules()))
		{
			if(class_exists('\PCT\AutoGrid\TableFormField',false))
			{
				$objHelper = new \PCT\AutoGrid\TableFormField();
				return $objHelper->listRecord($arrRow);
			}
		}
		
		return $objHelper->listFormFields($arrRow);
	}
}