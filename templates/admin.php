<div class="sputnik-search-page">
    <div class="sputnik-search-page__inner">
        <div class="sputnik-search-page__branding">
            <img src="<?= plugin_dir_url( dirname( __FILE__ ) ); ?>/assets/admin/logo-sputnik.svg" alt="">
        </div>

        <div class="sputnik-search-page__content">
            <h1 class="sputnik-search-page__title"><?= __('Sputnik Search','sputnik-search'); ?></h1>
            <p class="sputnik-search-page__text"><?= __('Zaawansowana wyszukiwarka stworzona przy użyciu ElasticSearch','sputnik-search'); ?></p>

            <div class='sputnik-search-pages'>
                <a class='btn btn--medium btn--primary' href="<?= get_home_url(); ?>/wp-admin/admin.php?page=sputnik-user" class="sputnik-search-pages__anchor" title='<?= __('Strona użytkownika','sputnik-search'); ?>'><?= __('Strona użytkownika','sputnik-search'); ?></a>
                <a class='btn btn--medium btn--primary' href="<?= get_home_url(); ?>/wp-admin/admin.php?page=sputnik-developer" class="sputnik-search-pages__anchor" title='<?= __('Strona developera','sputnik-search'); ?>'><?= __('Strona developera','sputnik-search'); ?></a>
            </div>
        </div>
    </div>
</div>