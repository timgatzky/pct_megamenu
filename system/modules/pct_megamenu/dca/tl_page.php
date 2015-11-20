<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2014
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_megamenu
 * @link		http://contao.org
 */

// load data containers
$this->loadDataContainer('tl_content');

/**
 * Selector
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'pct_megamenu';
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'pct_mm_noreplace';

/**
 * Palettes
 */
if(TL_MODE == 'BE')
{
	if(!is_array($GLOBALS['PCT_MEGAMENU']['pageIgnoreList']))
	{
		$GLOBALS['PCT_MEGAMENU']['pageIgnoreList'] = array();
	}
	
	foreach($GLOBALS['TL_DCA']['tl_page']['palettes'] as $type => $palette)
	{
		if(!in_array($type, $GLOBALS['PCT_MEGAMENU']['pageIgnoreList']) && $type != '__selector__')
		{
			$GLOBALS['TL_DCA']['tl_page']['palettes'][$type] = str_replace('type','type;{pct_megamenu_legend},pct_megamenu;',$GLOBALS['TL_DCA']['tl_page']['palettes'][$type]);
		}
	}
}


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['pct_megamenu'] = 'pct_mm_article,pct_mm_noreplace';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['pct_mm_noreplace'] = 'pct_mm_floating';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['pct_megamenu'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pct_megamenu'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pct_mm_article'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pct_mm_article'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('PCT\MegaMenu\TablePage', 'getArticles'),
	'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'submitOnChange'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pct_mm_noreplace'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pct_mm_noreplace'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''",
);


$GLOBALS['TL_DCA']['tl_page']['fields']['pct_mm_floating'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pct_mm_floating'],
	'default'                 => 'above',
	'exclude'                 => true,
	'inputType'               => 'radioTable',
	'options'                 => array('above', 'below'),
	'eval'                    => array('cols'=>2, 'tl_class'=>'w50'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'sql'                     => "varchar(32) NOT NULL default ''"
);