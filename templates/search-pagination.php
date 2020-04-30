<?php
	$posts_per_page = isset($posts_per_page) ? $posts_per_page : 8;

	$all_pages = ceil($count / $posts_per_page);

	$page_number = isset($_GET['from']) ? $_GET['from'] : 0;

	$current_page = $page_number;
	$current_page = ($count > 0) ? min($all_pages, $current_page) : 1;							

	$offset = $current_page * $posts_per_page;

	$query_data = array_slice($posts, $offset, $posts_per_page); 
?>

<?php if($all_pages > 0) : $query_data = $_GET; ?>
<div class="pagination">
	<?php if(! $page_number == 0 ) : 
		$query_data['from'] = $page_number - 1;
	?>
		<a class="prev page-numbers" href="?<?= http_build_query($query_data); ?>" title="Poprzednia strona">&lt; Poprzednia strona</a>
	<?php endif; ?>

	<?php for( $i = 0; $i < $all_pages; $i++ ) : ?>
		<?php if($page_number == $i) : ?>
			<span class="page-numbers current"><?= $i + 1; ?></span>
		<?php else : $query_data['from'] = $i; ?>
			<a href="?<?= http_build_query($query_data); ?>" class="page-numbers"><?= $i + 1; ?></a>
		<?php endif; ?>
	<?php endfor; ?>

	<?php if( $page_number < $all_pages - 1 ) : $query_data['from'] = $page_number + 1; ?>	
		<a class="next page-numbers" href="?<?= http_build_query($query_data); ?>" title="Następna strona">Następna strona &gt;</a>
	<?php endif; ?>
</div>
<?php endif; ?>