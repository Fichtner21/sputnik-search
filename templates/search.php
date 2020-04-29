<?php
/**
 * The template for displaying search results pages
*/

use \Inc\Base\SputnikSearch;

$sputnikSearch = new SputnikSearch($post_types, $_GET['sq'], $paged, $posts_per_page);

$posts = $sputnikSearch->get_results();

$count = $sputnikSearch->get_count();

// Search posts template
if(!function_exists('search_posts_template')) {
    function search_posts_template($post_var) {
        $post_tags = get_the_tags($post_var['ID']);
		$terms = get_the_terms( $post_var['ID'] , 'wydarzenia-category' );
		
		$post_title = preg_replace("/(\p{L}*?)(". preg_quote($_GET['sq']) .")(\p{L}*)/ui", "$1<span class=\"h\"><mark>$2</mark></span>$3", $post_var['post_title']);

        // start output
        $output = '<a href="'. get_the_permalink($post_var['ID']) .'">';
        $output .= '<article>';

        $output .= has_post_thumbnail($post_var['ID']) ? '<div class="thumbnail"><img src="'. get_the_post_thumbnail_url($post_var['ID'], "medium") .'" alt=""></div>'    : '<img src="' . esc_url(get_template_directory_uri()).'/images/luban_mock_logo.png" title="Gmina Lubań"/>';    

        $output .= '<div class="center">';
        $output .= '<h5>'. $post_title .'</h5>';
        $output .= '<div class="content">'. $post_var['post_content'] .'</div>';

        if ( is_array( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ($terms as $term) {
                $term_link = get_term_link($term, 'wydarzenia-category');
                if (is_wp_error($term_link))
                    continue;
                $output .= '<span>Kategoria: </span><a href="' . $term_link . '">' . $term->name . '</a> ';
            }
        } else {
            $output .= '';
        }
        
        if($post_tags) {
            $output .= '<div class="tags"><span>'. __('TAGI', 'main') .'</span>';

            foreach($post_tags as $post_tag) {
                $output .= '<span class="tag-name">'. $post_tag->name .'</span>';
            }

            $output .= '</div>';
        }


        $output .= '<div class="left">';
        $output .= '<div class="date"><i class="fa fa-clock-o" aria-hidden="true"></i>'. __('Data dodania', 'main');
        $output .= '<span class="post-date">'. get_the_date("j-m-Y", $post_var['ID']) .'</span></div>';
        $output .= '<div class="more">'. __('CZYTAJ WIĘCEJ', 'main') .'</div></div>';

        // end output
        $output .= '</article></a>';
    
        // return output
        return $output;
    }
}

get_header(); ?>
	<div class="section" id="content">
		<div class="container">
			<div class="section-title search">
				<h1><?php echo __('Wyniki wyszukiwania', 'main') . ': ' . $_GET['sq']; ?></h1>
			</div>
			<div class="archive-section">	
				<div class="archive-list search-list">
				<?php
					if( isset( $_GET['d_from'] ) ) {
						$date_from = $_GET['d_from'];
						$date_from_format = date('d-m-Y', strtotime($date_from));
						$date_from_value = intval(strtotime($date_from_format));
					}

					if( isset( $_GET['d_to'] ) ) {
						$date_to = $_GET['d_to'];
						$date_to_format = date('d-m-Y', strtotime($date_to));
						$date_to_value = intval(strtotime($date_to_format));
					}
					
					?>
					<?php 
					if($count > 0) {
						foreach($posts as $found_post) {
							$post_date = get_the_date('d-m-Y', $found_post['ID']); 
							$post_date_value = intval(strtotime($post_date));

							if(
								isset($_GET['sq']) &&
								( isset($_GET['d_from']) && $_GET['d_from'] != null ) &&
								( isset($_GET['d_to']) && $_GET['d_to'] != null )
							) {
								if( $post_date_value >= $date_from_value && $post_date_value <= $date_to_value ) {
									echo search_posts_template($found_post);
								}
							} elseif( isset($_GET['sq']) && ( isset($_GET['d_from']) && $_GET['d_from'] != null ) ) {
								if( $post_date_value >= $date_from_value ) {
									echo search_posts_template($found_post);
								}
							} elseif( isset($_GET['sq']) && ( isset($_GET['d_to']) && $_GET['d_to'] != null ) ) {
								if( $post_date_value <= $date_to_value ) {
									echo search_posts_template($found_post);
								}
							} else {
								echo search_posts_template($found_post);
							}
						}
					} ?>
				</div>
				<!-- Search pagination -->
				<?php require_once(__DIR__ . '/search-pagination.php'); ?>
			</div>
		</div>
	</div>

<?php get_footer();