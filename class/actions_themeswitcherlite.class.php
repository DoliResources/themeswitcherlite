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
 *  \file       custom/themeswitcherlite/class/actions_themeswitcherlite.class.php
 *  \ingroup    themeswitcherlite
 *  \brief      Hooks: early theme bootstrap (addHtmlHeader) + switcher control (printTopRightMenu).
 */

/**
 * Class ActionsThemeSwitcherLite
 */
class ActionsThemeSwitcherLite
{
	/** @var DoliDB */
	public $db;
	/** @var string  HTML pushed back to the hook manager */
	public $resprints;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Hook addHtmlHeader: apply the stored accent/theme on <html> before the page is
	 * painted, so a reload lands in the right state with no colour flash.
	 *
	 * @param  array        $parameters   Parameters
	 * @param  object       $object       Object
	 * @param  string       $action       Action
	 * @param  HookManager  $hookmanager  Hook manager
	 * @return int
	 */
	public function addHtmlHeader($parameters, &$object, &$action, $hookmanager)
	{
		if (!isModEnabled('themeswitcherlite')) {
			return 0;
		}
		require_once dol_buildpath('/themeswitcherlite/lib/themeswitcherlite.lib.php');

		$defaultAccent = tslGetDefaultAccent();          // '' or #hex
		$darkDefault = tslDarkDefault() ? 'dark' : '';
		$allowDark = tslAllowDark() ? 1 : 0;

		$js = '<script>(function(){try{'
			.'var d=document.documentElement;'
			.'var a=localStorage.getItem("tsl_accent");'
			.'if(a===null){a='.json_encode($defaultAccent).';}'
			.'if(a){d.setAttribute("data-tsl-accent","");d.style.setProperty("--tsl-accent",a);}'
			.'var allow='.((int) $allowDark).';'
			.'var t=localStorage.getItem("tsl_theme");'
			.'if(t===null){t='.json_encode($darkDefault).';}'
			.'if(!allow){t="";}'
			.'if(t==="dark"){d.setAttribute("data-tsl-theme","dark");}'
			.'}catch(e){}})();</script>';

		$this->resprints = $js;
		return 0;
	}

	/**
	 * Hook printTopRightMenu: render the accent picker + light/dark toggle.
	 *
	 * @param  array        $parameters   Parameters
	 * @param  object       $object       Object
	 * @param  string       $action       Action
	 * @param  HookManager  $hookmanager  Hook manager
	 * @return int
	 */
	public function printTopRightMenu($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;

		$this->resprints = '';
		if (!isModEnabled('themeswitcherlite')) {
			return 0;
		}
		require_once dol_buildpath('/themeswitcherlite/lib/themeswitcherlite.lib.php');
		$langs->loadLangs(array('themeswitcherlite@themeswitcherlite'));

		$accents = tslGetAccents();
		$allowDark = tslAllowDark();

		$html = '<div class="login_block_elementother tsl-switch">';

		// Compact accent picker: a single "palette" trigger that opens a dropdown of swatches.
		$html .= '<div class="tsl-accent-wrap">';
		$html .= '<button type="button" class="tsl-trigger" title="'.dol_escape_htmltag($langs->trans('TslAccentColor')).'" aria-label="'.dol_escape_htmltag($langs->trans('TslAccentColor')).'" aria-haspopup="true" aria-expanded="false">';
		$html .= '<span class="fas fa-palette"></span><span class="tsl-current"></span></button>';
		$html .= '<div class="tsl-dropdown" role="menu" aria-label="'.dol_escape_htmltag($langs->trans('TslAccentColor')).'">';
		// "None" swatch = back to native colours.
		$html .= '<button type="button" class="tsl-swatch tsl-swatch-none" data-color="" role="menuitemradio" title="'.dol_escape_htmltag($langs->trans('TslNoAccent')).'" aria-label="'.dol_escape_htmltag($langs->trans('TslNoAccent')).'"></button>';
		foreach ($accents as $hex) {
			$html .= '<button type="button" class="tsl-swatch" data-color="'.dol_escape_htmltag($hex).'" role="menuitemradio" style="background:'.dol_escape_htmltag($hex).';" title="'.dol_escape_htmltag($hex).'" aria-label="'.dol_escape_htmltag($langs->trans('TslAccentColor')).' '.dol_escape_htmltag($hex).'"></button>';
		}
		$html .= '</div>'; // dropdown
		$html .= '</div>'; // accent-wrap

		if ($allowDark) {
			$html .= '<button type="button" class="tsl-toggle" title="'.dol_escape_htmltag($langs->trans('TslThemeToggle')).'" aria-label="'.dol_escape_htmltag($langs->trans('TslThemeToggle')).'" aria-pressed="false"><span class="fas fa-moon"></span></button>';
		}

		$html .= '</div>';

		$this->resprints = $html;
		return 0;
	}
}
