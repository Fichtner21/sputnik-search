<?php

if (isset($_POST) && !empty($_POST)) {
    if(isset($_POST['custom-css'])) {
        update_option( 'custom_css', $_POST['custom-css'] );
    }

    // Add terms to choosen options
    $choosen_terms = array();

    $get_all_taxonomies = get_taxonomies(array(
        'public' => true
    ));

    foreach($get_all_taxonomies as $taxonomy) {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        foreach($terms as $term) {
            $term_name_slug = trim( strtolower( str_replace( ' ', '-', $term->name ) ) );
            $term_ID = $term->term_id;

            if(isset($_POST[$term_ID])) {
                array_push($choosen_terms, $term_ID);

                update_option( 'choosen_term_' . $term_ID, $term_ID );
            } else {
                delete_option( 'choosen_term_' . $term_ID );
            }
        }
    }

    if(isset($choosen_terms) && !empty($choosen_terms)) {
        $choosen_terms_string = implode(',', $choosen_terms);

        update_option( 'choosen_terms', $choosen_terms_string );
    }
}

$custom_css = get_option('custom_css');

if(!empty($_POST) && get_option('custom_css')) {
    $custom_css_filename = plugin_dir_path(dirname( __FILE__ ) ) . 'assets/public/custom-css.css';

    file_put_contents($custom_css_filename, $custom_css);
}

?>

<div class="sputnik-search-page">
    <div class="sputnik-search-page__inner">
        <div class="sputnik-search-page__branding">
            <img src="<?= plugin_dir_url( dirname( __FILE__ ) ); ?>/assets/admin/logo-sputnik.svg" alt="">
        </div>

        <div class="sputnik-search-page__content">
            <h1 class="sputnik-search-page__title"><?= __('Sputnik Search','sputnik-search'); ?></h1>
            <p class="sputnik-search-page__text"><?= __('Zaawansowana wyszukiwarka stworzona przy użyciu ElasticSearch','sputnik-search'); ?></p>

            <form method="POST" class="sputnik-search-form" data-tab='js-user-content'>
                <div class="sputnik-search-form__row">
                    <h3 class="sputnik-search-form__choose-title"><?= __('Wybierz kategorie:','sputnik-search'); ?>:</h3>
                    <?php
                        $get_all_taxonomies = get_taxonomies(array(
                            'public' => true
                        ));

                        $cat_terms = array();

                        echo '<button type="button" id="js-sputnik-search-categories-list-toggle">'. __('Rozwiń listę kategorii', 'sputnik-search') .'</button>';
                        echo '<ul class="content-categories" id="js-sputnik-search-categories-list">';

                        foreach($get_all_taxonomies as $taxonomy) {
                            $terms = get_terms([
                                'taxonomy' => $taxonomy,
                                'hide_empty' => false,
                            ]);

                            foreach($terms as $term) {
                                $term_name_slug = trim( strtolower( str_replace( ' ', '-', $term->name ) ) );
                                $term_ID = $term->term_id;

                                $cat_terms[$term_name_slug] = $term_ID;
                            }
                        }

                        if(isset($cat_terms) && !empty($cat_terms)) {
                            $i = 0;
                            foreach($cat_terms as $term_name => $term_id) {
                                $term_name = get_term( $term_id )->name;
                                $choosed_option = get_option( 'choosen_term_' . $term_id );

                                if($term_id == $choosed_option) {
                                    $term_output = '<li class="content-categories__item active"><label title="'. $term_name .'" class="content-categories__label" for="'. $term_id .'"><input type="checkbox" id="'. $term_id .'" name="'. $term_id .'" class="content-categories__checkbox" checked>'. $term_name .'</label></li>';
                                } else {
                                    $term_output = '<li class="content-categories__item"><label title="'. $term_name .'" class="content-categories__label" for="'. $term_id .'"><input type="checkbox" id="'. $term_id .'" name="'. $term_id .'" class="content-categories__checkbox">'. $term_name .'</label></li>';
                                }

                                echo $term_output;
                            }
                        } else {
                            echo __('Aktualnie nie ma żadnych kategorii','sputnik-search');
                        }

                        echo '</ul>';
                    ?>
                </div>
                <div class="sputnik-search-form__row">
                    <h3 class="sputnik-search-form__choose-title"><?= __('Własny kod CSS','sputnik-search'); ?>:</h3>
                    <textarea class="sputnik-search-form__textarea" name="custom-css" id="custom-css" cols="30" rows="5"><?= $custom_css ? $custom_css : false; ?></textarea>
                </div>
                <div class="sputnik-search-form__row">
                    <button type="submit" class="btn btn--medium btn--primary sputnik-search-form__submit" title="Zapisz dane"><?= __('Zapisz dane','sputnik-search'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>