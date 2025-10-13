<?php
/* Copyright (C) 2004-2018	Laurent Destailleur			<eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019	Nicolas ZABOURI				<info@inovea-conseil.com>
 * Copyright (C) 2019-2024	Frédéric France				<frederic.france@free.fr>
 * Copyright (C) 2025		Pierre ARDOIN
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   pvpropal     Module PvPropal
 *  \brief      PvPropal module descriptor.
 *
 *  \file       htdocs/pvpropal/core/modules/modPvPropal.class.php
 *  \ingroup    pvpropal
 *  \brief      Description and activation file for module PvPropal
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module PvPropal
 */
class modPvPropal extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 450004; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'pvpropal';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = 'crm';

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModulePvPropalName' not found (PvPropal is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// DESCRIPTION_FLAG
		// Module description, used if translation string 'ModulePvPropalDesc' not found (PvPropal is name of module).
		$this->description = "PvPropalDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "PvPropalDescription";

		// Author
		$this->editor_name = 'Les Métiers du Bâtiment';
		$this->editor_url = 'lesmetiersdubatiment.fr';		// Must be an external online web site
		$this->editor_squarred_logo = '';					// Must be image filename into the module/img directory followed with @modulename. Example: 'myimage.png@pvpropal'

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where PVPROPAL is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'propal';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 1,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				//    '/pvpropal/css/pvpropal.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/pvpropal/js/pvpropal.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			/* BEGIN MODULEBUILDER HOOKSCONTEXTS */
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			/* END MODULEBUILDER HOOKSCONTEXTS */
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
			// Set this to 1 if the module provides a website template into doctemplates/websites/website_template-mytemplate
			'websitetemplates' => 0,
			// Set this to 1 if the module provides a captcha driver
			'captcha' => 0
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/pvpropal/temp","/pvpropal/subdir");
		$this->dirs = array("/pvpropal/temp");

		// Config pages. Put here list of php page, stored into pvpropal/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@pvpropal");

		// Dependencies
		// A condition to hide module
		$this->hidden = getDolGlobalInt('MODULE_PVPROPAL_DISABLED'); // A condition to disable module;
		// List of module class names that must be enabled if this module is enabled. Example: array('always'=>array('modModuleToEnable1','modModuleToEnable2'), 'FR'=>array('modModuleToEnableFR')...)
		$this->depends = array();
		// List of module class names to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->requiredby = array();
		// List of module class names this module is in conflict with. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array();

		// The language file dedicated to your module
		$this->langfiles = array("pvpropal@pvpropal");

		// Prerequisites
		$this->phpmin = array(7, 1); // Minimum version of PHP required by module
		// $this->phpmax = array(8, 0); // Maximum version of PHP required by module
		$this->need_dolibarr_version = array(19, -3); // Minimum version of Dolibarr required by module
		// $this->max_dolibarr_version = array(19, -3); // Maximum version of Dolibarr required by module
		$this->need_javascript_ajax = 0;

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'PvPropalWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('PVPROPAL_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('PVPROPAL_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isModEnabled("pvpropal")) {
			$conf->pvpropal = new stdClass();
			$conf->pvpropal->enabled = 0;
		}

		// Array to add new pages in new tabs
		/* BEGIN MODULEBUILDER TABS */
		$this->tabs = array();
		/* END MODULEBUILDER TABS */
		// Example:
		// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data' => 'objecttype:+tabname1:Title1:mylangfile@pvpropal:$user->hasRight(\'pvpropal\', \'read\'):/pvpropal/mynewtab1.php?id=__ID__');
		// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data' => 'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@pvpropal:$user->hasRight(\'othermodule\', \'read\'):/pvpropal/mynewtab2.php?id=__ID__',
		// To remove an existing tab identified by code tabname
		// $this->tabs[] = array('data' => 'objecttype:-tabname:NU:conditiontoremove');
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'delivery'         to add a tab in delivery view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in foundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in sale order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view


		// Dictionaries
		/* Example:
		 $this->dictionaries=array(
		 'langs' => 'pvpropal@pvpropal',
		 // List of tables we want to see into dictionary editor
		 'tabname' => array("table1", "table2", "table3"),
		 // Label of tables
		 'tablib' => array("Table1", "Table2", "Table3"),
		 // Request to select fields
		 'tabsql' => array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.$this->db->prefix().'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.$this->db->prefix().'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.$this->db->prefix().'table3 as f'),
		 // Sort order
		 'tabsqlsort' => array("label ASC", "label ASC", "label ASC"),
		 // List of fields (result of select to show dictionary)
		 'tabfield' => array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields to edit a record)
		 'tabfieldvalue' => array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields for insert)
		 'tabfieldinsert' => array("code,label", "code,label", "code,label"),
		 // Name of columns with primary key (try to always name it 'rowid')
		 'tabrowid' => array("rowid", "rowid", "rowid"),
		 // Condition to show each dictionary
		 'tabcond' => array(isModEnabled('pvpropal'), isModEnabled('pvpropal'), isModEnabled('pvpropal')),
		 // Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
		 'tabhelp' => array(array('code' => $langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), array('code' => $langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), ...),
		 );
		 */
		/* BEGIN MODULEBUILDER DICTIONARIES */
		$this->dictionaries = array();
		/* END MODULEBUILDER DICTIONARIES */

		// Boxes/Widgets
		// Add here list of php file(s) stored in pvpropal/core/boxes that contains a class to show a widget.
		/* BEGIN MODULEBUILDER WIDGETS */
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'pvpropalwidget1.php@pvpropal',
			//      'note' => 'Widget provided by PvPropal',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);
		/* END MODULEBUILDER WIDGETS */

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		/* BEGIN MODULEBUILDER CRON */
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/pvpropal/class/myobject.class.php',
			//      'objectname' => 'MyObject',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => 'isModEnabled("pvpropal")',
			//      'priority' => 50,
			//  ),
		);
		/* END MODULEBUILDER CRON */
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'isModEnabled("pvpropal")', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'isModEnabled("pvpropal")', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		/*
		$o = 1;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($o * 10) + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read objects of PvPropal'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->hasRight('pvpropal', 'myobject', 'read'))
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($o * 10) + 2); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update objects of PvPropal'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->hasRight('pvpropal', 'myobject', 'write'))
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($o * 10) + 3); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete objects of PvPropal'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->hasRight('pvpropal', 'myobject', 'delete'))
		$r++;
		*/
		/* END MODULEBUILDER PERMISSIONS */


		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		/*
		$this->menu[$r++] = array(
			'fk_menu' => '', // Will be stored into mainmenu + leftmenu. Use '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type' => 'top', // This is a Top menu entry
			'titre' => 'ModulePvPropalName',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle"'),
			'mainmenu' => 'pvpropal',
			'leftmenu' => '',
			'url' => '/pvpropal/pvpropalindex.php',
			'langs' => 'pvpropal@pvpropal', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("pvpropal")', // Define condition to show or hide menu entry. Use 'isModEnabled("pvpropal")' if entry must be visible if module is enabled.
			'perms' => '1', // Use 'perms'=>'$user->hasRight("pvpropal", "myobject", "read")' if you want your menu with a permission rules
			'target' => '',
			'user' => 2, // 0=Menu for internal users, 1=external users, 2=both
		);
		*/
		/* END MODULEBUILDER TOPMENU */

		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT */
		/*
		$this->menu[$r++]=array(
			'fk_menu' => 'fk_mainmenu=pvpropal',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type' => 'left',                          // This is a Left menu entry
			'titre' => 'MyObject',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu' => 'pvpropal',
			'leftmenu' => 'myobject',
			'url' => '/pvpropal/pvpropalindex.php',
			'langs' => 'pvpropal@pvpropal',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("pvpropal")', // Define condition to show or hide menu entry. Use 'isModEnabled("pvpropal")' if entry must be visible if module is enabled.
			'perms' => '$user->hasRight("pvpropal", "myobject", "read")',
			'target' => '',
			'user' => 2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object' => 'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu' => 'fk_mainmenu=pvpropal,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type' => 'left',			                // This is a Left menu entry
			'titre' => 'New_MyObject',
			'mainmenu' => 'pvpropal',
			'leftmenu' => 'pvpropal_myobject_new',
			'url' => '/pvpropal/myobject_card.php?action=create',
			'langs' => 'pvpropal@pvpropal',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("pvpropal")', // Define condition to show or hide menu entry. Use 'isModEnabled("pvpropal")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms' => '$user->hasRight("pvpropal", "myobject", "write")'
			'target' => '',
			'user' => 2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object' => 'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu' => 'fk_mainmenu=pvpropal,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type' => 'left',			                // This is a Left menu entry
			'titre' => 'List_MyObject',
			'mainmenu' => 'pvpropal',
			'leftmenu' => 'pvpropal_myobject_list',
			'url' => '/pvpropal/myobject_list.php',
			'langs' => 'pvpropal@pvpropal',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("pvpropal")', // Define condition to show or hide menu entry. Use 'isModEnabled("pvpropal")' if entry must be visible if module is enabled.
			'perms' => '$user->hasRight("pvpropal", "myobject", "read")'
			'target' => '',
			'user' => 2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object' => 'MyObject'
		);
		*/
		/* END MODULEBUILDER LEFTMENU MYOBJECT */


		// Exports profiles provided by this module
		$r = 0;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		$langs->load("pvpropal@pvpropal");
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r] = $this->picto;
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'MyObject'; $keyforclassfile='/pvpropal/class/myobject.class.php'; $keyforelement='myobject@pvpropal';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'MyObjectLine'; $keyforclassfile='/pvpropal/class/myobject.class.php'; $keyforelement='myobjectline@pvpropal'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@pvpropal';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='myobjectline'; $keyforaliasextra='extraline'; $keyforelement='myobjectline@pvpropal';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('myobjectline' => array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field' => '...');
		//$this->export_examplevalues_array[$r] = array('t.field' => 'Example');
		//$this->export_help_array[$r] = array('t.field' => 'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.$this->db->prefix().'pvpropal_myobject as t';
		//$this->export_sql_end[$r]  .=' LEFT JOIN '.$this->db->prefix().'pvpropal_myobject_line as tl ON tl.fk_myobject = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 0;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/*
		$langs->load("pvpropal@pvpropal");
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->import_icon[$r] = $this->picto;
		$this->import_tables_array[$r] = array('t' => $this->db->prefix().'pvpropal_myobject', 'extra' => $this->db->prefix().'pvpropal_myobject_extrafields');
		$this->import_tables_creator_array[$r] = array('t' => 'fk_user_author'); // Fields to store import user id
		$import_sample = array();
		$keyforclass = 'MyObject'; $keyforclassfile='/pvpropal/class/myobject.class.php'; $keyforelement='myobject@pvpropal';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinimport.inc.php';
		$import_extrafield_sample = array();
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@pvpropal';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinimport.inc.php';
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.$this->db->prefix().'pvpropal_myobject');
		$this->import_regex_array[$r] = array();
		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('t.ref' => 'Ref');
		$this->import_convertvalue_array[$r] = array(
			't.ref' => array(
				'rule'=>'getrefifauto',
				'class'=>(!getDolGlobalString('PVPROPAL_MYOBJECT_ADDON') ? 'mod_myobject_standard' : getDolGlobalString('PVPROPAL_MYOBJECT_ADDON')),
				'path'=>"/core/modules/pvpropal/".(!getDolGlobalString('PVPROPAL_MYOBJECT_ADDON') ? 'mod_myobject_standard' : getDolGlobalString('PVPROPAL_MYOBJECT_ADDON')).'.php',
				'classobject'=>'MyObject',
				'pathobject'=>'/pvpropal/class/myobject.class.php',
			),
			't.fk_soc' => array('rule' => 'fetchidfromref', 'file' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
			't.fk_user_valid' => array('rule' => 'fetchidfromref', 'file' => '/user/class/user.class.php', 'class' => 'User', 'method' => 'fetch', 'element' => 'user'),
			't.fk_mode_reglement' => array('rule' => 'fetchidfromcodeorlabel', 'file' => '/compta/paiement/class/cpaiement.class.php', 'class' => 'Cpaiement', 'method' => 'fetch', 'element' => 'cpayment'),
		);
		$this->import_run_sql_after_array[$r] = array();
		$r++; */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int<-1,1>          	1 if OK, <=0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		// Create tables of module at module activation
		//$result = $this->_load_tables('/install/mysql/', 'pvpropal');
		$result = $this->_load_tables('/pvpropal/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		$this->createProductNatureValue();
		$this->createExtraFields();

		return $this->_init(array(), $options);
	}

	/**
	 *	Function called when module is disabled.
	 *	Remove from database constants, boxes and permissions from Dolibarr database.
	 *	Data directories are not deleted
	 *
	 *	@param	string		$options	Options when enabling module ('', 'noboxes')
	 *	@return	int<-1,1>				1 if OK, <=0 if KO
	 */
	public function remove($options = '')
	{
		$this->deleteExtraFields();
		$this->deleteProductNatureValue();

		return $this->_remove(array(), $options);
	}

	/**
	 * Create extra fields required by the module.
	 * Créer les champs supplémentaires requis par le module.
	 *
	 * @return void
	 */
	private function createExtraFields()
	{
		global $conf;

		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);

		$entity = 0;
		$langfile = 'pvpropal@pvpropal';
		$enabled = '$conf->pvpropal->enabled';

		$fields = array(
			'product' => array(
				'modulepvpc' => array(
					'label' => 'ExtraProductPvPeakPower',
					'help' => 'ExtraProductPvPeakPowerHelp',
					'type' => 'double',
					'pos' => 10,
					'size' => '24,8',
					'perms' => '((int) (!empty($object->fk_product_nature) && ((string) $object->fk_product_nature === "MOD" || (int) $object->fk_product_nature === getDolGlobalInt("PVPROPAL_NATURE_MOD_ID", 0))))',
					'list' => '1'
				)
			),
			'propal' => array(
				'ppvpc' => array('label' => 'ExtraProposalPvTotalPower', 'help' => 'ExtraProposalPvTotalPowerHelp'),
				'ppvpvwc' => array('label' => 'ExtraProposalPvSalePerWc', 'help' => 'ExtraProposalPvSalePerWcHelp'),
				'ppvpawc' => array('label' => 'ExtraProposalPvCostPerWc', 'help' => 'ExtraProposalPvCostPerWcHelp'),
				'ppvtxmarge' => array('label' => 'ExtraProposalPvMarginRate', 'help' => 'ExtraProposalPvMarginRateHelp'),
				'ppvmarge' => array('label' => 'ExtraProposalPvMargin', 'help' => 'ExtraProposalPvMarginHelp'),
				'ppvmargecible' => array('label' => 'ExtraProposalPvTargetMargin', 'help' => 'ExtraProposalPvTargetMarginHelp'),
				'ppvtxmargecible' => array('label' => 'ExtraProposalPvTargetMarginRate', 'help' => 'ExtraProposalPvTargetMarginRateHelp')
			)
		);

		foreach ($fields as $element => $definitions) {
			$position = 10;
			foreach ($definitions as $code => $definition) {
				$type = !empty($definition['type']) ? $definition['type'] : 'double';
				$size = !empty($definition['size']) ? $definition['size'] : '24,8';
				$perms = !empty($definition['perms']) ? $definition['perms'] : '';
				$list = !empty($definition['list']) ? $definition['list'] : '1';
				$help = !empty($definition['help']) ? $definition['help'] : '';

				$result = $extrafields->addExtraField($code, $definition['label'], $type, $position, $size, $element, 0, 0, '', '', 0, $perms, $list, $help, '', $entity, $langfile, $enabled, 0, 1);
				if ($result < 0 && $extrafields->error != 'ErrorFieldAlreadyExists') {
					continue;
				}

				if ($element === 'propal') {
					$this->configurePropalVisibility($code);
				}

				$position += 10;
			}
		}
	}

	/**
	 * Define list/export visibility for proposal extra fields.
	 * Définit la visibilité liste/export des champs supplémentaires des propositions.
	 *
	 * @param string $code Extra field code / Code du champ
	 * @return void
	 */
	private function configurePropalVisibility($code)
	{
		$sql = "UPDATE ".$this->db->prefix()."extrafields SET visible = 0, list = 1, printable = 1 WHERE name = '".$this->db->escape($code)."' AND elementtype = 'propal'";
		$this->db->query($sql);
	}

	/**
	 * Delete extra fields created by the module.
	 * Supprime les champs supplémentaires créés par le module.
	 *
	 * @return void
	 */
	private function deleteExtraFields()
	{
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);

		$map = array(
			'product' => array('modulepvpc'),
			'propal' => array('ppvpc', 'ppvpvwc', 'ppvpawc', 'ppvtxmarge', 'ppvmarge', 'ppvmargecible', 'ppvtxmargecible')
		);

		foreach ($map as $element => $codes) {
			foreach ($codes as $code) {
				$extrafields->delete($code, $element);
			}
		}
	}

	/**
	 * Create the product nature dictionary value.
	 * Crée la valeur de dictionnaire de nature de produit.
	 *
	 * @return void
	 */
	private function createProductNatureValue()
	{
		$columns = $this->describeTable($this->db->prefix().'c_product_nature');
		if (empty($columns)) {
			return;
		}

		include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

		// Reuse stored identifier when available / Réutilise l'identifiant stocké si disponible
		$storedId = (int) getDolGlobalInt('PVPROPAL_NATURE_MOD_ID', 0);
		if ($storedId > 0) {
			$sqlStored = "SELECT rowid, code FROM ".$this->db->prefix()."c_product_nature WHERE rowid = ".$storedId;
			$resStored = $this->db->query($sqlStored);
			if ($resStored) {
				$storedObj = $this->db->fetch_object($resStored);
				if ($storedObj) {
					// Refresh constants for the existing value / Rafraîchit les constantes pour la valeur existante
					dolibarr_set_const($this->db, 'PVPROPAL_NATURE_MOD_ID', (string) $storedObj->rowid, 'chaine', 0, '', 0);
					dolibarr_set_const($this->db, 'PVPROPAL_NATURE_MOD_CODE', (string) $storedObj->code, 'chaine', 0, '', 0);
					return;
				}
			}
		}

		// Look for an existing entry by label / Recherche une entrée existante par libellé
		$sqlExisting = "SELECT rowid, code FROM ".$this->db->prefix()."c_product_nature WHERE label = 'Module photovoltaïque' ORDER BY rowid ASC";
		$resExisting = $this->db->query($sqlExisting);
		if ($resExisting) {
			$existing = $this->db->fetch_object($resExisting);
			if ($existing) {
				// Reuse the found entry and store identifiers / Réutilise l'entrée trouvée et stocke les identifiants
				dolibarr_set_const($this->db, 'PVPROPAL_NATURE_MOD_ID', (string) $existing->rowid, 'chaine', 0, '', 0);
				dolibarr_set_const($this->db, 'PVPROPAL_NATURE_MOD_CODE', (string) $existing->code, 'chaine', 0, '', 0);
				return;
			}
		}

		$code = $this->generateProductNatureCode();

		$fields = array(
			'code' => "'".$this->db->escape($code)."'",
			'label' => "'Module photovoltaïque'"
		);

		if (isset($columns['active'])) {
			$fields['active'] = '1';
		}
		if (isset($columns['position'])) {
			$fields['position'] = '1000';
		}
		if (isset($columns['entity'])) {
			$fields['entity'] = '0';
		}

		$sql = "INSERT INTO ".$this->db->prefix()."c_product_nature (".implode(', ', array_keys($fields)).") SELECT ".implode(', ', $fields)." FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ".$this->db->prefix()."c_product_nature WHERE code = '".$this->db->escape($code)."')";
		$this->db->query($sql);

		$sqlid = "SELECT rowid, code FROM ".$this->db->prefix()."c_product_nature WHERE code = '".$this->db->escape($code)."' ORDER BY rowid ASC";
		$resql = $this->db->query($sqlid);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj) {
				// Store the dictionary identifier globally for every company / Stocke l'identifiant du dictionnaire pour chaque entité
				dolibarr_set_const($this->db, 'PVPROPAL_NATURE_MOD_ID', (string) $obj->rowid, 'chaine', 0, '', 0);
				dolibarr_set_const($this->db, 'PVPROPAL_NATURE_MOD_CODE', (string) $obj->code, 'chaine', 0, '', 0);
			}
		}
	}

	/**
	 * Remove the product nature dictionary value.
	 * Supprime la valeur de dictionnaire de nature de produit.
	 *
	 * @return void
	 */
	private function deleteProductNatureValue()
	{
		include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
		$natureId = (int) getDolGlobalInt('PVPROPAL_NATURE_MOD_ID', 0);
		if ($natureId > 0) {
			$sql = "DELETE FROM ".$this->db->prefix()."c_product_nature WHERE rowid = ".$natureId;
			$this->db->query($sql);
		} else {
			$sql = "DELETE FROM ".$this->db->prefix()."c_product_nature WHERE label = 'Module photovoltaïque'";
			$this->db->query($sql);
		}
		dolibarr_del_const($this->db, 'PVPROPAL_NATURE_MOD_ID', 0);
		dolibarr_del_const($this->db, 'PVPROPAL_NATURE_MOD_CODE', 0);
	}

	/**
	 * Generate the next product nature code.
	 * Génère le prochain code de nature de produit.
	 *
	 * @return string
	 */
	private function generateProductNatureCode()
	{
		$defaultCode = '01';
		$nextCode = $defaultCode;

		$sql = "SELECT code FROM ".$this->db->prefix()."c_product_nature ORDER BY CAST(code AS UNSIGNED) DESC LIMIT 1";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj && preg_match('/^\\d+$/', (string) $obj->code)) {
				$length = strlen((string) $obj->code);
				$length = $length > 0 ? $length : strlen($defaultCode);
				$numericCode = (int) $obj->code;
				$numericCode++;
				$nextCode = str_pad((string) $numericCode, $length, '0', STR_PAD_LEFT);
			}
		}

		// Ensure uniqueness by incrementing until a free code is found / Garantit l'unicité en incrémentant jusqu'à trouver un code libre
		while ($this->productNatureCodeExists($nextCode)) {
			if (preg_match('/^\\d+$/', $nextCode)) {
				$length = strlen($nextCode);
				$numericCode = (int) $nextCode;
				$numericCode++;
				$nextCode = str_pad((string) $numericCode, $length, '0', STR_PAD_LEFT);
			} else {
				$nextCode .= '0';
			}
		}

		return $nextCode;
	}

	/**
	 * Check if a product nature code already exists.
	 * Vérifie si un code de nature de produit existe déjà.
	 *
	 * @param string $code Product nature code / Code de nature de produit
	 * @return bool
	 */
	private function productNatureCodeExists($code)
	{
		$sql = "SELECT rowid FROM ".$this->db->prefix()."c_product_nature WHERE code = '".$this->db->escape($code)."'";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			if ($obj) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get table description.
	 * Retourne la description d'une table.
	 *
	 * @param string $table Table name / Nom de la table
	 * @return array<string, string>
	 */
	private function describeTable($table)
	{
		$columns = array();
		$sql = 'SHOW COLUMNS FROM '.$table;
		$resql = $this->db->query($sql);
		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$columns[$obj->Field] = $obj->Type;
			}
		}

		return $columns;
	}
}
