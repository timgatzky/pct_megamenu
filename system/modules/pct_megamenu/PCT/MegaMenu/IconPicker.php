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
 * IconPicker
 * Provide various function to extend contao with font icon classes and the font picker widget link
 */
class IconPicker extends \Backend
{
	/**
	 * Init
	 */
	public function __construct() {}
	
	/**
	 * Inject font icon class in content elements
	 * @param object
	 * @param string
	 * @return string
	 * called from getContentElement HOOK
	 */
	public function getContentElementCallback(\ContentModel $objElement, $strBuffer, $objModel)
	{
		if(!$objElement->addFontIcon || strlen($objElement->fontIcon) < 1)
		{
			return $strBuffer;
		}
		
		// add stylesheets
		$this->addStyleSheetsToPage();
			
		switch($objElement->type)
		{
			case 'list':
				$items = deserialize($objElement->listitems);
				if(empty($items))
				{
					break;
				}
				
				// find all li elements
				$preg = preg_match_all('/<li(.*?)>/', $strBuffer,$match);
				if($preg)
				{
					// inject class in li
					foreach($match[0] as $res)
					{
						$replace = '';
						$arrClass = array($objElement->fontIcon);
						$preg = preg_match('/class="(.*?)\"/', $res,$result);
						if($preg)
						{
							$class = explode(' ', $result[1]);
							$arrClass = array_unique(array_merge($arrClass, $class));
							$replace = preg_replace('/class="(.*?)\"/', 'class="'.trim(implode(' ', $arrClass)).'"',$res);
						}
						else
						{
							$replace = str_replace('<li', '<li class="'.trim(implode(' ', $arrClass)).'"', $res);
						}
						
						$strBuffer = str_replace($res, $replace, $strBuffer);
					}
				}
				break;
			// inject in anchor only
			case 'hyperlink':
			case 'toplink':
				$arrClass = array($objElement->fontIcon);
				$preg = preg_match_all('/<a(.*?) class="(.*?)\"/',$strBuffer,$result);
				if($preg)
				{
					$arrClass = array_unique(array_merge($arrClass, $result[2]));
					$strBuffer = str_replace($result[2], trim(implode(' ', $arrClass)), $strBuffer);
				}
				else
				{
					$strBuffer = str_replace('<a', '<a class="'.trim(implode(' ', $arrClass)).'"', $strBuffer);
				}
				break;
			// inject in headlines only
			case 'text':
			case 'table':
			case 'code':
			case 'image':
			case 'gallery':
			case 'video':
			case 'sliderStart':
			case 'youtube':
			case 'download': case 'downloads':
			case 'comments':
			case 'form':
				$arrClass = array($objElement->fontIcon);
				$preg = preg_match('/<h(.*?)\>/', $strBuffer,$result);
				if($preg)
				{
					$preg = preg_match('/class="(.*?)\"/', $strBuffer,$result[0]);
					if($preg)
					{
						$class = explode(' ', $result[1]);
						$arrClass = array_unique(array_merge($arrClass, $class));
					}
					
					$strBuffer = preg_replace('/<h(.*?)\>/', '<h'.$result[1].' class="'.trim(implode(' ', $arrClass)).'">',$strBuffer);
				}
				break;
			default:
				$arrCssID = deserialize($objElement->cssID);
				$arrClass = array($objElement->fontIcon);
				$arrClass[] = trim($arrCssID[1]);
				$preg = preg_match('/class="(.*?)\"/', $strBuffer,$result);
				if($preg)
				{
					$class = explode(' ', $result[1]);
					$arrClass = array_unique(array_merge($arrClass, $class));
				}
				$strBuffer = str_replace($result[1], trim(implode(' ', $arrClass)), $strBuffer);
				break;
		}
	
		return $strBuffer;
	}
	
	
	/**
	 * Inject font icon class in form fields
	 * @param object
	 * @param string
	 * @param array
	 * @return object
	 * called from loadFormField HOOK
	 */
	public function loadFormFieldCallback(\Widget $objWidget, $strForm, $arrForm)
	{
		if(!$objWidget->addFontIcon || strlen($objWidget->fontIcon) < 1)
		{
			return $objWidget;
		}
		
		// add stylesheets
		$this->addStyleSheetsToPage();
		
		$arrClass = explode(' ', $objWidget->class);
		if(!in_array($objWidget->fontIcon, $arrClass))
		{
			$arrClass[] = $objWidget->fontIcon;
			$objWidget->class = $objWidget->fontIcon;
		}
		#$objWidget->class = trim(implode(' ', $arrClass));
		
		return $objWidget;
	}

	
	/**
	 * Render attribute from a custom element
	 * @param string
	 * @param string
	 * @param mixed
	 * @param array
	 * @return string
	 * called from CustomElements renderAttribute HOOK
	 */
	public function renderAttributeCallback($strBuffer,$strField,$varValue,$arrFieldDef,$objAttribute)
	{
		if(!$objAttribute->get('addFontIcon'))
		{
			return $strBuffer;
		}
		
		// add stylesheets
		$this->addStyleSheetsToPage();
		
		$arrClass = array($objAttribute->get('fontIcon'));
		
		$preg = preg_match('/class="(.*?)\"/', $strBuffer,$result);
		if($preg)
		{
			$class = explode(' ', $result[1]);
			$arrClass = array_unique(array_merge($arrClass, $class));
		}
		#$strBuffer = preg_replace('/class="(.*?)\"/', 'class="'.trim(implode(' ', $arrClass)).'"',$strBuffer);
		$strBuffer = str_replace($result[1], trim(implode(' ', $arrClass)), $strBuffer);
				
		return $strBuffer;
	}

	/**
	 * Add icon style sheets to the page <head>
	 */
	public function addStyleSheetsToPage()
	{
		$objSession = \Session::getInstance();
		
		// load backend styles
		if(TL_MODE == 'BE')
		{
			$GLOBALS['TL_CSS'][] = 'system/modules/pct_iconpicker/assets/css/iconpicker.css';
		}
		
		// load stylesheet files
		if(count($objSession->get('icon_selector_files')) > 0)
		{
			foreach($objSession->get('icon_selector_files') as $file)
			{
				if(!is_array($GLOBALS['TL_CSS']))
				{
					$GLOBALS['TL_CSS'] = array($file);
					continue;
				}
				
				if(in_array($file, $GLOBALS['TL_CSS']))
				{
					continue;	
				}
				
				$GLOBALS['TL_CSS'][] = $file;
			}
		}
		else
		{
			$arrFiles = array($GLOBALS['PCT_ICONPICKER']['pct_defaultCssIconFile']);
			// add custom icon stylesheets
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
					
					$arrFiles[] = $file;	
				}
			}
			$objSession->set('icon_selector_files',$arrFiles);
			
			// call recursive with new Session data
			$this->addStyleSheetsToPage();
		}
	}

	
	/**
	 * Return the font icon picker
	 * @param object
	 * @return string
	 */
	public function fontIconPicker(\DataContainer $objDC)
	{
		$href = 'system/modules/pct_iconpicker/assets/html/iconpicker.php?do=' . \Input::get('do'). '&amp;table=' . $objDC->table. '&amp;field=' . $objDC->field . '&amp;value=' . $objDC->value;
		$link = \Image::getHtml('system/modules/pct_iconpicker/assets/img/icon.gif', $GLOBALS['TL_LANG']['MSC']['fontIconPicker'], 'style="vertical-align:top;cursor:pointer"');
		
		$options = array
		(
			'width'	=> 765,
			'title'	=> $GLOBALS['TL_LANG']['MSC']['fontIconPicker'],
			'value'	=> $objDC->value,
			'ctrl_'	=> $objDC->field,
			'tag'	=> 'ctrl_'. $objDC->field . ((\Input::get('act') == 'editAll') ? '_' . $objDC->id : ''),
			'id'	=> $objDC->field,
		);
		
		$options = json_encode($options);
		$options = str_replace(array('{','}'), '', $options);
		$options = str_replace('"', "'", $options);
		$options .= ',\'url\':this.href,\'self\':this';
		
		$attributes = 'onclick="Backend.getScrollOffset();Backend.openModalSelector({'.$options.'});return false;"';
		return sprintf('<a href="%s" title="%s" %s>'.$link.'</a>',$href,$title,$attributes);
	}
	
	/**
	 * Attach the current icon to the field value
	 */
	public function attachIcon($varValue, \DataContainer $objDC)
	{
		if(!$varValue)
		{
			return $varValue;
		}
		
		$this->addStyleSheetsToPage();
		
		$GLOBALS['TL_DCA'][$objDC->table]['fields']['fontIcon']['eval']['tl_class'] = 'clr w50 '.$varValue;
		
		return $varValue;
		
	}
}