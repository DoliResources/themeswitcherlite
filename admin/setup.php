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
 *  \file       custom/themeswitcherlite/admin/setup.php
 *  \ingroup    themeswitcherlite
 *  \brief      Theme Switcher Lite setup page.
 */

$res = 0;
if (!$res && file_exists("../../../main.inc.php"))    $res = @include "../../../main.inc.php";
if (!$res && file_exists("../../../../main.inc.php")) $res = @include "../../../../main.inc.php";
if (!$res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once dol_buildpath('/themeswitcherlite/lib/themeswitcherlite.lib.php');

global $conf, $db, $langs, $user;
$langs->loadLangs(array('admin', 'themeswitcherlite@themeswitcherlite'));

if (!isModEnabled('themeswitcherlite')) accessforbidden();
// Admins can view + edit. On a demo platform anyone may VIEW (to showcase the module),
// but ANY submit/write is strictly admin-only (checked again inside each handler below).
if (!$user->admin && !getDolGlobalString('DOLIRESOURCES_DEMO_PLATFORM')) accessforbidden();
if (!$user->admin && GETPOSTISSET('action')) accessforbidden();

$action = GETPOST('action', 'aZ09');

if ($action == 'save') {
	if (!$user->admin) accessforbidden();
	// Palette: keep only valid hex colours.
	$accents = array();
	foreach (preg_split('/[\s,;]+/', (string) GETPOST('THEMESWITCHERLITE_ACCENTS', 'alphanohtml')) as $c) {
		$c = tslSanitizeHex($c);
		if ($c !== '' && !in_array($c, $accents, true)) {
			$accents[] = $c;
		}
	}
	dolibarr_set_const($db, 'THEMESWITCHERLITE_ACCENTS', implode(',', $accents), 'chaine', 0, '', $conf->entity);
	dolibarr_set_const($db, 'THEMESWITCHERLITE_DEFAULT_ACCENT', tslSanitizeHex(GETPOST('THEMESWITCHERLITE_DEFAULT_ACCENT', 'alphanohtml')), 'chaine', 0, '', $conf->entity);
	dolibarr_set_const($db, 'THEMESWITCHERLITE_ALLOW_DARK', GETPOST('THEMESWITCHERLITE_ALLOW_DARK', 'int') ? '1' : '0', 'chaine', 0, '', $conf->entity);
	dolibarr_set_const($db, 'THEMESWITCHERLITE_DARK_DEFAULT', GETPOST('THEMESWITCHERLITE_DARK_DEFAULT', 'int') ? '1' : '0', 'chaine', 0, '', $conf->entity);

	// Bump asset revision so users pick up the new default without a hard refresh.
	dolibarr_set_const($db, 'MAIN_IHM_PARAMS_REV', getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);

	setEventMessages($langs->trans('SetupSaved'), null, 'mesgs');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

if ($action == 'reset') {
	if (!$user->admin) accessforbidden();
	foreach (array('THEMESWITCHERLITE_ACCENTS', 'THEMESWITCHERLITE_DEFAULT_ACCENT', 'THEMESWITCHERLITE_ALLOW_DARK', 'THEMESWITCHERLITE_DARK_DEFAULT') as $k) {
		dolibarr_del_const($db, $k, $conf->entity);
	}
	dolibarr_set_const($db, 'MAIN_IHM_PARAMS_REV', getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
	setEventMessages($langs->trans('SetupSaved'), null, 'mesgs');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

$title = $langs->trans('ThemeSwitcherLiteSetup');
llxHeader('', $title);

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($title, $linkback, 'title_setup');

$head = themeswitcherliteAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', '', -1, '');

$accents = tslGetAccents();
$defaultAccent = tslGetDefaultAccent();
$rawAccents = trim((string) getDolGlobalString('THEMESWITCHERLITE_ACCENTS'));
if ($rawAccents === '') {
	$rawAccents = implode(', ', tslDefaultAccents());
}
?>
<style>
.tsl-cfg-preview{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin:6px 0 2px; }
.tsl-cfg-dot{ width:22px; height:22px; border-radius:50%; box-shadow:0 1px 2px rgba(0,0,0,.2); border:2px solid #fff; outline:1px solid rgba(0,0,0,.08); }
.tsl-cfg-hint{ font-size:11.5px; color:var(--colortextbackhmenu,#8a93a3); }
</style>
<?php
print '<div class="opacitymedium" style="margin:2px 0 16px;">'.dol_escape_htmltag($langs->trans('TslConfigIntro')).'</div>';

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans('Parameter').'</td><td>'.$langs->trans('Value').'</td></tr>';

// Palette
print '<tr class="oddeven"><td><b>'.$langs->trans('CfgAccents').'</b><br><span class="opacitymedium small">'.$langs->trans('CfgAccentsHint').'</span></td>';
print '<td><input type="text" name="THEMESWITCHERLITE_ACCENTS" value="'.dol_escape_htmltag($rawAccents).'" class="minwidth500 quatrevingtpercent">';
print '<div class="tsl-cfg-preview">';
foreach ($accents as $hex) {
	print '<span class="tsl-cfg-dot" style="background:'.dol_escape_htmltag($hex).';" title="'.dol_escape_htmltag($hex).'"></span>';
}
print '</div>';
print '<div class="tsl-cfg-hint">'.$langs->trans('CfgAccentsPreview').'</div>';
print '</td></tr>';

// Default accent
print '<tr class="oddeven"><td><b>'.$langs->trans('CfgDefaultAccent').'</b><br><span class="opacitymedium small">'.$langs->trans('CfgDefaultAccentHint').'</span></td>';
print '<td><select name="THEMESWITCHERLITE_DEFAULT_ACCENT" class="flat minwidth200">';
print '<option value=""'.($defaultAccent === '' ? ' selected' : '').'>'.$langs->trans('TslNoAccent').'</option>';
foreach ($accents as $hex) {
	print '<option value="'.dol_escape_htmltag($hex).'"'.($defaultAccent === $hex ? ' selected' : '').'>'.dol_escape_htmltag($hex).'</option>';
}
print '</select></td></tr>';

// Allow dark
print '<tr class="oddeven"><td><b>'.$langs->trans('CfgAllowDark').'</b><br><span class="opacitymedium small">'.$langs->trans('CfgAllowDarkHint').'</span></td>';
print '<td><input type="checkbox" name="THEMESWITCHERLITE_ALLOW_DARK" value="1"'.(tslAllowDark() ? ' checked' : '').'></td></tr>';

// Dark default
print '<tr class="oddeven"><td><b>'.$langs->trans('CfgDarkDefault').'</b><br><span class="opacitymedium small">'.$langs->trans('CfgDarkDefaultHint').'</span></td>';
print '<td><input type="checkbox" name="THEMESWITCHERLITE_DARK_DEFAULT" value="1"'.(tslDarkDefault() ? ' checked' : '').'></td></tr>';

print '</table>';

// Save/reset bar. For a non-admin (e.g. a demo viewer) the controls stay VISIBLE but
// DISABLED; every write is also blocked server-side by the guards above.
print '<div class="center" style="margin:18px 0;">';
if ($user->admin) {
	print '<input type="submit" class="button button-save" value="'.$langs->trans('Save').'">';
	print ' &nbsp; ';
	print '<a class="button button-cancel" href="'.$_SERVER['PHP_SELF'].'?action=reset&token='.newToken().'" onclick=\'return confirm('.json_encode($langs->transnoentitiesnoconv('TslResetConfirm'), JSON_UNESCAPED_UNICODE).');\'>'.$langs->trans('TslResetButton').'</a>';
} else {
	print '<input type="submit" class="button button-save" value="'.$langs->trans('Save').'" disabled style="opacity:.5;cursor:not-allowed;">';
	print ' &nbsp; ';
	print '<span class="button button-cancel" aria-disabled="true" style="opacity:.5;cursor:not-allowed;">'.$langs->trans('TslResetButton').'</span>';
}
print '</div>';

print '</form>';

print '<div class="opacitymedium small" style="margin-top:10px;">'.$langs->trans('TslTryHint').'</div>';

print dol_get_fiche_end();

llxFooter();
$db->close();
