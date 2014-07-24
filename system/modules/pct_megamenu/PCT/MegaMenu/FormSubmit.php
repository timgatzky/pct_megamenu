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
 * FormSubmit
 */
class FormSubmit extends \Contao\FormSubmit
{
	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$strBuffer = parent::generate();
		
		$preg = preg_match('/class="(.*?)icon(.*?)\"/', $strBuffer,$result);
		if($preg)
		{
			$strIcon = 'icon'.$result[2];
			$strBuffer = '<span class="input-wrap"><i class="'.$strIcon.'"></i>'.$strBuffer.'</span>';
		}
		
		return $strBuffer;
	}
}
