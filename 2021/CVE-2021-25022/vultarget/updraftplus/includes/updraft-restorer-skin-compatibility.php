<?php

if (!defined('ABSPATH')) die('No direct access.');

class Updraft_Restorer_Skin extends Updraft_Restorer_Skin_Main {

	public function feedback($string, ...$args) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ellipsisFound -- spread operator is not supported in PHP < 5.5 but WP 5.3 supports PHP 5.6 minimum
		parent::updraft_feedback($string);
	}
}
