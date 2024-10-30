<?php
/**
 * Created by ra.
 * Date: 7/15/2016
 *
 *
 All pages
	- author archives
		- current author (by author ID)
	- category archive
		- current category archive (by cat ID)
	- posttype archive (by posttype)
	- tag
		- current tag archive (by tag ID)

	- single-post (all)
		- current single-post (by ID)
	- page (all)
		- current page (by ID)
	home / sau current page ? :-/
	404
	search
 */

class td_live_css_controller {
	static function detect_css_parts() {
		$buffy = array();

		// global
		$buffy []= array (
			'detect_key' => 'global',
			'name' => 'Global',
			'title' => 'This CSS or LESS will be added to every page of the site'
		);


		// singular ALL
		if (is_singular()) {
			$buffy []= array (
				'detect_key' => 'is_singular',
				'name' => 'All posts',
				'title' => 'Add css to all the posts and pages. (This also works on all single Custom Post Types pages)'
			);
			$buffy []= array (
				'detect_key' => 'is_single_id_' . get_the_ID(),
				'name' => 'This post',
				'title' => 'Add CSS or LESS to this page only. The page id is: ' . get_the_ID()
			);

			return $buffy;
		}



		// by author
		if (is_author()) {
			$buffy []= array (
				'detect_key' => 'is_author',
				'name' => 'All authors',
				'title' => 'This will be added to all the author archives'
			);


			$part_cur_auth_obj = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			if (isset($part_cur_auth_obj->ID)) {
				$buffy []= array (
					'detect_key' => 'is_author_id_' . $part_cur_auth_obj->ID,
					'name' => 'This author',
					'title' => 'Add CSS OR LESS to this author only'
				);
			}

			return $buffy;
		}



		if (is_category()) {

			$buffy []= array (
				'detect_key' => 'is_category',
				'name' => 'All categories',
				'title' => 'Add CSS OR LESS to all the Categories'
			);


			$current_category_id = get_query_var('cat');
			if (!empty($current_category_id)) {
				$buffy []= array (
					'detect_key' => 'is_category_id_' . $current_category_id,
					'name' => 'This category',
					'title' => 'Add CSS OR LESS to this specific category. Category id: ' . $current_category_id
				);
			}

			return $buffy;
		}


		if (is_tag()) {
			$buffy []= array (
				'detect_key' => 'is_tag',
				'name' => 'All tags',
				'title' => 'Add CSS OR LESS to all the tag archives'
			);

			return $buffy;
		}




		if (is_search()) {
			$buffy []= array (
				'detect_key' => 'is_search',
				'name' => 'Search page',
				'title' => 'Add CSS OR LESS to all the search pages'
			);
			return $buffy;
		}


		if (is_404()) {
			$buffy []= array (
				'detect_key' => 'is_404',
				'name' => '404 page',
				'title' => 'Add CSS OR LESS to all 404 pages'
			);
			return $buffy;
		}



		return $buffy;
	}
}