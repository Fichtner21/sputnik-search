<?php
    $args = array(
        'post_type' => get_post_type(),
        'posts_per_page' => -1,
    );

	$posts_per_page = isset($posts_per_page) ? $posts_per_page : 8;
	$all_post_query = new WP_Query($args);
	$all_pages = ceil(count($all_post_query->posts) / $posts_per_page);
	$page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 0;
?>
<?php if($all_pages > 1): ?>
	<?php $data = $_GET; ?>
	<div class="pagination">
		<?php if($page_number > 0): ?>
			<?php $data["page_number"] = $page_number - 1; ?>
			<a class="prev page-numbers" href="?<?php echo http_build_query($data); ?>" title="poprzednia strona">&lt; POPRZEDNIA STRONA</a>
		<?php endif; ?>
		<?php for($i = 0; $i < $all_pages; $i++): ?>
			<?php if(abs($i - $page_number) <= 2 || $i == 0 || $i == $all_pages - 1): ?>
				<?php if($page_number == $i): ?>
					<span class="page-numbers current"><?php echo $i + 1; ?></span>
				<?php else: ?>
					<?php $data["page_number"] = $i; ?>
					<a class="page-numbers" href="?<?php echo http_build_query($data); ?>" title="<?php echo ($i + 1) . ' strona'; ?>"><?php echo $i + 1; ?></a>
				<?php endif; ?>
			<?php endif; ?>
			<?php if($i == 0 && $page_number >= 4): ?>
				<span class="page-numbers dots"><?php echo '...'; ?></span>
			<?php endif; ?>
			<?php if($i == $all_pages - 2 && $page_number <= $all_pages - 5): ?>
				<span class="page-numbers dots"><?php echo '...'; ?></span>
			<?php endif; ?>
		<?php endfor; ?>

		<?php if($page_number < $all_pages - 1): ?>
			<?php $data["page_number"] = $page_number + 1; ?>
			<a class="next page-numbers" href="?<?php echo http_build_query($data); ?>" title="następna strona">NASTĘPNA STRONA &gt;</a>
		<?php endif; ?>
	</div>
<?php endif; ?>
