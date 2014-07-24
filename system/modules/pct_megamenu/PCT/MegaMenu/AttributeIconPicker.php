<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013, Premium Contao Webworks, Premium Contao Themes
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customelements
 * @link		http://contao.org
 */

/**
 * Namespace
 */
namespace PCT\IconPicker;

/**
 * Imports
 */
use PCT\CustomElements\Core\Attribute as Attribute;
use PCT\CustomElements\Helper\ControllerHelper as ControllerHelper;

/**
 * Class file
 * AttributeIconPicker
 */
class AttributeIconPicker extends Attribute
{
	/**
	 * Data Array
	 * @var array
	 */
	protected $arrData = array();
	
	/**
	 * Create new instance
	 * @param array
	 */ 
	public function __construct($arrData=array())
	{
		if(count($arrData) > 0)
		{
			foreach($arrData as $strKey => $varValue)
			{
				$this->arrData[$strKey] = deserialize($varValue);
			}
		}
	}	
	
	/**
	 * Return the field definition
	 * @return array
	 */
	public function getFieldDefinition()
	{
		$arrEval = $this->getEval();
		
		$arrReturn = array
		(
			'label'			=> array( $this->get('title'),$this->get('description') ),
			'exclude'		=> true,
			'inputType'		=> 'text',
			'eval'			=> $arrEval,
		);
		
		$options = $this->get('options');
		if(empty($options) || !is_array($options))
		{
			return $arrReturn;
		}
		
		if(in_array('iconpicker',$options))
		{
			$arrReturn['wizard'] = array
			(
				'wizard' => array
				(
					'PCT\IconPicker\IconPicker', 'fontIconPicker'
				),
			);
			
			$arrReturn['eval']['tl_class'] .= ' wizard';
		}
		
		return $arrReturn;
	}
	
}