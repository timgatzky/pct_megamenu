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
	'options_callback'        => array('tl_page_pct_megamenu', 'getArticles'),
	'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'submitOnChange'=>true),
	'wizard' => array
	(
		array('tl_content', 'editArticle')
	),
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


/**
 * Class tl_page_pct_megamenu
 */
class tl_page_pct_megamenu extends \Backend
{
	/**
	 * Get all articles and return them as array (article teaser)
	 * @param \DataContainer
	 * @return array
	 */
	public function getArticles(\DataContainer $objDC)
	{
		$this->import('BackendUser','User');
		$arrPids = array();
		$arrArticle = array();
		$arrRoot = array();
		$intPid = $objDC->activeRecord->pid;

		if (\Input::get('act') == 'overrideAll')
		{
			$intPid= \Input::get('id');
		}

		$objDatabase = \Database::getInstance();
		$objPage = \PageModel::findWithDetails($objDC->id);
		$arrRoot = $objDatabase->getChildRecords($objPage->rootId, 'tl_page');
		array_unshift($arrRoot, $objPage->rootId);
		
		// Limit pages to the user's pagemounts
		if ($this->User->isAdmin)
		{
			$objArticle = $objDatabase->execute("SELECT a.id, a.pid, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid" . (!empty($arrRoot) ? " WHERE a.pid IN(". implode(',', array_map('intval', array_unique($arrRoot))) .")" : "") . " ORDER BY parent, a.sorting");
		}
		else
		{
			foreach ($this->User->pagemounts as $id)
			{
				if (!in_array($id, $arrRoot))
				{
					continue;
				}

				$arrPids[] = $id;
				$arrPids = array_merge($arrPids, $objDatabase->getChildRecords($id, 'tl_page'));
			}

			if (empty($arrPids))
			{
				return $arrArticle;
			}

			$objArticle = $objDatabase->execute("SELECT a.id, a.pid, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(". implode(',', array_map('intval', array_unique($arrPids))) .") ORDER BY parent, a.sorting");
		}

		// Edit the result
		if ($objArticle->numRows)
		{
			\System::loadLanguageFile('tl_article');

			while ($objArticle->next())
			{
				$key = $objArticle->parent . ' (ID ' . $objArticle->pid . ')';
				$arrArticle[$key][$objArticle->id] = $objArticle->title . ' (' . ($GLOBALS['TL_LANG']['tl_article'][$objArticle->inColumn] ?: $objArticle->inColumn) . ', ID ' . $objArticle->id . ')';
			}
		}

		return $arrArticle;
	}

}