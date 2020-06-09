<?php get_header(); ?>
	<div class="ss-container">
		<div class="ss-content">
			<div class="ss-articles" id="content">
				<h2 class="ss-articles__title" id="search-title"><?php printf(__( 'Wyniki wyszukiwania dla: %s', 'sputnik-search' ), get_search_query()); ?></h2>

				<?php
                    $q = isset($_GET['sq']) ? $_GET['sq'] : false;
					$blog_id = get_current_blog_id();
					$search_mode = isset($_GET['search-mode']) ? $_GET['search-mode'] : false;
					$case_sensitive = isset($_GET['case_sensitive']) ? $_GET['case_sensitive'] : false;
					$category = isset($_GET['category']) ? $_GET['category'] : false;
					$from = isset($_GET['from']) ? $_GET['from'] : false;
					$size = isset($_GET['size']) ? $_GET['size'] : false;
					$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : false;
                    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : false;
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : false;
				?>

				<div id="search-results" class="ss-articles-list"></div>

                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            var q = '<?= $q; ?>';
                            var from = '<?= $from; ?>' || 0;
                            var size = '<?= $size; ?>' || 10;
                            var search_mode = '<?= $search_mode; ?>' || '';
                            var case_sensitive = '<?= $case_sensitive; ?>' || '';
                            var sort = '<?= $sort ? $sort : ""; ?>' || '';
                            var category = <?= $category ? $category : 0; ?> || '';

                            var date_from = '<?= $date_from; ?>' || '';
                            var date_to = '<?= $date_to; ?>' || '';

                            window.configES.blogID = <?= $blog_id; ?> || 1;
                            window.configES.apiURL = 'https://elasticsearch.sputnik.pl';
                            window.configES.user = '<?= get_option('es_username'); ?>';
                            window.configES.facebook.iconUrl = '';

                            window.InitSputnikWordpressSearch("search-results", q, size, from, search_mode, case_sensitive, category, date_from, date_to, sort);

                            window.configES.onSearch = function (q) {
                                $('#search-title').text('Wyniki wyszukiwania dla zapytania „' + q + '”');
                            }
                        });
                    })(jQuery);
				</script>
			</div>
		</div>
	</div>

<?php get_footer(); ?>