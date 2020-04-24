<?php 

function es() {
	add_menu_page('es_plugin', 'Elastic Search', 'read', 'es_plugin', 'es_plugin', 'dashicons-image-filter');
	add_submenu_page('es_plugin', 'Indeksowanie wpisów', 'Indeksowanie wpisów', 'read', 'es_plugin_current_index_url', 'es_plugin_current_index');
	add_submenu_page('es_plugin', 'Indeksowanie plików', 'Indeksowanie plików', 'read', 'es_plugin_current_file_index_url', 'es_plugin_current_file_index');
	add_submenu_page('es_plugin', 'usuwanie indeksu', 'Usuwanie indeksu', 'read', 'es_plugin_delete_index', 'es_plugin_delete_index');
}

add_action('admin_menu', 'es');

function es_plugin() {
	createIndex(get_current_blog_id());

	// $post = get_post(12564);

	// print_r($post);

	// print_r(get_the_category(12564));

	// $url = 'http://sp12.krosno.nowoczesnyurzad.pl/wp-content/uploads/sites/4/2017/07/list-motywacyjny-wzor-2.pdf';
	// $response = wp_remote_get(esc_url_raw($url));
	// $api_response = get_attached_file(45);

	// $content = file_get_contents($api_response);

	// echo base64_encode($content);

	// echo $content;

	// print_r($api_response);
}

function es_plugin_delete_index(){
	deleteIndex(1);
}

function es_plugin_current_index() {
	// $posts_arr = get_posts(array('numberposts' => -1, 'post_status' => 'publish'));
	// $posts_ids = array();
	// foreach ($posts_arr as $post_arr) {
	// 	$posts_ids[] = $post_arr->ID;
	// }
	// print_r($posts_ids);
	?>
	<button id="synchronize">Synchronize</button>
	<ul id="es-logs"></ul>
	<script type="text/javascript">
		jQuery(document).ready(function($) {

			$('#synchronize').click(function(){
				var index = 0;

				sendPostToES(index);
			});

			function sendPostToES(index) {
				var data = {
					'action': 'index_post_in_es',
					id: index
				};

				jQuery.post(ajaxurl, data, function(response) {
					$('#es-logs').append("<li style='color: green;'>Dodano wpis " + index + "!</li>");

					sendPostToES(++index);
				}).fail(function() {
					$('#es-logs').append("<li style='color: red;'>Nie dodano wpisu " + index + "!</li>");

					sendPostToES(++index);
				});
				
			}
		});
	</script> 
	<?php
}

function es_plugin_current_file_index() {
	// $args = array(
	// 	'post_type' => 'attachment',
	// 	'numberposts' => 1,
	// 	'post_mime_type' => array('application/pdf', "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/vnd.oasis.opendocument.text"),
	// 	'offset' => 2
	// );

	// $attachments = get_posts($args);

	// print_r($attachments);

	?>
	<button id="synchronize">Synchronize</button>

	<ul id="es-logs">
		
	</ul>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#synchronize').click(function(){
				var index = 3905;

				sendPostToES(index);
			});

			function sendPostToES(index) {
				var data = {
					'action': 'index_attachment_in_es',
					id: index
				};

				jQuery.post(ajaxurl, data, function(response) {
					$('#es-logs').append("<li style='color: green;'>Dodano załącznik " + index + "!</li>");

					sendPostToES(++index);
				}).fail(function() {
					$('#es-logs').append("<li style='color: red;'>Nie dodano załącznika " + index + "!</li>");

					sendPostToES(++index);
				});
				
			}
		});
	</script> 

	<?php
}

//add_action('admin_menu','index_post_in_es');
function index_post_in_es() {
	// $args = array(
	// 	'public' => true
	// );
	// $post_types = get_post_types($args);
	// $get_post_types = get_post_types(array( 'public' => true ));
	// $post_types = array();

	// foreach($get_post_types as $post_type) {
	// 	array_push($post_types, $post_type);
	// }	
	
	$post_types = array(
	 	'post',
	 	'page',
	 	'komunikaty',
	 	'galerie',
	 	'wydarzenia',
	 	'wydarzenia-category',
	 	'kategoria-wydarzenia',
	 	'investment'
	// 	//'sesja_rady',
	// 	//'sport_object',
	// 	// 'club',
	// 	// 'sciezki_rowerowe',
	// 	// 'spacer_po_miescie',
	// 	// 'culture',
	// 	// 'przyroda',
	// 	// 'zabytki_i_koscioly',
	// 	// 'komisje',
	// 	// 'radni',
	// 	// 'adresy',
	// 	// 'band',
	// 	// 'uep',
	// 	// 'pracownicy',
	// 	// 'event'
	 );

	$posts_arr = get_posts(array('posts_per_page' => 1, 'post_status' => 'publish', 'post_type'=> $post_types, 'offset' => $_POST['id']));
	//var_dump($posts_arr);
	echo print_r($posts_arr, true);

	if(count($posts_arr) > 0) {
		login();

		$res = synchronize_with_ES($posts_arr[0]->ID);

		file_put_contents(__DIR__ . '/test_' . $posts_arr[0]->ID . '.text', print_r($res, true));

		status_header($res['info']['http_code']);
	} else {
		status_header(400);
	}

	wp_die();
}

add_action('wp_ajax_index_post_in_es', 'index_post_in_es');

function index_attachment_in_es() {
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => array('application/pdf', "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/vnd.oasis.opendocument.text"),
		'offset' => $_POST['id']
	);

	$attachments = get_posts($args);

	if(count($attachments) > 0) {
		login();

		$res = add_attachment_func($attachments[0]->ID);

		status_header($res['info']['http_code']);

		echo print_r($res, true);
	} else {
		status_header(400);
	}

	wp_die();
}

add_action('wp_ajax_index_attachment_in_es', 'index_attachment_in_es');
