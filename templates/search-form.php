<?php if( get_option('visibility_version') === 'toggle-visibility' ) : ?>
    <button type='button' class='sputnik-search-form__toggle' id='sputnik-search-form-toggle' title='<?= __('Wyszukiwarka', 'sputnik-search'); ?>'>
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-1 0 136 136.21852" id="sputnik-search-toggle-icon" width='20'>
            <g>
            <path d="M 93.148438 80.832031 C 109.5 57.742188 104.03125 25.769531 80.941406 9.421875 C 57.851562 -6.925781 25.878906 -1.460938 9.53125 21.632812 C -6.816406 44.722656 -1.351562 76.691406 21.742188 93.039062 C 38.222656 104.707031 60.011719 105.605469 77.394531 95.339844 L 115.164062 132.882812 C 119.242188 137.175781 126.027344 137.347656 130.320312 133.269531 C 134.613281 129.195312 134.785156 122.410156 130.710938 118.117188 C 130.582031 117.980469 130.457031 117.855469 130.320312 117.726562 Z M 51.308594 84.332031 C 33.0625 84.335938 18.269531 69.554688 18.257812 51.308594 C 18.253906 33.0625 33.035156 18.269531 51.285156 18.261719 C 69.507812 18.253906 84.292969 33.011719 84.328125 51.234375 C 84.359375 69.484375 69.585938 84.300781 51.332031 84.332031 C 51.324219 84.332031 51.320312 84.332031 51.308594 84.332031 Z M 51.308594 84.332031 " style=" stroke:none;fill-rule:nonzero;fill-opacity:1;" />
            </g>
        </svg>

        <span><?= __('Wyszukiwarka', 'sputnik-search'); ?></span>
    </button>
<?php endif; ?>

<form role="search" method="GET" id="sputnik-search-form" class="sputnik-search-form <?php if( get_option('visibility_version') === 'toggle-visibility' ) : ?>sputnik-search-form--hidden<?php endif; ?>" action="<?= esc_url(home_url('/')); ?>">
    <div class="sputnik-search-form__container">
        <label class="sputnik-search-form__title" for="s"><?= __('Przeszukaj portal', 'sputnik-search') . ' ' . get_bloginfo(); ?></label>

        <div class="sputnik-search-form__wrapper">
            <div class="sputnik-search-form__row">
                <input type="text" id="s" class="sputnik-search-form__searchfield" tabindex="<?= is_search() ? '0': '-1'; ?>" placeholder="<?=__('Szukaj...', 'sputnik-search'); ?>" value="<?= isset($_GET['sq']) ? $_GET['sq'] : false; ?>" name="sq" required />
            </div>

            <div class="sputnik-search-form__parametrs" <?php if(get_option('search_version') === 'expanded-search' || get_option('search_version') === false ) : ?> style='display: grid;' <?php endif; ?>>
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
                        <option value="or"<?= isset($_GET['search-mode']) && "or" == $_GET['search-mode'] ? ' selected="selected"' : ''; ?>>
                        <?= __('Szukanie dowolnego słowa', 'sputnik-search'); ?></option>
                        <option value="and"<?= isset($_GET['search-mode']) && "and" == $_GET['search-mode'] ? ' selected="selected"' : ''; ?>><?= __('Szukanie wszystkich słów', 'sputnik-search'); ?></option>
                        <option value="phrase"<?= isset($_GET['search-mode']) && "phrase" == $_GET['search-mode'] ? ' selected="selected"' : ''; ?>><?= __('Szukanie dokładnej frazy', 'sputnik-search'); ?></option>
                    </select>
                </div>

                <div class="sputnik-search-form__row sputnik-search-form__row--category">
                    <label for="category-select"><?= __('Wybierz kategorie
                    ','sputnik-search'); ?></label>
                    <select id="category-select" name="category" title="<?= __('Wybierz kategorie','sputnik-search'); ?>">
                        <option value=""><?= __('Wybierz kategorie','sputnik-search'); ?></option>

                        <?php
                        $categories = get_option(' choosen_terms ');

                        if(isset($categories) && $categories != false) {
                            $categories_terms = explode(',', $categories);
                        }

                        if(isset($categories_terms)) :

                            foreach($categories_terms as $category_term) :
                                $term = get_term($category_term);
                                $term_name = $term->name;
                            ?>
                                <option value="<?= $category_term; ?>"<?= isset($_GET['category']) && $category_term == $_GET['category'] ? ' selected="selected"' : ''; ?>><?= $term_name; ?></option>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </select>
                </div>

                <div class="sputnik-search-form__row sputnik-search-form__row--cs">
                    <label for="case_sensitive"><?= __('Uwzględnij wielkość liter','sputnik-search'); ?></label>
                    <input type="checkbox" id="case_sensitive" name="case_sensitivity" title="<?= __('Uwzględnij wielkość liter', 'sputnik-search'); ?>" value="case" <?= isset($_GET['case_sensitivity']) && $_GET['case_sensitivity'] == "case" ? ' checked' : ''; ?>>
                </div>
            </div>

            <div class="sputnik-search-form__row sputnik-search-form__row__submit">
                <input type="hidden" name="sort" id="sort" value="date_new">
                <button type="submit" id="search-submit" class="sputnik-search-form__submit" tabindex="<?= is_search() ? '0': '-1'; ?>" title="<?= __('Wyszukaj', 'sputnik-search'); ?>"><?= __('Wyszukaj', 'sputnik-search'); ?></button>
            </div>
        </div>
    </div>
</form>