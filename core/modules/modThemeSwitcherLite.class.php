<?php
/* Copyright (C) 2026 DoliResources <contact@doliresources.com>
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
 *  \file       custom/themeswitcherlite/core/modules/modThemeSwitcherLite.class.php
 *  \ingroup    themeswitcherlite
 *  \brief      Descriptor of module Theme Switcher Lite.
 *
 *  Theme Switcher Lite adds a small control in the top-right menu that lets each user
 *  pick an accent colour and toggle a light/dark theme. The choice is remembered per
 *  user in the browser (localStorage). It is a lightweight "interface" module: no SQL
 *  table, no CRUD. It only injects a small CSS + JS on every page.
 */

include_once DOL_DOCUMENT_ROOT."/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module ThemeSwitcherLite
 */
class modThemeSwitcherLite extends DolibarrModules
{
	/**
	 *  Constructor. Define names, constants, directories, boxes, permissions.
	 *
	 *  @param  DoliDB  $db  Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs;

		$this->db = $db;

		// Unique module id (DoliResources free range)
		$this->numero = 10000160;
		$this->rights_class = 'themeswitcherlite';

		$this->family = "interface";
		$this->module_position = '90';
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		$this->description = "A quick accent-colour picker and light/dark toggle in the top-right menu, remembered per user.";
		$this->descriptionlong = "Theme Switcher Lite adds a small control to the top-right menu so every user can instantly recolour Dolibarr with an accent of their choice and switch between light and dark mode. The preference is stored in the browser and applied on every page with no flash. Administrators choose the palette and defaults. No database table, no data entry, no full theme replacement: just a fast personal touch. Free and open-source by DoliResources.";

		$this->editor_name = 'DoliResources';
		$this->editor_url = 'https://www.doliresources.com';
		$this->version = '1.0';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto = 'technic';

		// Injected on every page: CSS + JS + hooks
		$this->module_parts = array(
			'css' => array('/themeswitcherlite/css/themeswitcherlite.css.php'),
			'js'  => array('/themeswitcherlite/js/themeswitcherlite.js.php'),
			'hooks' => array('data' => array('toprightmenu', 'main', 'all')),
		);

		$this->dirs = array();
		$this->config_page_url = array("setup.php@themeswitcherlite");

		$this->hidden = false;
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->phpmin = array(7, 0);
		$this->need_dolibarr_version = array(16, 0);
		$this->langfiles = array("themeswitcherlite@themeswitcherlite");

		if (!isset($conf->themeswitcherlite) || !isset($conf->themeswitcherlite->enabled)) {
			$conf->themeswitcherlite = new stdClass();
			$conf->themeswitcherlite->enabled = 0;
		}

		$this->dictionaries = array();
		$this->boxes = array();
		$this->cronjobs = array();
		$this->rights = array();
		$this->menu = array();
		$this->const = array();
	}

	/**
	 *  Function called when module is enabled.
	 *
	 *  @param   string  $options  Options when enabling module
	 *  @return  int                1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf;
		$sql = array();
		$res = $this->_init($sql, $options);

		// Cache-bust the injected CSS/JS after an update so users get the new assets
		// on a normal reload (the asset URLs carry &revision=MAIN_IHM_PARAMS_REV).
		if (getDolGlobalString('THEMESWITCHERLITE_ASSET_VER') !== $this->version) {
			dolibarr_set_const($this->db, 'MAIN_IHM_PARAMS_REV', getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
			dolibarr_set_const($this->db, 'THEMESWITCHERLITE_ASSET_VER', $this->version, 'chaine', 0, '', 0);
		}

		return $res;
	}

	/**
	 *  Function called when module is disabled.
	 *
	 *  @param   string  $options  Options when disabling module
	 *  @return  int                1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
