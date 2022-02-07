<?php

if (!defined('ABSPATH')) die('No direct access.');

class Automatic_Upgrader_Skin extends Automatic_Upgrader_Skin_Main {

	public function feedback($string, ...$args) { // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ellipsisFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- spread operator is not supported in PHP < 5.5 but WP 5.3 supports PHP 5.6 minimum
		parent::updraft_feedback($string);
	}
}
