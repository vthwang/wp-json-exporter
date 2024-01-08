<?php

if ( ! class_exists( 'WP_Json_Exporter_API' ) ) {
	class WP_Json_Exporter_API {
		private string $namespace = 'wp-json-exporter/v1';
		private int $posts_per_page = 6;
		private string $redirect_url = '';
		private string $wordpress_url = '';

		function __construct() {
			$this->redirect_url  = get_option( 'wp_json_exporter_redirect_url' );
			$this->wordpress_url = get_site_url();
			add_action( 'rest_api_init', array( $this, 'register_api' ) );
		}

		function register_api(): void {
			register_rest_route( $this->namespace, '/posts', array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_posts' ),
				'permission_callback' => '__return_true',
			) );

			register_rest_route( $this->namespace, '/posts/(?P<slug>[a-zA-Z0-9-]+)', array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_post' ),
				'permission_callback' => '__return_true',
			) );
		}

		function get_posts( $request ): array {
			$page = $request->get_param( 'page' ) ? (int) $request->get_param( 'page' ) : 1;

			$count_args = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
			);

			$count_query = new WP_Query( $count_args );
			$total_posts = $count_query->found_posts;
			$total_pages = ceil( $total_posts / $this->posts_per_page );

			$args = array(
				'post_type'           => 'post',
				'posts_per_page'      => $this->posts_per_page,
				'ignore_sticky_posts' => true,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'paged'               => $page,
			);

			$query = new WP_Query( $args );
			$data  = [];

			foreach ( $query->posts as $post ) {
				$data[] = [
					'title'          => $post->post_title,
					'featured_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
					'category'       => str_replace( $this->wordpress_url, $this->redirect_url, get_the_category_list( ', ', '', $post->ID ) ),
					'date'           => get_the_date( 'd M / Y', $post->ID ),
				];
			}

			return [
				'data' => $data,
				'meta' => [
					'current_page' => $page,
					'total_pages'  => $total_pages,
				]
			];
		}

		function get_post( $request ) {
			$slug = $request['slug'];

			$args = array(
				'name'        => $slug,
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => 1
			);

			$posts = get_posts( $args );

			if ( empty( $posts ) ) {
				return new WP_Error( 'no_posts', __( 'Post not found' ), array( 'status' => 404 ) );
			}

			$post = $posts[0];

			return array(
				'title'         => $post->post_title,
				'post_date'     => get_the_date( 'd M / Y', $post->ID ),
				'updated_date'  => get_the_modified_date( 'd M / Y', $post->ID ),
				'feature_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
				'content'       => apply_filters( 'the_content', $post->post_content ),
				'excerpt'       => get_the_excerpt( $post->ID ),
				'tags'          => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
				'categories'    => wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) )
			);
		}
	}
}