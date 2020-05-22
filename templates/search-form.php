<?php $unique_id = esc_attr(uniqid('sputnik-search-form__')); ?>

<form role="search" method="GET" id="sputnik-search-form" class="sputnik-search-form" action="<?= esc_url(home_url('/')); ?>">
    <div class="sputnik-search-form__container">
        <label class="sputnik-search-form__title" for="<?= $unique_id; ?>"><?= __('Przeszukaj portal', 'sputnik-search') . ' ' . get_bloginfo(); ?></label>

        <div class="sputnik-search-form__wrapper">
            <div class="sputnik-search-form__row">
                <input type="text" id="s" class="sputnik-search-form__searchfield" tabindex="<?= is_search() ? '0': '-1'; ?>" placeholder="<?=__('Szukaj...', 'sputnik-search'); ?>" value="<?= isset($_GET['sq']) ? $_GET['sq'] : false; ?>" name="sq" required />
            </div>
            <div class="sputnik-search-form__parametrs">
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

                <div class="sputnik-search-form__row sputnik-search-form__row--searchmode">
                    <label for="search-mode"><?= __('Tryb wyszukiwania','sputnik-search'); ?></label>
                    <select id="search-mode" name="search-mode" title="<?= __('Tryb wyszukiwania','sputnik-search'); ?>">
                        <option value="" selected disabled><?= __('Tryb wyszukiwania','sputnik-search'); ?></option>
                        <option value="or"<?= "or" == $_GET['search-mode'] ? ' selected="selected"' : ''; ?>>
                        <?= __('Szukanie dowolnego słowa', 'sputnik-search'); ?></option>
                        <option value="and"<?= "and" == $_GET['search-mode'] ? ' selected="selected"' : ''; ?>><?= __('Szukanie wszystkich słów', 'sputnik-search'); ?></option>
                        <option value="phrase"<?= "phrase" == $_GET['search-mode'] ? ' selected="selected"' : ''; ?>><?= __('Szukanie dokładnej frazy', 'sputnik-search'); ?></option>
                    </select>
                </div>

                <div class="sputnik-search-form__row sputnik-search-form__row--category">
                    <label for="category-select"><?= __('Wybierz kategorie
                    ','sputnik-search'); ?></label>
                    <select id="category-select" name="category" title="<?= __('Wybierz kategorie','sputnik-search'); ?>">
                        <option value=""><?= __('Wybierz kategorie','sputnik-search'); ?></option>
                        <?php
                            $categories = get_categories();
                            foreach($categories as $category):
                        ?>
                            <option value="<?= $category->term_id; ?>"<?= $category->term_id == $_GET['category'] ? ' selected="selected"' : ''; ?>><?= $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sputnik-search-form__row sputnik-search-form__row--cs">
                    <label for="case_sensitive"><?= __('Uzględnij wielkość liter','sputnik-search'); ?></label>
                    <input type="checkbox" id="case_sensitive" name="case_sensitivity" title="<?= __('Uwzględnij wielkość liter', 'sputnik-search'); ?>" value="case" <?= $_GET['case_sensitivity'] == "case" ? ' checked' : ''; ?>>
                </div>
            </div>

            <div class="sputnik-search-form__row sputnik-search-form__row__submit">
                <button type="submit" id="search-submit" class="sputnik-search-form__submit" tabindex="<?= is_search() ? '0': '-1'; ?>" title="<?= __('Wyszukaj', 'sputnik-search'); ?>"><?= __('Wyszukaj', 'sputnik-search'); ?></button>
            </div>
        </div>
    </div>
</form>