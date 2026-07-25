<?php
/* Theme Switcher Lite - dynamic stylesheet
 * Copyright (C) 2026 DoliResources - GPL-3.0-or-later
 *
 * Injected on every page via module_parts['css'].
 * - Accent: remaps two key Dolibarr colour variables to var(--tsl-accent),
 *   which the JS sets from the user's choice (stored in localStorage).
 * - Dark mode: re-uses Dolibarr's own native dark palette, but scoped to
 *   html[data-tsl-theme="dark"] so it can be toggled per user (client side)
 *   instead of the global THEME_DARKMODEENABLED constant.
 */

if (!defined('NOREQUIRESOC'))    define('NOREQUIRESOC', '1');
if (!defined('NOREQUIRETRAN'))   define('NOREQUIRETRAN', '1');
if (!defined('NOCSRFCHECK'))     define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL', 1);
if (!defined('NOLOGIN'))         define('NOLOGIN', 1);
if (!defined('NOREQUIREMENU'))   define('NOREQUIREMENU', 1);
if (!defined('NOREQUIREHTML'))   define('NOREQUIREHTML', 1);
if (!defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX', '1');

session_cache_limiter(false);

$res = 0;
if (!$res && file_exists("../../../main.inc.php"))    $res = @include "../../../main.inc.php";
if (!$res && file_exists("../../../../main.inc.php")) $res = @include "../../../../main.inc.php";
if (!$res && file_exists("../../main.inc.php"))       $res = @include "../../main.inc.php";

header('Content-Type: text/css; charset=UTF-8');
header('Cache-Control: max-age=3600');
?>
/* ---------------------------------------------------------------------------
 * Theme Switcher Lite
 * ------------------------------------------------------------------------- */

/* Accent colour: two key surfaces follow --tsl-accent (top menu bar + tabs
 * underline, and primary action buttons). Applied only when the user picked
 * an accent (JS adds the data-tsl-accent attribute + the --tsl-accent value). */
html[data-tsl-accent] {
	--colorbackhmenu1: var(--tsl-accent) !important;
	--butactionbg: var(--tsl-accent) !important;
}
html[data-tsl-accent] .button-save,
html[data-tsl-accent] input[type=submit].button:not(.button-cancel):not(.button-delete):not(.buttonreset) {
	background-color: var(--tsl-accent) !important;
	border-color: var(--tsl-accent) !important;
}

/* Dark mode: native Dolibarr dark palette, scoped to a per-user attribute. */
html[data-tsl-theme="dark"] {
	--colorbackhmenu1: #3d3e40;
	--colorbackvmenu1: #2b2c2e;
	--colorbacktitle1: #3b3c3e;
	--colorbacktabcard1: #1d1e20;
	--colorbacktabactive: rgb(220,220,220);
	--colorbacklineimpair1: #38393d;
	--colorbacklineimpair2: #2b2d2f;
	--colorbacklinepair1: #38393d;
	--colorbacklinepair2: #2b2d2f;
	--colorbacklinepairhover: #2b2d2f;
	--colorbacklinepairchecked: #0e5ccd;
	--colorbackbody: #1d1e20;
	--colorbackmobilemenu: #080808;
	--colorbackgrey: #0f0f0f;
	--tooltipbgcolor: #2b2d2f;
	--colortexttitlenotab: rgb(220,220,220);
	--colortexttitlenotab2: rgb(220,220,220);
	--colortexttitle: rgb(220,220,220);
	--colortext: rgb(220,220,220);
	--colortextlink: #4390dc;
	--colortexttitlelink: #4390dc;
	--colortextbackhmenu: rgb(220,220,220);
	--colortextbackvmenu: rgb(220,220,220);
	--tooltipfontcolor: rgb(220,220,220);
	--listetotal: rgb(245, 83, 158);
	--inputbackgroundcolor: rgb(70, 70, 70);
	--inputbackgroundcolordisabled: rgb(60, 60, 60);
	--inputcolordisabled: rgb(140, 140, 140);
	--inputbordercolor: rgb(220,220,220);
	--oddevencolor: rgb(220,220,220);
	--colorboxstatsborder: rgb(65,100,138);
	--dolgraphbg: #1d1e20;
	--fieldrequiredcolor: rgb(250,183,59);
	--colortextbacktab: rgb(220,220,220);
	--colorboxiconbg: rgb(36,38,39);
	--refidnocolor: rgb(220,220,220);
	--tableforfieldcolor: rgb(220,220,220);
	--amountremaintopaycolor: rgb(252,84,91);
	--amountpaymentcomplete: rgb(101,184,77);
	--amountremaintopaybackcolor: rgb(245,130,46);
	--tablevalidbgcolor: rgb(80, 64, 33);
	--colorblack: #fff;
	--colorwhite: #000;
}
html[data-tsl-theme="dark"] body,
html[data-tsl-theme="dark"] button {
	color: #bbb;
}
/* Keep the injected switcher readable on the accent/dark top bar. */
html[data-tsl-theme="dark"] .tsl-switch { color: #ddd; }

/* ---- The switcher control injected in the top-right menu ---- */
.tsl-switch {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 0 6px;
	vertical-align: middle;
}
.tsl-accent-wrap { position: relative; display: inline-flex; }

/* Compact round buttons (accent trigger + dark toggle) */
.tsl-trigger,
.tsl-toggle {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 26px;
	height: 26px;
	border-radius: 50%;
	border: none;
	background: rgba(255,255,255,.16);
	color: inherit;
	cursor: pointer;
	font-size: 13px;
	padding: 0;
	position: relative;
}
.tsl-trigger:hover,
.tsl-toggle:hover { background: rgba(255,255,255,.30); }
/* Small dot on the accent trigger reflecting the current accent colour. */
.tsl-trigger .tsl-current {
	position: absolute;
	right: -1px;
	bottom: -1px;
	width: 9px;
	height: 9px;
	border-radius: 50%;
	border: 1.5px solid var(--colorbackhmenu1, #1b2437);
	background: #ffffff;
	display: none;
}
.tsl-trigger.tsl-has-accent .tsl-current { display: block; }

/* The dropdown panel with the swatch grid */
.tsl-dropdown {
	position: absolute;
	top: 34px;
	right: 0;
	z-index: 10050;
	display: none;
	grid-template-columns: repeat(5, 20px);
	gap: 8px;
	padding: 12px;
	background: #ffffff;
	border: 1px solid #e2e6ee;
	border-radius: 10px;
	box-shadow: 0 8px 26px rgba(20,30,60,.22);
}
.tsl-accent-wrap.tsl-open .tsl-dropdown { display: grid; }
.tsl-dropdown::before {
	content: "";
	position: absolute;
	top: -6px;
	right: 12px;
	width: 11px;
	height: 11px;
	background: inherit;
	border-left: 1px solid #e2e6ee;
	border-top: 1px solid #e2e6ee;
	transform: rotate(45deg);
}
html[data-tsl-theme="dark"] .tsl-dropdown { background: #2b2d2f; border-color: #3d3e40; }
html[data-tsl-theme="dark"] .tsl-dropdown::before { border-color: #3d3e40; }

.tsl-swatch {
	width: 20px;
	height: 20px;
	border-radius: 50%;
	border: 2px solid rgba(0,0,0,.10);
	cursor: pointer;
	padding: 0;
	box-shadow: 0 1px 2px rgba(0,0,0,.2);
	transition: transform .12s ease;
	outline: none;
}
.tsl-swatch:hover { transform: scale(1.18); }
.tsl-swatch.tsl-active { box-shadow: 0 0 0 2px #246bfd; transform: scale(1.1); }
.tsl-swatch-none {
	background:
		linear-gradient(45deg, transparent 44%, #e0592a 44%, #e0592a 56%, transparent 56%),
		#f2f2f2;
}
