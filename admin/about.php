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
 *  \file       custom/themeswitcherlite/admin/about.php
 *  \ingroup    themeswitcherlite
 *  \brief      About page of module Theme Switcher Lite.
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
if (!$user->admin) accessforbidden();

$title = $langs->trans('ThemeSwitcherLiteSetup');
llxHeader('', $title);

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($title, $linkback, 'title_setup');

$head = themeswitcherliteAdminPrepareHead();
print dol_get_fiche_head($head, 'about', '', -1, '');

print '<div class="fichecenter">';
print '<div style="max-width:760px;line-height:1.7;">';
print '<h3>'.$langs->trans('Module10000160Name').' — '.$langs->trans('Version').' 1.0</h3>';
print '<p>'.$langs->trans('TslAboutIntro').'</p>';
print '<ul>';
print '<li>'.$langs->trans('TslAboutFeat1').'</li>';
print '<li>'.$langs->trans('TslAboutFeat2').'</li>';
print '<li>'.$langs->trans('TslAboutFeat3').'</li>';
print '<li>'.$langs->trans('TslAboutFeat4').'</li>';
print '</ul>';
print '<p><b>'.$langs->trans('TslAboutEditor').'</b> '.$langs->trans('TslAboutEditorText').'</p>';
print '<p>';
print '<a class="button" href="https://www.doliresources.com" target="_blank" rel="noopener">doliresources.com</a> ';
print '<a class="button" href="https://demo.doliresources.com" target="_blank" rel="noopener">demo.doliresources.com</a>';
print '</p>';
print '<p class="opacitymedium small">'.$langs->trans('TslAboutLicense').'</p>';
print '</div></div>';

print dol_get_fiche_end();

llxFooter();
$db->close();
