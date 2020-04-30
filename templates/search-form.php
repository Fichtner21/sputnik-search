<?php $unique_id = esc_attr(uniqid('sputnik-search-form__')); ?>

<form role="search" method="GET" id="sputnik-search-form" class="sputnik-search-form<?= is_search() ? ' active': false; ?>" action="<?= esc_url(home_url('/')); ?>">
    <label class="sputnik-search-form__title" for="<?= $unique_id; ?>"><?= __('Przeszukaj portal', 'sputnik-search') . ' ' . get_bloginfo(); ?></label>
    
    <div class="sputnik-search-form__wrapper">
        <div class="sputnik-search-form__row">
            <input type="text" id="<?= $unique_id; ?>" class="sputnik-search-form__searchfield" tabindex="<?= is_search() ? '0': '-1'; ?>" placeholder="<?=__('Szukaj...', 'sputnik-search'); ?>" value="<?= isset($_GET['sq']) ? $_GET['sq'] : false; ?>" name="sq" required />
        </div>

        <div class="sputnik-search-form__row sputnik-search-form__row--hidden">
            <label for="s" class="hidden"></label>
            <input type="hidden" id="s" name="s" value="" />
        </div>

        <div class="sputnik-search-form__row sputnik-search-form__row--dates">
            <div class="sputnik-search-form__date-from">
                <label for="datepicker-from"><?= __('Data od:', 'sputnik-search'); ?></label>
                <input type="date" class="datepicker" name="d_from" id="datepicker-from" size="10" value="<?= isset($_GET['d_from']) ? $_GET['d_from'] : false; ?>" title="<?= __('Data od:', 'sputnik-search'); ?>" />
            </div>
            <div class="sputnik-search-form__date-to">
                <label for="datepicker-to"><?= __('Data do:', 'sputnik-search'); ?></label>
                <input type="date" class="datepicker" name="d_to" id="datepicker-to" size="10" value="<?= isset($_GET['d_to']) ? $_GET['d_to'] : false; ?>" title="<?= __('Data do:', 'sputnik-search'); ?>" />
            </div>
        </div>
        
        <div class="sputnik-search-form__row">
            <button type="submit" id="search-submit" name="search-submit" class="sputnik-search-form__submit" tabindex="<?= is_search() ? '0': '-1'; ?>" title="<?= __('Wyszukaj', 'sputnik-search'); ?>"><?= __('Wyszukaj', 'sputnik-search'); ?></button>
        </div>
    </div>
</form>