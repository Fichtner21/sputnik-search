<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

class Activate {
    public static function activate() {
        // Flush rewrite rules
        add_action( 'init', 'flush_rewrite_rules' );
    }

    public static function create_synonyms_db_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'sbs_synonyms';

        $wpdb->get_results("
            CREATE TABLE IF NOT EXISTS `$table_name` (
                `id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `words` TEXT NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");

        $count = $wpdb->get_var("SELECT count(*) FROM `$table_name`");

        if($count == 0) {
            $handle = @fopen(plugin_dir_path( dirname( __FILE__, 2 ) ) . "/synonyms", "r");

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
}