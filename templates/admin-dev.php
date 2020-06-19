<?php

if(isset($_POST) && !empty($_POST)) {
    if(isset($_POST['dev-user']) && isset($_POST['dev-pass'])) {
        if($_POST['dev-user'] === 'sputnik' && $_POST['dev-pass'] === 'oB$R*hYQaI(AKQDGbh7E7MZ&') {
            $_SESSION['is-logged'] = true;
        }
    }

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
    if(isset($_POST['visibility-version'])) {
        update_option( 'visibility_version', $_POST['visibility-version'] );
    }
}

$es_username = get_option('es_username');
$es_password = get_option('es_password');

$displayVersion = get_option('display_version');
$stylesOption = get_option( 'styles_option' );
$searchVersion = get_option( 'search_version' );
$visibilityVersion = get_option( 'visibility_version' );

?>

<div class="sputnik-search-page">
    <div class="sputnik-search-page__inner">
        <div class="sputnik-search-page__branding">
            <img src="<?= plugin_dir_url( dirname( __FILE__ ) ); ?>/assets/admin/logo-sputnik.svg" alt="">
        </div>

        <div class="sputnik-search-page__content">
            <h1 class="sputnik-search-page__title"><?= __('Sputnik Search','sputnik-search'); ?></h1>
            <p class="sputnik-search-page__text"><?= __('Zaawansowana wyszukiwarka stworzona przy użyciu ElasticSearch','sputnik-search'); ?></p>

            <?php if(! isset($_SESSION['is-logged'])) : ?>

            <form method="POST" class="sputnik-search-form" data-tab='js-user-content'>
                <h2 class="sputnik-search-form__title"><?= __('Wypełnij pola, aby można było nawiązać połączenie','sputnik-search'); ?></h2>

                <div class="sputnik-search-form__row">
                    <label for="dev-user" class="sputnik-search-form__label"><?= __('Nazwa użytkownika','sputnik-search'); ?>:</label>
                    <input type="text" id="dev-user" name="dev-user" class="sputnik-search-form__input">
                </div>

                <div class="sputnik-search-form__row">
                    <label for="dev-pass" class="sputnik-search-form__label"><?= __('Hasło','sputnik-search'); ?>:</label>
                    <input type="password" id="dev-pass" name="dev-pass" class="sputnik-search-form__input">
                </div>

                <div class="sputnik-search-form__row">
                    <button type="submit" class="btn btn--medium btn--primary sputnik-search-form__submit" title="Zaloguj się"><?= __('Zaloguj się','sputnik-search'); ?></button>
                </div>
            </form>

            <?php else : ?>

            <form method="POST" class="sputnik-search-form" data-tab='js-user-content'>
                <p><?= __('Zalogowany jako developer: <strong>sputnik</strong>','sputnik-search'); ?> <a href='<?= $_SERVER["PHP_SELF"]; ?>/admin.php?page=sputnik-developer&dev=logout'><?= __('[ Wyloguj ]','sputnik-search'); ?></a></p>
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
                    <h3 class="sputnik-search-form__choose-title"><?= __('Wybierz opcje widoczności','sputnik-search'); ?>:</h3>
                    <div class="sputnik-search-form__radio-buttons">
                        <label for="default-visibility" class="sputnik-search-form__label"><?= __('Wyszukiwarka widoczna od razu','sputnik-search'); ?>:</label>
                        <input type="radio" id="default-visibility" name="visibility-version" class="sputnik-search-form__radio" value="default-visibility" <?= $visibilityVersion == 'default-visibility' ? 'checked' : false; ?><?= $visibilityVersion ? false : 'checked'; ?>>
                        <label for="toggle-visibility" class="sputnik-search-form__label"><?= __('Wyszukiwarka widoczna po kliknięciu w lupę','sputnik-search'); ?>:</label>
                        <input type="radio" id="toggle-visibility" name="visibility-version" class="sputnik-search-form__radio" value="toggle-visibility" <?= $visibilityVersion == 'toggle-visibility' ? 'checked' : false; ?>>
                    </div>
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

            <?php
                $shortcode_string = "&lt;!-- Sputnik Search plugin search form --&gt;<br>";
                $shortcode_string .= "&lt;?= shortcode_exists( 'sputnik_search_form' ) ? do_shortcode( '[sputnik_search_form]' ) : false; ?&gt;";
            ?>
            <div class="sputnik-shortcode">
                <h3 class="sputnik-shortcode__title"><?= __('Poniższy kod odpowiada za wyświetlenie wyszukiwarki wtyczki w motywie. Wklej go np. w header.php w folderze motywu.','sputnik-search'); ?></h3>
                <pre class="sputnik-shortcode__code"><?= $shortcode_string; ?></pre>
            </div>

            <ul id="es-logs"></ul>

            <?php endif; ?>
        </div>
    </div>
</div>