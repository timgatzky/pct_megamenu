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
 * TablePage
 */
class TablePage extends \Backend
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
	 * Save font icon class in real page class field
	 */
	public function setCssClassOnSave($varValue, \DataContainer $objDC)
	{
		$varValue = $this->getFormattedClassFieldValue($varValue, $objDC);
		
		return $varValue;
	}
	
	/**
	 * Save font icon class in real page class field
	 */
	public function setCssClassOnLoad($varValue, \DataContainer $objDC)
	{
		$varValue = $this->getFormattedClassFieldValue($varValue, $objDC);
		
		// manually update the database here
		\Database::getInstance()->prepare("UPDATE ".$objDC->table." %s WHERE id=?")->set(array('cssClass'=>$varValue))->execute($objDC->id);
		
		return $varValue;
	}

	/**
	 * Prepare the class field array and return it
	 * @param string
	 * @param object
	 * @return array
	 */
	protected function getFormattedClassFieldValue($varValue, \DataContainer $objDC)
	{
		$objActiveRecord = \Database::getInstance()->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($objDC->id);

		// allow to enter a icon class manually
		if(!$objActiveRecord->addFontIcon)
		{
			return $varValue;
		}

		$arrClass = explode(' ', $varValue);
		
		if(!in_array($objActiveRecord->addFontIcon,$arrClass))
		{
			$arrClass[] = $objActiveRecord->fontIcon;
		}
		
		// remove the icon class from the class field
		foreach($arrClass as $i => $cls)
		{
			// skip non icon classes
			if(strlen(strpos($cls, 'icon')) < 1)
			{
				continue;
			}
			
			// remove any icon that is not selected
			if(strlen($objActiveRecord->fontIcon) > 0)
			{
				if($cls != $objActiveRecord->fontIcon)
				{
					unset($arrClass[$i]);
				}
			}
			
			// remove class if option is disabled
			if(!$objActiveRecord->addFontIcon)
			{
				if($cls == $objActiveRecord->fontIcon)
				{
					unset($arrClass[$i]);
				}
			}
			
			// remove class when icon selection is empty
			if(strlen($objActiveRecord->fontIcon) < 1)
			{
				if(strlen(strpos($cls, 'icon')))
				{
					unset($arrClass[$i]);
				}
			}
		}
		
		$arrClass = array_unique($arrClass);
		$varValue = trim(implode(' ', $arrClass));
		
		return $varValue;
	}
	
}