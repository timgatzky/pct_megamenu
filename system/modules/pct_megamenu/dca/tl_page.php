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

/**
 * Palettes
 */
foreach($GLOBALS['TL_DCA']['tl_page']['palettes'] as $type => $palette)
{
	if(!in_array($type, $GLOBALS['PCT_MEGAMENU']['pageIgnoreList']) && $type != '__selector__')
	{
		$GLOBALS['TL_DCA']['tl_page']['palettes'][$type] = str_replace('type','type;{pct_megamenu_legend},pct_megamenu;',$GLOBALS['TL_DCA']['tl_page']['palettes'][$type]);
	}
}

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['pct_megamenu'] = 'pct_mm_article';

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
	'options_callback'        => array('tl_content', 'getArticles'),
	'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true),
	'wizard' => array
	(
		array('tl_content', 'editArticle')
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);