<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2015
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_megamenu
 * @link		http://contao.org
 */

/**
 * Namespace
 */
namespace PCT\MegaMenu;

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
	}


	/**
	 * Return all articles in all root pages
	 * @param object
	 * @return array
	 */
	public function getArticles($objDC)
	{
		$objDatabase = \Database::getInstance();
		$objContents = $objDatabase->prepare("SELECT id FROM tl_content WHERE invisible!=1")->execute();
		if($objContents->numRows < 1)
		{
			return array();
		}
		
		if(!is_array($GLOBALS['TL_DCA']['tl_content']) || !isset($GLOBALS['loadDataContainer']['tl_content']))
		{
			\Controller::loadDataContainer('tl_content');
		}
		
		$dc = clone($objDC);
		$tl_content = new \tl_content;
		
		$arrProcessed = array();
		$arrReturn = array();
		while($objContents->next())
		{
			if(in_array($objContents->pid, $arrProcessed))
			{
				continue;
			}
			
			$dc->activeRecord->pid = $objContents->pid;
			$articles = $tl_content->getArticles($dc);
			if(count($articles) > 0 && is_array($articles))
			{
				$arrReturn += $articles;
			}
			
			$arrProcessed[] = $objContents->pid;
		}
		
		return $arrReturn;
	}
	
}