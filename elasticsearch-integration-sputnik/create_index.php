<?php 

//add_action('admin_menu', 'createIndex');

function createIndex($id) {
	global $token;
	
	if($token) {
		$headers = array("Authorization: $token");

		$existsResponse = method("GET", "indices/$id", null, $headers);

		if($existsResponse['info']['http_code'] == 404) {
			$response = method("PUT", "indices/$id", null, $headers);

			print_r($response);
			?>
				<div style='color: orange; margin: 20px; font-size: 20px;'>
					<span class="dashicons dashicons-clock"></span>
					Wysłano request o dodanie indeksu.
				</div>
			<?php
		} else if($existsResponse['info']['http_code'] == 200) {
			?>
				<div style='color: green; margin: 20px; font-size: 20px;'>
					<span class="dashicons dashicons-yes"></span>
					Indeks dla tej strony już istnieje
				</div>
			<?php
		} else {
			?>
				<div style='color: red; margin: 20px; font-size: 20px;'>
					<span class="dashicons dashicons-warning"></span>
					Błąd podczas połączenia z ElasticSearch: <?php echo $existsResponse['info']['http_code']; ?>
					<br>
					<?php  
						echo 'Token: ';
						var_dump($headers);
						echo '<br>';
					?>
					<?php 
						echo '<pre>';
						var_dump($existsResponse);
						echo '</pre>';
					?>						
				</div>
			<?php
		}
	}
}

function unsetMenuPage($type) {
	global $wp_post_types;

	if (isset($wp_post_types[$type])) {
		unset($wp_post_types[$type]);
	}
}

function check_if_index_exists() {
	$token = login();

	$blog_id = get_current_blog_id();

	$headers = array("Authorization: $token");

	$existsResponse = method("GET", "indices/$blog_id", null, $headers);

	echo $existsResponse['info']['http_code'];

	wp_die();
}

add_action('wp_ajax_check_if_index_exists', 'check_if_index_exists');

$existsResponse = array();
$createIndexText = 'Tworzenie indeksu...';
$createdIndexText = "Index został utworzony.";
$createdIndexAskText = "Indeks dla strony został utworzony. Odświeżyć strone?";
$permissionDeniedText = 'Brak dostępu do ElasticSearch';
$connectionErrorText = 'Błąd połączenia z ElasticSearch';

function my_action_javascript() { 
	global $createIndexText, $createdIndexText, $createdIndexAskText, $permissionDeniedText, $connectionErrorText, $existsResponse;

	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			var data = {
				'action': 'check_if_index_exists'
			};

			var currentStatus = "<?php echo $existsResponse['info']['http_code']; ?>";

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
							jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $createdIndexText; ?>");

							var reload = confirm("<?php echo $createdIndexAskText; ?>");

							if(reload) {
								location.reload();
							}
						} else if(response === "404" && currentStatus != response) {
							jQuery("#wp-admin-bar-website-status").removeClass('error');
							jQuery("#wp-admin-bar-website-status").removeClass('success');
							jQuery("#wp-admin-bar-website-status").addClass('progress');
							jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $createIndexText; ?>");
						} else if(response === "401" && currentStatus != response) {
							jQuery("#wp-admin-bar-website-status").removeClass('success');
							jQuery("#wp-admin-bar-website-status").removeClass('progress');
							jQuery("#wp-admin-bar-website-status").addClass('error');
							jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $permissionDeniedText; ?>");
						} else if(currentStatus != response) {
							jQuery("#wp-admin-bar-website-status").removeClass('success');
							jQuery("#wp-admin-bar-website-status").removeClass('progress');
							jQuery("#wp-admin-bar-website-status").addClass('error');
							jQuery("#wp-admin-bar-website-status").find('.ab-item').text("<?php echo $connectionErrorText; ?>");
						}
					});
				}, 5000);
			}
			
		});
	</script> <?php
}

function custom_toolbar_link_not_found($wp_admin_bar) {
	global $createIndexText;

	$args = array(
		'id' => 'website-status',
		'title' => $createIndexText,
		'href' => '/wp-admin/admin.php?page=es_plugin',
		'meta' => array(
			'class' => 'website-status progress',
			'title' => $createIndexText
		)
	);

	$wp_admin_bar->add_node($args);
}

function custom_toolbar_link_unauthorized($wp_admin_bar) {
	global $permissionDeniedText;

	$args = array(
		'id' => 'website-status',
		'title' => $permissionDeniedText,
		'href' => '/wp-admin/admin.php?page=es_plugin',
		'meta' => array(
			'class' => 'website-status error',
			'title' => $permissionDeniedText
		)
	);

	$wp_admin_bar->add_node($args);
}

function custom_toolbar_link_ok($wp_admin_bar) {
	$args = array(
		'id' => 'website-status',
		'title' => 'Ok',
		'href' => '/wp-admin/admin.php?page=es_plugin',
		'meta' => array(
			'class' => 'website-status success',
			'title' => 'ok'
		)
	);

	$wp_admin_bar->add_node($args);
}

function custom_toolbar_link_connection_error($wp_admin_bar) {
	global $connectionErrorText;

	$args = array(
		'id' => 'website-status',
		'title' => $connectionErrorText,
		'href' => '/wp-admin/admin.php?page=es_plugin',
		'meta' => array(
			'class' => 'website-status error',
			'title' => $connectionErrorText
		)
	);

	$wp_admin_bar->add_node($args);
}

function custom_unregister_theme_post_types() {
	unsetMenuPage("post");
	unsetMenuPage("page");
	unsetMenuPage("wydarzenia");
	unsetMenuPage("wideo");
	unsetMenuPage("galerie");
	unsetMenuPage("media");
}

function admin_tool_bar(){
	global $token, $existsResponse;

	if(!is_network_admin() && is_admin()) {
		$blog_id = get_current_blog_id();

		$headers = array("Authorization: $token");

		$existsResponse = method("GET", "indices/$blog_id", null, $headers);

		add_action('admin_footer', 'my_action_javascript');

		if($existsResponse['info']['http_code'] == 404) {
			add_action('admin_bar_menu', 'custom_toolbar_link_not_found', 999);
			add_action('init', 'custom_unregister_theme_post_types', 20);
		} else if($existsResponse['info']['http_code'] == 401) {
			add_action('admin_bar_menu', 'custom_toolbar_link_unauthorized', 999);
		} else if($existsResponse['info']['http_code'] == 200) {
			add_action('admin_bar_menu', 'custom_toolbar_link_ok', 999);
		} else if($existsResponse['info']['http_code'] != 200) {
			add_action('admin_bar_menu', 'custom_toolbar_link_connection_error', 999);
		}

		function load_admin_style() {
			wp_enqueue_style('admin_css',  plugin_dir_url(__FILE__) . 'es-styles.css');
		}

		add_action('admin_enqueue_scripts', 'load_admin_style');
	}
}

add_action('init', 'admin_tool_bar', 10);
