<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;

class SputnikSearch extends BaseController {
	protected $search_query;
	protected $paged;
	protected $number_of_posts;
	protected $post_types;
	protected $count;
	protected $posts;
	protected $all_posts;

	private $all_words;

	public function __construct($post_types=array('post', 'page'), $search_query='', $paged=0, $number_of_posts=10) {
		parent::__construct();

		$this->search_query = $search_query;
		$this->paged = $paged;
		$this->number_of_posts = $number_of_posts;

		$get_post_types = get_post_types(array( 'public' => true ));
		$post_types = array();

		foreach($get_post_types as $post_type) {
			array_push($post_types, $post_type);
		}

		$this->post_types = $post_types;
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

		$url = $this->apiURL . "search/$this->ESUserName/$this->blog_id?q=$q&from=$from&size=$posts_per_page&mode=&cs=&category=&d_from=&d_to=&sort=&no_attachments";

		$results = json_decode(file_get_contents($url));

		$this->count = $results->count;
		$this->posts = array();

		if($this->count > 0) {
			foreach($results->hits as $hit) {
				$one_post = array();
				$one_post['ID'] = $hit->_id;
				$one_post['post_type'] = get_post_type($hit->_id);
				$one_post['post_title'] = $hit->_source->title;
				$contentSearch = 'content.search';
				$contentTitle = 'content.title';
				$one_post['post_content'] = '';

				if($hit->highlight && $hit->highlight->$contentSearch) {
					$first = true;
					foreach($hit->highlight->$contentSearch as $line) {
						if($first) {
							$first = false;
							$one_post['post_content'] .= $line;
						} else {
							$one_post['post_content'] .= ' ... ' . $line;
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