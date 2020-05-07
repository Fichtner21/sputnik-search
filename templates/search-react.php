<?php get_header(); ?>
	<div class="container content-section">
		<div class="main-content">
			<div class="left-side articles" id="content">
				<h2 class="page-title" id="search-title"><?php printf(__( 'Wyniki wyszukiwania dla: %s', 'sputnik-search' ), get_search_query()); ?></h2>

				<?php
                    $q = $_GET['sq'];
					$blog_id = get_current_blog_id();
					$search_mode = $_GET['search-mode'];
					$case_sensitive = $_GET['case_sensitive'];
					$category = $_GET['category'];							
					$from = $_GET['from'];
					$size = $_GET['size'];
					$date_from = $_GET['date_from'];
					$date_to = $_GET['date_to'];
					$sort = $_GET['sort'];
				?>

				<div id="search-results" class="search-list"></div>

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
                            window.configES.apiURL = 'http://35.158.146.123:9005';
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