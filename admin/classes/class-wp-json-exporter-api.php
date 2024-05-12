<?php
/**
 * WP JSON Exporter API
 *
 * @package WP_JSON_Exporter
 */

if ( ! class_exists( 'WP_Json_Exporter_API' ) ) {
	/**
	 * Class WP_Json_Exporter_API
	 */
	class WP_Json_Exporter_API {
		/**
		 * Namespace
		 *
		 * @var string
		 */
		private string $namespace = 'wp-json-exporter/v1';

		/**
		 * Posts per page
		 *
		 * @var int
		 */
		private int $posts_per_page = 6;

		/**
		 * WPDB
		 *
		 * @var wpdb
		 */
		protected wpdb $wpdb;

		/**
		 * Table name
		 *
		 * @var string
		 */
		protected string $table_name;

		/**
		 * WP_Json_Exporter_API constructor.
		 */
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_api' ) );
			global $wpdb;
			$this->wpdb       = $wpdb;
			$this->table_name = $wpdb->prefix . WP_JSON_EXPORTER_VISITS_TABLE;
		}

		/**
		 * Register API
		 */
		public function register_api(): void {
			/** Get all posts */
			register_rest_route(
				$this->namespace,
				'/posts',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_posts' ),
					'permission_callback' => '__return_true',
				)
			);
			/** Get single post */
			register_rest_route(
				$this->namespace,
				'/posts/(?P<slug>[a-zA-Z0-9-]+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_post' ),
					'permission_callback' => '__return_true',
				)
			);
			/** Get all projects */
			register_rest_route(
				$this->namespace,
				'/projects',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_projects' ),
					'permission_callback' => '__return_true',
				)
			);
			/** Get single project */
			register_rest_route(
				$this->namespace,
				'/projects/(?P<slug>[a-zA-Z0-9-]+)',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_project' ),
					'permission_callback' => '__return_true',
				)
			);
			/** Get total visits */
			register_rest_route(
				$this->namespace,
				'/visits',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_visits' ),
					'permission_callback' => '__return_true',
				)
			);
			/** Update visits */
			register_rest_route(
				$this->namespace,
				'/visits',
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_visits' ),
					'permission_callback' => '__return_true',
				)
			);
		}

		/**
		 * Get all posts
		 *
		 * @param WP_REST_Request $request Request.
		 *
		 * @return WP_REST_Response
		 */
		public function get_posts( WP_REST_Request $request ): WP_REST_Response {
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
			$data  = array();

			foreach ( $query->posts as $post ) {
				$data[] = $this->get_post_data( $post, 'post', false );
			}

			$pagination_data = $this->get_pagination_data( 'post', $page );
			$response        = array(
				'data' => $data,
				'meta' => array(
					'current_page' => $pagination_data['current_page'],
					'total_pages'  => $pagination_data['total_pages'],
					'total_posts'  => $pagination_data['total_posts'],
				),
			);

			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Get single post
		 *
		 * @param WP_REST_Request $request Request.
		 *
		 * @return WP_REST_Response|WP_Error
		 */
		public function get_post( WP_REST_Request $request ): WP_REST_Response|WP_Error {
			$slug = $request['slug'];

			$args = array(
				'name'        => $slug,
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => 1,
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

			/** Get Visits Data */
			$route       = '/posts/' . $slug;
			$visit_count = $this->wpdb->get_var(
				$this->wpdb->prepare( "SELECT count FROM $this->table_name WHERE route = %s", $route ) // phpcs:ignore
			);
			$visit_count = $visit_count ? (int) $visit_count : 0;

			$data           = $this->get_post_data( $post );
			$data['visits'] = $visit_count;

			$response = array(
				'data' => $data,
				'prev' => $previous_post_data,
				'next' => $next_post_data,
			);

			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Get all projects
		 *
		 * @param WP_REST_Request $request Request.
		 *
		 * @return WP_REST_Response
		 */
		public function get_projects( WP_REST_Request $request ): WP_REST_Response {
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
			$data  = array();

			foreach ( $query->posts as $post ) {
				$post_data = $this->get_post_data( $post, 'project', false );
				$post_data = $this->get_post_meta( $post, $post_data );
				$data[]    = $post_data;
			}

			$pagination_data = $this->get_pagination_data( 'project', $page );
			$response        = array(
				'data' => $data,
				'meta' => array(
					'current_page' => $pagination_data['current_page'],
					'total_pages'  => $pagination_data['total_pages'],
					'total_posts'  => $pagination_data['total_posts'],
				),
			);

			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Get single project
		 *
		 * @param WP_REST_Request $request Request.
		 *
		 * @return WP_REST_Response|WP_Error
		 */
		public function get_project( WP_REST_Request $request ): WP_REST_Response|WP_Error {
			$slug = $request['slug'];

			$args = array(
				'name'        => $slug,
				'post_type'   => 'project',
				'post_status' => 'publish',
				'numberposts' => 1,
			);

			$posts = get_posts( $args );

			if ( empty( $posts ) ) {
				return new WP_Error( 'no_projects', __( 'Project not found' ), array( 'status' => 404 ) );
			}

			$post = $posts[0];
			$data = $this->get_post_data( $post, 'project' );

			/** Get Post meta */
			$data = $this->get_post_meta( $post, $data );

			$next_post      = $this->get_adjacent_post_custom( $post->post_date, 'project', 'next' );
			$next_post_data = null;
			if ( $next_post ) {
				$next_post_data = $this->get_post_data( $next_post, 'project', false );
			}

			/** Get Visits Data */
			$route          = '/projects/' . $slug;
			$visit_count    = $this->wpdb->get_var(
				$this->wpdb->prepare( "SELECT count FROM $this->table_name WHERE route = %s", $route ) // phpcs:ignore
			);
			$visit_count    = $visit_count ? (int) $visit_count : 0;
			$data['visits'] = $visit_count;

			$response = array(
				'data' => $data,
				'next' => $next_post_data,
			);

			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Get post data
		 *
		 * @param WP_Post $post Post.
		 * @param string $type Post type.
		 * @param bool $show_detail Show detail.
		 *
		 * @return array
		 */
		private function get_post_data( WP_Post $post, string $type = 'post', bool $show_detail = true ): array {
			$categories = array();

			if ( 'post' === $type ) {
				$category_list = get_the_category( $post->ID );
				foreach ( $category_list as $category ) {
					$categories[] = $category->name;
				}
			} elseif ( 'project' === $type ) {
				$terms = get_the_terms( $post->ID, 'project_category' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$term_link = get_term_link( $term );
						if ( ! is_wp_error( $term_link ) ) {
							$categories[] = $term->name;
						}
					}
				}
			}

			if ( $show_detail ) {
				return array(
					'title'          => $post->post_title,
					'slug'           => $post->post_name,
					'featured_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
					'category'       => $categories,
					'date'           => get_the_date( 'Y-m-d', $post->ID ),
					'last_modified'  => get_the_modified_date( 'Y-m-d', $post->ID ),
					'content'        => apply_filters( 'the_content', $post->post_content ),
					'excerpt'        => get_the_excerpt( $post->ID ),
					'tags'           => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
				);
			} else {
				return array(
					'title'          => $post->post_title,
					'slug'           => $post->post_name,
					'featured_image' => get_the_post_thumbnail_url( $post->ID, 'full' ),
					'category'       => $categories,
					'date'           => get_the_date( 'Y-m-d', $post->ID ),
				);
			}
		}

		/**
		 * Get pagination data
		 *
		 * @param string $post_type Post type.
		 * @param int $current_page Current page.
		 *
		 * @return array
		 */
		private function get_pagination_data( string $post_type, int $current_page = 1 ): array {
			$count_args = array(
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				/** Optimize query for performance */
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

		/**
		 * Get post meta
		 *
		 * @param WP_Post $post Post.
		 * @param array $data Data.
		 *
		 * @return array
		 */
		private function get_post_meta( WP_Post $post, array $data ): array {
			$specific_meta_keys = array( 'color', 'product_owner', 'website', 'tech_stack', 'my_role' );
			foreach ( $specific_meta_keys as $key ) {
				$value = get_post_meta( $post->ID, $key, true );
				if ( $value ) {
					$data['meta'][ $key ] = $value;
				}
			}

			return $data;
		}

		/**
		 * Get adjacent post custom
		 *
		 * @param string $current_post_date Current post date.
		 * @param string $post_type Post type.
		 * @param string $op Operation.
		 *
		 * @return WP_Post|null
		 */
		private function get_adjacent_post_custom( string $current_post_date, string $post_type, string $op ): ?WP_Post {
			$args = array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'numberposts' => 1,
				'orderby'     => 'date',
				'order'       => 'prev' === $op ? 'DESC' : 'ASC',
				'date_query'  => array(
					array(
						'prev' === $op ? 'before' : 'after' => $current_post_date,
						'inclusive' => false,
					),
				),
			);

			$adjacent_posts = get_posts( $args );

			return ! empty( $adjacent_posts ) ? $adjacent_posts[0] : null;
		}

		/**
		 * Get total visits
		 *
		 * @return WP_REST_Response
		 */
		public function get_visits(): WP_REST_Response {
			$visits = $this->wpdb->get_var( "SELECT SUM(count) FROM $this->table_name" ); // phpcs:ignore

			$response = array(
				'data' => (int) $visits,
			);

			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Update visits
		 *
		 * @param WP_REST_Request $request Request.
		 *
		 * @return WP_REST_Response|WP_Error
		 */
		public function update_visits( WP_REST_Request $request ): WP_REST_Response|WP_Error {
			$route = $request->get_param( 'route' );
			if ( ! $route ) {
				return new WP_Error( 'missing_route', __( 'No `route` provided' ), array( 'status' => 400 ) );
			}

			$route_exists = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count FROM $this->table_name WHERE route = %s", $route ) ); // phpcs:ignore

			if ( null !== $route_exists ) {
				$this->wpdb->query( $this->wpdb->prepare( "UPDATE $this->table_name SET count = count + 1 WHERE route = %s", $route ) ); // phpcs:ignore
			} else {
				$this->wpdb->insert(
					$this->table_name,
					array(
						'route' => $route,
						'count' => 1,
					),
					array( '%s', '%d' )
				);
			}

			$response = array(
				'data' => array(
					'route' => $route,
					'count' => null !== $route_exists ? (int) $route_exists + 1 : 1,
				),
			);

			return new WP_REST_Response( $response, 200 );
		}
	}
}
