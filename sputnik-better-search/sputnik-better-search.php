<?php 
/*
Plugin Name: Better Search
Author: Przemysław Ratajczak
*/

define("SBS_TABLE_NAME", "sbs_synonyms");

include_once(__DIR__ . "/helpers.php");

function sbs_plugin_activation() {
	global $wpdb;

	$table_name = $wpdb->prefix . SBS_TABLE_NAME;

	$wpdb->get_results("
		CREATE TABLE IF NOT EXISTS `$table_name` (
			`id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`words` TEXT NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

	$count = $wpdb->get_var("SELECT count(*) FROM `$table_name`");

	if($count == 0) {
		$handle = @fopen(__DIR__ . "/synonyms", "r");
		
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$wpdb->insert($table_name, array(
					'words' => $buffer
				));
			}
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
	}
}

register_activation_hook(__FILE__, 'sbs_plugin_activation');