<?php

if ( ! class_exists( 'WP_Json_Exporter_API' ) ) {
	class WP_Json_Exporter_API {
		private string $namespace = 'wp-json-exporter/v1';
		private int $posts_per_page = 6;
		private string $redirect_url;
		private string $wordpress_url;

		function __construct() {
			$this->redirect_url  = get_option( 'wp_json_exporter_redirect_url' );
			$this->wordpress_url = get_site_url();
			add_action( 'rest_api_init', array( $this, 'register_api' ) );
		}

		function register_api(): void {
			$this->register_route( '/posts', 'get_posts' );
			$this->register_route( '/posts/(?P<slug>[a-zA-Z0-9-]+)', 'get_post' );
			$this->register_route( '/projects', 'get_projects' );
			$this->register_route( '/projects/(?P<slug>[a-zA-Z0-9-]+)', 'get_project' );
		}

		function get_posts( $request ): array {
			$page = $request->get_param( 'page' ) ? (int) $request->get_param( 'page' ) : 1;

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
				$data[] = $this->get_post_data( $post, 'post', false );
			}

			$pagination_data = $this->get_pagination_data( 'post', $page );

			return [
				'data' => $data,
				'meta' => [
					'current_page' => $pagination_data['current_page'],
					'total_pages'  => $pagination_data['total_pages'],
					'total_posts'  => $pagination_data['total_posts'],
				]
			];
		}

		function get_post( $request ): array|WP_Error {
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

			$previous_post      = $this->get_adjacent_post_custom( $post->post_date, 'post', 'prev' );
			$previous_post_data = null;
			if ( $previous_post ) {
				$previous_post_data = $this->get_post_data( $previous_post, 'post', false );
			}

			$next_post      = $this->get_adjacent_post_custom( $post->post_date, 'post', 'next' );
			$next_post_data = null;
			if ( $next_post ) {
				$next_post_data = $this->get_post_data( $next_post, 'post', false );
			}

			return array(
				'data' => $this->get_post_data( $post ),
				'prev' => $previous_post_data,
				'next' => $next_post_data,
			);
		}

		function get_projects( $request ): array {
			$page = $request->get_param( 'page' ) ? (int) $request->get_param( 'page' ) : 1;

			$args = array(
				'post_type'           => 'project',
				'posts_per_page'      => $this->posts_per_page,
				'ignore_sticky_posts' => true,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'paged'               => $page,
			);

			$query = new WP_Query( $args );
			$data  = [];

			foreach ( $query->posts as $post ) {
				$data[] = $this->get_post_data( $post, 'project', false );
			}

			$pagination_data = $this->get_pagination_data( 'project', $page );

			return [
				'data' => $data,
				'meta' => array(
					'current_page' => $pagination_data['current_page'],
					'total_pages'  => $pagination_data['total_pages'],
					'total_posts'  => $pagination_data['total_posts'],
				)
			];
		}

		function get_project( $request ): array|WP_Error {
			$slug = $request['slug'];

			$args = array(
				'name'        => $slug,
				'post_type'   => 'project',
				'post_status' => 'publish',
				'numberposts' => 1
			);

			$posts = get_posts( $args );

			if ( empty( $posts ) ) {
				return new WP_Error( 'no_projects', __( 'Project not found' ), array( 'status' => 404 ) );
			}

			$post = $posts[0];

			$next_post      = $this->get_adjacent_post_custom( $post->post_date, 'project', 'next' );
			$next_post_data = null;
			if ( $next_post ) {
				$next_post_data = $this->get_post_data( $next_post, 'project', false );
			}

			return array(
				'data' => $this->get_post_data( $post, 'project' ),
				'next' => $next_post_data,
			);
		}

		private function register_route( $route, $callback ): void {
			register_rest_route( $this->namespace, $route, array(
				'methods'             => 'GET',
				'callback'            => array( $this, $callback ),
				'permission_callback' => '__return_true',
			) );
		}

		private function get_post_data( $post, $type = 'post', $show_detail = true ): array {
			$categories    = [];
			$category_list = '';

			if ( $type === 'post' ) {
				$category_list = get_the_category_list( ', ', '', $post->ID );
			} else if ( $type === 'project' ) {
				$terms = get_the_terms( $post->ID, 'project_category' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$term_link = get_term_link( $term );
						if ( ! is_wp_error( $term_link ) ) {
							$categories[] = '<a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a>';
						}
					}
				}
				$category_list = implode( ', ', $categories );
			}

			$custom_category_list = str_replace( $this->wordpress_url, $this->redirect_url, $category_list );

			if ( $show_detail ) {
				return [
					'title'          => $post->post_title,
					'slug'           => $post->post_name,
					'featured_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
					'category'       => $custom_category_list,
					'date'           => get_the_date( 'd M / Y', $post->ID ),
					'content'        => apply_filters( 'the_content', $post->post_content ),
					'excerpt'        => get_the_excerpt( $post->ID ),
					'tags'           => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
				];
			} else {
				return [
					'title'          => $post->post_title,
					'slug'           => $post->post_name,
					'featured_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
					'category'       => $custom_category_list,
					'date'           => get_the_date( 'd M / Y', $post->ID ),
				];
			}
		}

		private function get_pagination_data( $post_type, $current_page = 1 ): array {
			$count_args = array(
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
				'fields'         => 'ids', // optimize query for performance
			);

			$count_query  = new WP_Query( $count_args );
			$total_posts  = $count_query->found_posts;
			$total_pages  = ceil( $total_posts / $this->posts_per_page );
			$current_page = max( 1, min( $current_page, $total_pages ) );

			return array(
				'current_page' => $current_page,
				'total_pages'  => $total_pages,
				'total_posts'  => $total_posts,
			);
		}

		private function get_adjacent_post_custom( $current_post_date, $post_type, $op ): ?WP_Post {
			$args = array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'numberposts' => 1,
				'orderby'     => 'date',
				'order'       => $op == 'prev' ? 'DESC' : 'ASC',
				'date_query'  => array(
					array(
						$op == 'prev' ? 'before' : 'after' => $current_post_date,
						'inclusive'                        => false
					)
				)
			);

			$adjacent_posts = get_posts( $args );

			return ! empty( $adjacent_posts ) ? $adjacent_posts[0] : null;
		}
	}
}