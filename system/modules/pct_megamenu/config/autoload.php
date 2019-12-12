<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package pct_megamenu
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Register the namespaces
 */
\Contao\ClassLoader::addNamespaces(array
(
	'PCT\MegaMenu',
));


/**
 * Register the classes
 */
\Contao\ClassLoader::addClasses(array
(
	'PCT\MegaMenu\TablePage' 				=> 'system/modules/pct_megamenu/PCT/MegaMenu/TablePage.php',
));

/**
 * Register the templates
 */
\Contao\TemplateLoader::addFiles(array
(
	'nav_pct_megamenu' 					=> 'system/modules/pct_megamenu/templates',
));
