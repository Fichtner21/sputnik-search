<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;
use \Inc\Base\Login;

class CreateIndex extends BaseController {
    public $existsResponse = array();
    public $createIndexText = 'Tworzenie indeksu...';
    public $createdIndexText = "Index został utworzony.";
    public $createdIndexAskText = "Indeks dla strony został utworzony. Odświeżyć strone?";
    public $permissionDeniedText = 'Brak dostępu do ElasticSearch';
    public $connectionErrorText = 'Błąd połączenia z ElasticSearch';

    public function register() {
        add_action('wp_ajax_check_if_index_exists', array( $this, 'check_if_index_exists' ) );
        add_action('init', array( $this, 'admin_tool_bar' ), 10 );
        add_action('admin_menu', array( $this, 'admin_tool_bar' ) );
    }

    public function createIndex($id) {
        $login = new Login;
        $login->register();

        if($login->token) {
            $headers = array("Authorization: $login->token");

            $this->existsResponse = $this->method("GET", "indices/$id", null, $headers);

            if($this->existsResponse['info']['http_code'] == 404) {
                $response = $this->method("PUT", "indices/$id", null, $headers);

                $output = '<div style="color: orange; margin: 20px; font-size: 20px;"><span class="dashicons dashicons-clock"></span> Wysłano request o dodanie indeksu.</div>';

                echo $output;
            } elseif($this->existsResponse['info']['http_code'] == 200) {
                $output = '<div style="color: green; margin: 20px; font-size: 20px;"><span class="dashicons dashicons-yes"></span>Indeks dla tej strony już istnieje</div>';

                echo $output;
            } else {
                $output = '<div style="color: red; margin: 20px; font-size: 20px;"><span class="dashicons dashicons-warning"></span>Błąd podczas połączenia z ElasticSearch:'. $existsResponse['info']['http_code'] .'</div>';

                echo $output;
            }
        }
    }

    public function check_if_index_exists() {
        $login = new Login;
        $login->register();

        $headers = array("Authorization: $login->token");

        $this->existsResponse = $this->method("GET", "indices/$this->blog_id", null, $headers);

        echo $this->existsResponse['info']['http_code'];

        wp_die();
    }

    public function my_action_javascript() { ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var data = {
                    'action': 'check_if_index_exists'
                };

                var currentStatus = "<?php echo $this->existsResponse['info']['http_code']; ?>";

                if(currentStatus !== "200") {
                    var executed = false;
                    var interval = setInterval(function() {
                        jQuery.post(ajaxurl, data, function(response) {
                            if(response === "200" && currentStatus != response) {
                                if(executed) return;

                                clearInterval(interval);

                                executed = true;

                                jQuery("#wp-admin-bar-website-status").removeClass('error');
                                jQuery("#wp-admin-bar-website-status").removeClass('progress');
                                jQuery("#wp-admin-bar-website-status").addClass('success');
                                jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $this->createdIndexText; ?>");

                                var reload = confirm("<?php echo $this->createdIndexAskText; ?>");

                                if(reload) {
                                    location.reload();
                                }
                            } else if(response === "404" && currentStatus != response) {
                                jQuery("#wp-admin-bar-website-status").removeClass('error');
                                jQuery("#wp-admin-bar-website-status").removeClass('success');
                                jQuery("#wp-admin-bar-website-status").addClass('progress');
                                jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $this->createIndexText; ?>");
                            } else if(response === "401" && currentStatus != response) {
                                jQuery("#wp-admin-bar-website-status").removeClass('success');
                                jQuery("#wp-admin-bar-website-status").removeClass('progress');
                                jQuery("#wp-admin-bar-website-status").addClass('error');
                                jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $this->permissionDeniedText; ?>");
                            } else if(currentStatus != response) {
                                jQuery("#wp-admin-bar-website-status").removeClass('success');
                                jQuery("#wp-admin-bar-website-status").removeClass('progress');
                                jQuery("#wp-admin-bar-website-status").addClass('error');
                                jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $this->connectionErrorText; ?>");
                            }
                        });
                    }, 5000);
                }
            });
        </script> <?php
    }

   public function custom_toolbar_link_not_found($wp_admin_bar) {
        $args = array(
            'id' => 'website-status',
            'title' => $this->createIndexText,
            'href' => get_home_path() . '/wp-admin/admin.php?page=sputnik-search',
            'meta' => array(
                'class' => 'website-status progress',
                'title' => $this->createIndexText
            )
        );

        $wp_admin_bar->add_node($args);
    }

    public function custom_toolbar_link_unauthorized($wp_admin_bar) {
        $args = array(
            'id' => 'website-status',
            'title' => $this->permissionDeniedText,
            'href' => get_home_path() . '/wp-admin/admin.php?page=sputnik-search',
            'meta' => array(
                'class' => 'website-status error',
                'title' => $this->permissionDeniedText
            )
        );

        $wp_admin_bar->add_node($args);
    }

    public function custom_toolbar_link_ok($wp_admin_bar) {
        $args = array(
            'id' => 'website-status',
            'title' => 'Ok',
            'href' => get_home_url() . '/wp-admin/admin.php?page=sputnik-search',
            'meta' => array(
                'class' => 'website-status success',
                'title' => 'ok'
            )
        );

        $wp_admin_bar->add_node($args);
    }

    public function custom_toolbar_link_connection_error($wp_admin_bar) {
        $args = array(
            'id' => 'website-status',
            'title' => $this->connectionErrorText,
            'href' => get_home_path() . '/wp-admin/admin.php?page=sputnik-search',
            'meta' => array(
                'class' => 'website-status error',
                'title' => $this->connectionErrorText
            )
        );

        $wp_admin_bar->add_node($args);
    }

    public function admin_tool_bar(){
        if(!is_network_admin() && is_admin()) {
            $login = new Login;
            $login->register();

            $headers = array("Authorization: $login->token");

            $this->existsResponse = $this->method("GET", "indices/$this->blog_id", null, $headers);

            add_action('admin_footer', array($this, 'my_action_javascript' ) );

            if($this->existsResponse['info']['http_code'] == 404) {
                add_action('admin_bar_menu', array( $this, 'custom_toolbar_link_not_found' ), 999);
            } else if($this->existsResponse['info']['http_code'] == 401) {
                add_action('admin_bar_menu', array( $this, 'custom_toolbar_link_unauthorized' ), 999);
            } else if($this->existsResponse['info']['http_code'] == 200) {
                add_action('admin_bar_menu', array( $this, 'custom_toolbar_link_ok' ), 999);
            } else if($this->existsResponse['info']['http_code'] != 200) {
                add_action('admin_bar_menu', array( $this, 'custom_toolbar_link_connection_error' ), 999);
            }
        }
    }
}