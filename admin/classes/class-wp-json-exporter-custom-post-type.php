<?php

if ( ! class_exists( 'WP_Json_Exporter_Custom_Post_Type' ) ) {
	class WP_Json_Exporter_Custom_Post_Type {
		function __construct() {
			add_action( 'init', array( $this, 'register_project' ) );
			add_action( 'init', array( $this, 'register_project_taxonomies' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_project_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_project_meta_boxes' ) );
		}

		function register_project(): void {
			$labels = array(
				'name'                  => _x( 'Projects', 'Post Type General Name', 'text_domain' ),
				'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'text_domain' ),
				'menu_name'             => __( 'Projects', 'text_domain' ),
				'name_admin_bar'        => __( 'Project', 'text_domain' ),
				'archives'              => __( 'Project Archives', 'text_domain' ),
				'attributes'            => __( 'Project Attributes', 'text_domain' ),
				'parent_item_colon'     => __( 'Parent Project:', 'text_domain' ),
				'all_items'             => __( 'All Projects', 'text_domain' ),
				'add_new_item'          => __( 'Add New Project', 'text_domain' ),
				'add_new'               => __( 'Add New', 'text_domain' ),
				'new_item'              => __( 'New Project', 'text_domain' ),
				'edit_item'             => __( 'Edit Project', 'text_domain' ),
				'update_item'           => __( 'Update Project', 'text_domain' ),
				'view_item'             => __( 'View Project', 'text_domain' ),
				'view_items'            => __( 'View Projects', 'text_domain' ),
				'search_items'          => __( 'Search Project', 'text_domain' ),
				'not_found'             => __( 'Not found', 'text_domain' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
				'featured_image'        => __( 'Featured Image', 'text_domain' ),
				'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
				'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
				'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
				'insert_into_item'      => __( 'Insert into project', 'text_domain' ),
				'uploaded_to_this_item' => __( 'Uploaded to this project', 'text_domain' ),
				'items_list'            => __( 'Projects list', 'text_domain' ),
				'items_list_navigation' => __( 'Projects list navigation', 'text_domain' ),
				'filter_items_list'     => __( 'Filter projects list', 'text_domain' ),
			);

			$args = array(
				'label'               => __( 'Project', 'text_domain' ),
				'description'         => __( 'Custom post type for projects', 'text_domain' ),
				'labels'              => $labels,
				'show_in_rest'        => true,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
				'taxonomies'          => array( 'project_category' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'menu_icon'           => 'dashicons-portfolio',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_graphql'     => true,
				'graphql_single_name' => 'project',
				'graphql_plural_name' => 'projects',
			);

			register_post_type( 'project', $args );
		}

		function register_project_taxonomies(): void {
			$labels = array(
				'name'                       => _x( 'Project Categories', 'Taxonomy General Name', 'text_domain' ),
				'singular_name'              => _x( 'Project Category', 'Taxonomy Singular Name', 'text_domain' ),
				'menu_name'                  => __( 'Project Categories', 'text_domain' ),
				'all_items'                  => __( 'All Categories', 'text_domain' ),
				'parent_item'                => __( 'Parent Category', 'text_domain' ),
				'parent_item_colon'          => __( 'Parent Category:', 'text_domain' ),
				'new_item_name'              => __( 'New Category Name', 'text_domain' ),
				'add_new_item'               => __( 'Add New Category', 'text_domain' ),
				'edit_item'                  => __( 'Edit Category', 'text_domain' ),
				'update_item'                => __( 'Update Category', 'text_domain' ),
				'view_item'                  => __( 'View Category', 'text_domain' ),
				'separate_items_with_commas' => __( 'Separate categories with commas', 'text_domain' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'text_domain' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
				'popular_items'              => __( 'Popular Categories', 'text_domain' ),
				'search_items'               => __( 'Search Categories', 'text_domain' ),
				'not_found'                  => __( 'Not Found', 'text_domain' ),
				'no_terms'                   => __( 'No categories', 'text_domain' ),
				'items_list'                 => __( 'Categories list', 'text_domain' ),
				'items_list_navigation'      => __( 'Categories list navigation', 'text_domain' ),
			);

			$args = array(
				'labels'              => $labels,
				'show_in_rest'        => true,
				'hierarchical'        => true,
				'public'              => true,
				'show_ui'             => true,
				'show_admin_column'   => true,
				'show_in_nav_menus'   => true,
				'show_tagcloud'       => true,
				'show_in_graphql'     => true,
				'graphql_single_name' => 'projectCategory',
				'graphql_plural_name' => 'projectCategories',
			);

			register_taxonomy( 'project_category', array( 'project' ), $args );
		}

		function add_project_meta_boxes(): void {
			add_meta_box( 'product_owner', 'Product Owner', array(
				$this,
				'product_owner_meta_callback'
			), 'project', 'normal', 'high' );

			add_meta_box( 'tech_stack', 'Tech Stack', array(
				$this,
				'tech_stack_meta_callback'
			), 'project', 'normal', 'high' );

			add_meta_box( 'website', 'Website', array(
				$this,
				'website_meta_callback'
			), 'project', 'normal', 'high' );

			add_meta_box( 'my_role', 'My Role', array(
				$this,
				'my_role_meta_callback'
			), 'project', 'normal', 'high' );

			add_meta_box( 'json_exporter_color', 'Color', array(
				$this,
				'color_meta_callback'
			), 'project', 'normal', 'high' );
		}

		function product_owner_meta_callback( $post ): void {
			wp_nonce_field( basename( __FILE__ ), 'product_owner_nonce' );
			$product_owner = get_post_meta( $post->ID, 'product_owner', true );

			echo '<input type="text" id="product_owner" name="product_owner" style="width: 100%;" value="' . esc_attr( $product_owner ) . '" />';
		}

		function tech_stack_meta_callback( $post ): void {
			wp_nonce_field( basename( __FILE__ ), 'tech_stack_nonce' );
			$tech_stack = get_post_meta( $post->ID, 'tech_stack', true );

			echo '<textarea id="tech_stack" name="tech_stack" rows="4" cols="50">' . esc_textarea( $tech_stack ) . '</textarea>';
		}


		function website_meta_callback( $post ): void {
			wp_nonce_field( basename( __FILE__ ), 'website_nonce' );
			$website = get_post_meta( $post->ID, 'website', true );

			echo '<input type="url" id="website" name="website" style="width: 100%;" value="' . esc_url( $website ) . '" />';
		}

		function my_role_meta_callback( $post ): void {
			wp_nonce_field( basename( __FILE__ ), 'my_role_nonce' );
			$my_role = get_post_meta( $post->ID, 'my_role', true );

			echo '<textarea id="my_role" name="my_role" rows="4" cols="50">' . esc_textarea( $my_role ) . '</textarea>';
		}

		function color_meta_callback( $post ): void {
			wp_nonce_field( basename( __FILE__ ), 'color_nonce' );
			$color = get_post_meta( $post->ID, 'color', true );

			echo '<input type="color" id="color" name="color" value="' . esc_attr( $color ) . '" />';
		}

		function save_project_meta_boxes( $post_id ): void {
			// Check if our nonce is set.
			if ( ! isset( $_POST['product_owner_nonce'] ) ||
			     ! isset( $_POST['tech_stack_nonce'] ) ||
			     ! isset( $_POST['website_nonce'] ) ||
			     ! isset( $_POST['my_role_nonce'] ) ||
			     ! isset( $_POST['color_nonce'] ) ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['product_owner_nonce'], basename( __FILE__ ) ) ||
			     ! wp_verify_nonce( $_POST['tech_stack_nonce'], basename( __FILE__ ) ) ||
			     ! wp_verify_nonce( $_POST['website_nonce'], basename( __FILE__ ) ) ||
			     ! wp_verify_nonce( $_POST['my_role_nonce'], basename( __FILE__ ) ) ||
			     ! wp_verify_nonce( $_POST['color_nonce'], basename( __FILE__ ) ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check the user's permissions.
			if ( isset( $_POST['post_type'] ) && 'project' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}

			// Make sure that it is set.
			if ( ! isset( $_POST['product_owner'] ) ||
			     ! isset( $_POST['tech_stack'] ) ||
			     ! isset( $_POST['website'] ) ||
			     ! isset( $_POST['my_role'] ) ||
			     ! isset( $_POST['color'] ) ) {
				return;
			}

			// Sanitize user input.
			$product_owner_data = sanitize_text_field( $_POST['product_owner'] );
			$tech_stack_data    = sanitize_textarea_field( $_POST['tech_stack'] );
			$website_data       = esc_url_raw( $_POST['website'] );
			$my_role_data       = sanitize_textarea_field( $_POST['my_role'] );
			$color_data         = sanitize_hex_color( $_POST['color'] );

			// Update the meta field in the database.
			update_post_meta( $post_id, 'product_owner', $product_owner_data );
			update_post_meta( $post_id, 'tech_stack', $tech_stack_data );
			update_post_meta( $post_id, 'website', $website_data );
			update_post_meta( $post_id, 'my_role', $my_role_data );
			update_post_meta( $post_id, 'color', $color_data );
		}
	}
}