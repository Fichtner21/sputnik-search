<?php


class SputnikBetterSearch {
	protected $search_query = '';
	protected $paged = '';
	protected $number_of_posts = 0;
	protected $post_types = array();
	protected $count = 0;
	protected $posts = array();

	private $all_words = array();

	public function __construct($post_types, $search_query, $paged, $number_of_posts) {
		$this->search_query = $search_query ? $search_query : $_GET['sq'];
		$this->paged = $paged ? $paged : get_query_var('paged');
		$this->number_of_posts = $number_of_posts ? $number_of_posts : 10;
		$this->post_types = $post_types ? $post_types : array('post', 'page');
	}

	public function get_search_query() {
		return $this->search_query;
	}

	public function get_paged() {
		return $this->paged;
	}

	public function get_number_of_posts() {
		return $this->number_of_posts;
	}

	public function content_to_mark($content, $length = 340) {
		$search_query = $this->search_query;		

		$content = strip_shortcodes($content);		

		$_content = str_replace($search_query, "<mark>" . $search_query . "</mark>", $content);	


		preg_match("/[^\<\>]{0,25}(?:<mark>[^\<\>]*<\/mark>[^\<\>]{0,25})+/", $_content, $found);		

		$to_return = "";

		foreach($found as $index => $line) {
			$to_return .= ($index == 0 ? "..." : "") . $line . "... ";
		}

		if($to_return) {
			return $to_return;
		}

		return $length == 0 ? $content : mb_substr($content, 0, $length);
	}

	public function get_results() {
		$posts_per_page = $this->number_of_posts;
		$from = $this->paged * $posts_per_page;
		$q = urlencode($this->search_query);
		$cur_blog_id = get_current_blog_id();

		$url = "http://35.158.146.123:9005/api/search/luban/$cur_blog_id?q=$q&from=$from&size=$posts_per_page&mode=&cs=&category=&d_from=&d_to=&sort=&no_attachments";
		
		$results = json_decode(file_get_contents($url));

		function highlight_keywords($from_q, $post_title) {
			return preg_replace("/(\p{L}*?)(".preg_quote($from_q).")(\p{L}*)/ui", "$1<span class=\"h\"><mark>$2</mark></span>$3", $post_title);
		}

		//var_dump($results->hits[0]);

		$this->count = $results->count;
		$this->posts = array();

		if($this->count > 0) {
			foreach($results->hits as $hit) {
				$one_post = array();
				$one_post['ID'] = $hit->_id;
				$one_post['post_type'] = get_post_type($hit->_id);
				$one_post['post_title'] = $hit->_source->title;
				$conentSeearch = 'content.search';	
				$contentTitle = 'content.title';			
				$one_post['post_content'] = '';
				//var_dump($hit->highlight);

				// if($hit->highlight && $hit->highlight->$contentTitle){
				// 	$first = true;
				// 	foreach($hit->highlight->contentTitle as $line){
				// 		if($first){
				// 			$one_post['post_title'] .= $line;
				// 		} else {
				// 			$one_post['post_title'] .= $line;
				// 		}
				// 	}
				// }
				
				if($hit->highlight && $hit->highlight->$conentSeearch) {
					$first = true;
					foreach($hit->highlight->$conentSeearch as $line) {						
						if($first) {
							$first = false;
							$one_post['post_content'] .= $line;
							//$one_post['post_title'] .= $line;
						} else {
							$one_post['post_content'] .= ' ... ' . $line;
							//$one_post['post_title'] .= ' ... ' . $line;
						}
					}
				}

				$this->posts[] = $one_post;
			}
		}

		return $this->posts;
	}

	public function get_count() {
		return $this->count;
	}
}
