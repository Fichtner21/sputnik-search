<?php

if (isset($_POST) && !empty($_POST)) {
    if(isset($_POST['es-username']) || isset($_POST['es-password'])){
        update_option( 'es_username', $_POST['es-username']);
        update_option( 'es_password', $_POST['es-password']);
    }

    if(isset($_POST['display-version'])) {
        update_option( 'display_version', $_POST['display-version'] );
    }

    if(isset($_POST['styles-option'])) {
        update_option( 'styles_option', $_POST['styles-option'] );
    }

    if(isset($_POST['search-version'])) {
        update_option( 'search_version', $_POST['search-version'] );
    }

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
            }
        }
    }

    if(isset($choosen_terms) && !empty($choosen_terms)) {
        $choosen_terms_string = implode(',', $choosen_terms);

        update_option( 'choosen_terms', $choosen_terms_string );
    }
}

$es_username = get_option('es_username');
$es_password = get_option('es_password');

$displayVersion = get_option('display_version');
$stylesOption = get_option( 'styles_option' );
$searchVersion = get_option( 'search_version' );

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

            <form method="POST" class="sputnik-search-form">
                <h2 class="sputnik-search-form__title"><?= __('Wypełnij pola, aby można było nawiązać połączenie','sputnik-search'); ?></h2>
                <div class="sputnik-search-form__row">
                    <label for="es-username" class="sputnik-search-form__label"><?= __('ESUserName','sputnik-search'); ?>:</label>
                    <input type="text" id="es-username" name="es-username" class="sputnik-search-form__input" value="<?= $es_username ? $es_username : false; ?>">
                </div>
                <div class="sputnik-search-form__row">
                    <label for="es-password" class="sputnik-search-form__label"><?= __('ESPassword','sputnik-search'); ?>:</label>
                    <input type="text" id="es-password" name="es-password" class="sputnik-search-form__input" value="<?= $es_password ? $es_password : false; ?>">
                </div>
                <div class="sputnik-search-form__row">
                    <h3 class="sputnik-search-form__choose-title"><?= __('Wybierz opcje wyświetlania','sputnik-search'); ?>:</h3>
                    <div class="sputnik-search-form__radio-buttons">
                        <label for="react" class="sputnik-search-form__label"><?= __('React','sputnik-search'); ?>:</label>
                        <input type="radio" id="react" name="display-version" class="sputnik-search-form__radio" value="react" <?= $displayVersion == 'react' ? 'checked' : false; ?><?= $displayVersion ? false : 'checked'; ?>>
                        <label for="php" class="sputnik-search-form__label"><?= __('PHP','sputnik-search'); ?>:</label>
                        <input type="radio" id="php" name="display-version" class="sputnik-search-form__radio" value="php" <?= $displayVersion == 'php' ? 'checked' : false; ?>>
                    </div>
                </div>
                <div class="sputnik-search-form__row">
                    <h3 class="sputnik-search-form__choose-title"><?= __('Wybierz style wyświetlania','sputnik-search'); ?>:</h3>
                    <div class="sputnik-search-form__radio-buttons">
                        <label for="plugin-styles" class="sputnik-search-form__label"><?= __('Style Wtyczki','sputnik-search'); ?>:</label>
                        <input type="radio" id="plugin-styles" name="styles-option" class="sputnik-search-form__radio" value="plugin-styles" <?= $stylesOption == 'plugin-styles' ? 'checked' : false; ?><?= $stylesOption ? false : 'checked'; ?>>
                        <label for="theme-styles" class="sputnik-search-form__label"><?= __('Brak styli','sputnik-search'); ?>:</label>
                        <input type="radio" id="theme-styles" name="styles-option" class="sputnik-search-form__radio" value="theme-styles" <?= $stylesOption == 'theme-styles' ? 'checked' : false; ?>>
                    </div>
                </div>
                <div class="sputnik-search-form__row">
                    <h3 class="sputnik-search-form__choose-title"><?= __('Wybierz wersje wyszukiwarki','sputnik-search'); ?>:</h3>
                    <div class="sputnik-search-form__radio-buttons">
                        <label for="simple-search" class="sputnik-search-form__label"><?= __('Wersja Prosta','sputnik-search'); ?>:</label>
                        <input type="radio" id="simple-search" name="search-version" class="sputnik-search-form__radio" value="simple-search" <?= $searchVersion == 'simple-search' ? 'checked' : false; ?>>
                        <label for="expanded-search" class="sputnik-search-form__label"><?= __('Wersja z parametrami','sputnik-search'); ?>:</label>
                        <input type="radio" id="expanded-search" name="search-version" class="sputnik-search-form__radio" value="expanded-search" <?= $searchVersion == 'expanded-search' ? 'checked' : false; ?> <?= $searchVersion ? false : 'checked'; ?>>
                    </div>
                </div>
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

            <div class="sputnik-action-buttons">
                <button class="btn btn--medium btn--primary sputnik-action-buttons__button" title="Synchronizuj Wpisy" id="js-synchronize"><?= __('Synchronizuj Wpisy','sputnik-search'); ?></button>
                <button class="btn btn--medium btn--primary sputnik-action-buttons__button" title="Synchronizuj Pliki" id="js-synchronize-files"><?= __('Synchronizuj Pliki','sputnik-search'); ?></button>
                <button class="btn btn--medium btn--danger sputnik-action-buttons__button" title="Usuń indeks" id="js-deleteindex"><?= __('Usuń indeks <small>Usunięcie indeksu spowoduje błąd w wyszukiwarce</small>','sputnik-search'); ?></button>
            </div>

            <ul id="es-logs"></ul>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // const myCodeMirror = CodeMirror.fromTextArea(document.getElementById('custom-css'));
        const textarea = document.getElementById('custom-css');

        const myCodeMirror = CodeMirror.fromTextArea(textarea);
    });
    (function($) {
        $(document).ready(function($) {
            // Synchronize index
            $('#js-synchronize').click(function(){
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

                    if(response != 'nothing') {
                        sendPostToES(++index);
                    } else {
                        const $reloadButton = $('<button/>', {
                            text: 'Odśwież stronę',
                            class: 'btn btn--medium btn--primary',
                            click: function() { window.location.reload() }
                        });

                        $('#es-logs').addClass('synchronize-complete').after($reloadButton).after('<p>Wszystkie wpisy zostały zsynchronizowane.</p>');
                        return
                    }
                }).fail(function() {
                    $('#es-logs').append("<li style='color: red;'>Nie dodano wpisu " + index + "!</li>");

                    sendPostToES(++index);
                });
            }

            // Synchronize files
            $('#js-synchronize-files').click(function(){
                var index = 3905;

                sendFilesToEs(index);
            });

            function sendFilesToEs(index) {
                var data = {
                    'action': 'index_attachment_in_es',
                    id: index
                };

                jQuery.post(ajaxurl, data, function(response) {
                    $('#es-logs').append("<li style='color: green;'>Dodano załącznik " + index + "!</li>");
                    console.log(response)
                    sendFilesToEs(++index);
                }).fail(function() {
                    $('#es-logs').append("<li style='color: red;'>Nie dodano załącznika " + index + "!</li>");
                    console.log(response)

                    sendFilesToEs(++index);
                });
            }

            // Delete index
            $('#js-deleteindex').click(function(){
                deleteIndexFromES();
            });

            function deleteIndexFromES() {
                var data = {
                    'action': 'deleteindex',
                };

                jQuery.post(ajaxurl, data, function(response) {
                    // $('#es-logs').append("<li style='color: red;'>Usunięto Indeks!</li>");
                }).fail(function() {
                    $('#es-logs').append("<li style='color: red;'>Nie powiodło się usuwanie indeksu!</li>");
                }).done(function() {
                    window.alert('Poprawnie usunięto indeks, strona zostanie przeładowana w celu utworzenia nowego indeksu.');

                    window.location.reload();
                });
            }
        });
    })(jQuery);
</script>