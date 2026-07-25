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
 *  \file       custom/themeswitcherlite/lib/themeswitcherlite.lib.php
 *  \ingroup    themeswitcherlite
 *  \brief      Shared helpers (palette, defaults, admin tabs) for Theme Switcher Lite.
 */

/**
 * Default curated accent palette (readable on both light and dark backgrounds).
 *
 * @return string[] List of hex colours
 */
function tslDefaultAccents()
{
	return array('#2563EB', '#4F46E5', '#7C3AED', '#DB2777', '#E11D48', '#EA580C', '#059669', '#0D9488', '#475569');
}

/**
 * Sanitize a hex colour (#RGB or #RRGGBB). Returns '' if invalid.
 *
 * @param  string $hex Candidate colour
 * @return string      Normalised hex (uppercase) or ''
 */
function tslSanitizeHex($hex)
{
	$hex = trim((string) $hex);
	if (preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hex, $m)) {
		return '#'.strtoupper($m[1]);
	}
	return '';
}

/**
 * The accent palette offered to users (from config, else the default palette).
 *
 * @return string[] List of hex colours
 */
function tslGetAccents()
{
	$raw = trim((string) getDolGlobalString('THEMESWITCHERLITE_ACCENTS'));
	if ($raw === '') {
		return tslDefaultAccents();
	}
	$out = array();
	foreach (preg_split('/[\s,;]+/', $raw) as $c) {
		$c = tslSanitizeHex($c);
		if ($c !== '' && !in_array($c, $out, true)) {
			$out[] = $c;
		}
	}
	return empty($out) ? tslDefaultAccents() : $out;
}

/**
 * The default accent applied when a user has not chosen one ('' = native colours).
 *
 * @return string Hex colour or ''
 */
function tslGetDefaultAccent()
{
	return tslSanitizeHex(getDolGlobalString('THEMESWITCHERLITE_DEFAULT_ACCENT'));
}

/**
 * Whether users may toggle dark mode.
 *
 * @return bool
 */
function tslAllowDark()
{
	return (bool) getDolGlobalInt('THEMESWITCHERLITE_ALLOW_DARK', 1);
}

/**
 * Whether the interface starts in dark mode by default (per user, until they change it).
 *
 * @return bool
 */
function tslDarkDefault()
{
	return (bool) getDolGlobalInt('THEMESWITCHERLITE_DARK_DEFAULT', 0);
}

/**
 * Prepare the tabs of the module configuration pages.
 *
 * @return array Array of tabs
 */
function themeswitcherliteAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("themeswitcherlite@themeswitcherlite");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/themeswitcherlite/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	$head[$h][0] = dol_buildpath("/themeswitcherlite/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	complete_head_from_modules($conf, $langs, null, $head, $h, 'themeswitcherlite@themeswitcherlite');

	return $head;
}
