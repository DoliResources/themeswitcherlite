<?php
/* Theme Switcher Lite - dynamic JS
 * Copyright (C) 2026 DoliResources - GPL-3.0-or-later
 * Injected on every page via module_parts['js'].
 */
if (!defined('NOREQUIRESOC'))    define('NOREQUIRESOC', '1');
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

header('Content-Type: application/javascript; charset=UTF-8');
header('Cache-Control: max-age=3600');
?>
(function () {
	"use strict";

	var LS_ACCENT = "tsl_accent";
	var LS_THEME  = "tsl_theme";

	function lsGet(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
	function lsSet(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }

	function applyAccent(hex) {
		var d = document.documentElement;
		if (hex) {
			d.setAttribute("data-tsl-accent", "");
			d.style.setProperty("--tsl-accent", hex);
		} else {
			d.removeAttribute("data-tsl-accent");
			d.style.removeProperty("--tsl-accent");
		}
	}

	function applyTheme(dark) {
		var d = document.documentElement;
		if (dark) { d.setAttribute("data-tsl-theme", "dark"); }
		else { d.removeAttribute("data-tsl-theme"); }
	}

	function currentAccent() { var a = lsGet(LS_ACCENT); return a === null ? "" : a; }
	function currentDark() { return lsGet(LS_THEME) === "dark"; }

	function refreshSwatches() {
		var cur = currentAccent();
		var nodes = document.querySelectorAll(".tsl-swatch");
		for (var i = 0; i < nodes.length; i++) {
			var val = nodes[i].getAttribute("data-color") || "";
			if (val === cur) { nodes[i].classList.add("tsl-active"); }
			else { nodes[i].classList.remove("tsl-active"); }
		}
		// Reflect the current accent on the palette trigger (small dot).
		var trig = document.querySelector(".tsl-trigger");
		if (trig) {
			var dot = trig.querySelector(".tsl-current");
			if (cur) { trig.classList.add("tsl-has-accent"); if (dot) dot.style.background = cur; }
			else { trig.classList.remove("tsl-has-accent"); }
		}
	}

	function closeDropdown() {
		var w = document.querySelector(".tsl-accent-wrap");
		if (w) { w.classList.remove("tsl-open"); }
		var t = document.querySelector(".tsl-trigger");
		if (t) { t.setAttribute("aria-expanded", "false"); }
	}
	function toggleDropdown(e) {
		var w = document.querySelector(".tsl-accent-wrap");
		if (!w) return;
		var open = w.classList.toggle("tsl-open");
		var t = document.querySelector(".tsl-trigger");
		if (t) { t.setAttribute("aria-expanded", open ? "true" : "false"); }
		e.preventDefault();
		e.stopPropagation();
	}

	function refreshToggle() {
		var btn = document.querySelector(".tsl-toggle");
		if (!btn) return;
		var dark = currentDark();
		var icon = btn.querySelector("span");
		if (icon) {
			icon.className = dark ? "fas fa-sun" : "fas fa-moon";
		}
		btn.setAttribute("aria-pressed", dark ? "true" : "false");
	}

	function onSwatchClick(e) {
		var hex = this.getAttribute("data-color") || "";
		lsSet(LS_ACCENT, hex);
		applyAccent(hex);
		refreshSwatches();
		closeDropdown();
		e.preventDefault();
	}

	function onToggleClick(e) {
		var dark = !currentDark();
		lsSet(LS_THEME, dark ? "dark" : "");
		applyTheme(dark);
		refreshToggle();
		e.preventDefault();
	}

	function wire() {
		var swatches = document.querySelectorAll(".tsl-swatch");
		for (var i = 0; i < swatches.length; i++) {
			swatches[i].addEventListener("click", onSwatchClick);
		}
		var trigger = document.querySelector(".tsl-trigger");
		if (trigger) { trigger.addEventListener("click", toggleDropdown); }
		var toggle = document.querySelector(".tsl-toggle");
		if (toggle) { toggle.addEventListener("click", onToggleClick); }

		// Close the dropdown on outside click or Escape.
		document.addEventListener("click", function (ev) {
			var w = document.querySelector(".tsl-accent-wrap");
			if (w && w.classList.contains("tsl-open") && !w.contains(ev.target)) { closeDropdown(); }
		});
		document.addEventListener("keydown", function (ev) {
			if (ev.key === "Escape" || ev.keyCode === 27) { closeDropdown(); }
		});

		// Re-assert stored state (the early <head> bootstrap already applied it, but
		// this keeps things correct if the control was injected after paint).
		applyAccent(currentAccent());
		applyTheme(currentDark());
		refreshSwatches();
		refreshToggle();
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", wire);
	} else {
		wire();
	}
})();
