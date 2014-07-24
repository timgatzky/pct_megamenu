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
 * IconPickerFactory
 * Provide various function to handle stylesheets 
 */
class IconPickerFactory
{
	/**
	 * Current object instance (Singleton)
	 * @var object
	 */
	protected static $objInstance;
	
	/**
	 * Instantiate this class and return it (Factory)
	 * @return PCT_IconPickerFactory
	 * @throws Exception
	 */
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new self();
		}

		return self::$objInstance;
	}
	
	/**
	 * Cache name
	 * @var string
	 */
	protected $strCache = 'iconpicker';
	
	/**
	 * CSS class selector
	 * @var string
	 */
	public $strSelector = 'icon';
	
	
	/**
	 * Find all styles in a list of stylesheet files
	 * @param array
	 * @return array/boolean
	 */
	public function findStylesInFiles($arrFiles)
	{
		if(empty($arrFiles))
		{
			return false;
		}
		
		// read from cache
		if(\Cache::has($this->strCache))
		{
			return \Cache::get($this->strCache);
		}
		
		$arrReturn = array();
		foreach($arrFiles as $file)
		{
			$arrReturn[$file] = $this->findStylesInFile($file);
		}
		
		// set Cache
		\Cache::set($this->strCache,$arrReturn);
		
		return $arrReturn;
	}
	
	/**
	 * Find all styles in a stylesheet and return as array
	 * @param string
	 * @return array
	 */
	public function findStylesInFile($strFile)
	{
		// read from cache
		if(\Cache::has($this->strCache))
		{
			return \Cache::get($this->strCache);
		}
		
		$arrReturn = $this->getStyles($strFile);
		
		// set Cache
		\Cache::set($this->strCache,$arrReturn);
		
		return $arrReturn;
	}
	
	/**
	 * Generate the styles array
	 * @param string
	 * @return array
	 */
	protected function getStyles($strFile)
	{
		$results = $this->getStylesFromStylesheet($strFile);
		if(count($results) < 1 || empty($results))
		{
			return array();
		}
		
		$arrReturn = array();
		
		foreach($results[0] as $i => $style)
		{
			$raw = array($results[0][$i],$results[1][$i],$results[2][$i]);
			$arrSelector = explode(':', $results[1][$i]);
			$styles = $arrStyles[2][$i];
			$selector = $this->strSelector.$arrSelector[0];
			$selector_full = $this->strSelector.$results[1][$i];
			$class = ltrim($this->strSelector.$arrSelector[0],'.');
			
			$arrReturn[$i] = array
			(
				'class'		=> $class,
				'selector'	=> $selector,
				'styles'	=> $raw[2],
				'raw'		=> $raw
			);
		}
		
		return $arrReturn;
	}
	
	/**
	 * Search styles in the content of a stylesheet file and return matches as array
	 * @param string
	 * @return array
	 */
	protected function getStylesFromStylesheet($strFile)
	{
		if(!file_exists(TL_ROOT.'/'.$strFile) || !is_readable(TL_ROOT.'/'.$strFile) )
		{
			return array();
		}
		
		$objFile = new \File($strFile,false);
		
		$content = $objFile->getContent();
		
		# remove everything between /* and */
		$content = preg_replace("!/\*.*?\*/!ms", "", $content);
		
		# remove whitespaces after semicolons
		$content = preg_replace("/;\s+/m", "; ", $content);
		
		# remove whitespaces after {
		$content = preg_replace("/{\s+/m", "{ ", $content);
		
		# remove whitespace before {
		$content = preg_replace("/\s+{/m", " {", $content);
		
		# replace several newlines with one
		$content = preg_replace("/\n{2,}/m", "\n", $content);
		
		# Leading whitespace
		$content = preg_replace("/^\s*/m", "", $content);
		
		# Multiple whitespaces to one
		$content = preg_replace("/ +/m", " ", $content);
		
		// remove all defining styles like: [class^="icon-"]
		$content = str_replace(array('[class^="icon-"]','[class*="icon-"]'), '', $content);
		
		// find styles
		$preg = preg_match_all('/'.$this->strSelector.'(.+?)\s?\{\s?(.+?)\s?\}/', $content, $match);
		
		if(!$preg)
		{
			continue;
		}
		
		return $match;
	}

}