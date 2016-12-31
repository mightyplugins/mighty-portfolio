<?php

if (!class_exists('MP_Post_Type')):

	/**
	* Resgister custom post type with class
	*/
	class MP_Post_Type
	{
		
		function __construct()
		{
			// Register post type
			add_action( 'init', array($this, 'mp_register_post_type') );
			add_action( 'init', array($this, 'mp_register_portfolio_category') );

			// Add custom column to portfolio list 
			add_action( 'manage_mp-portfolio_posts_columns', array($this, 'mp_add_custom_columns') );

			add_action( 'manage_mp-portfolio_posts_custom_column', array($this, 'mp_render_custom_column'), 10, 2 );
		}

		function mp_register_post_type() {
			$labels = array(
				'name'               => _x( 'Portfolios', 'post type general name', 'mighty-portfolio' ),
				'singular_name'      => _x( 'Portfolio', 'post type singular name', 'mighty-portfolio' ),
				'menu_name'          => _x( 'Portfolios', 'admin menu', 'mighty-portfolio' ),
				'name_admin_bar'     => _x( 'Portfolio', 'add new on admin bar', 'mighty-portfolio' ),
				'add_new'            => _x( 'Add New', 'book', 'mighty-portfolio' ),
				'add_new_item'       => __( 'Add New Portfolio', 'mighty-portfolio' ),
				'new_item'           => __( 'New Portfolio', 'mighty-portfolio' ),
				'edit_item'          => __( 'Edit Portfolio', 'mighty-portfolio' ),
				'view_item'          => __( 'View Portfolio', 'mighty-portfolio' ),
				'all_items'          => __( 'All Portfolios', 'mighty-portfolio' ),
				'search_items'       => __( 'Search Portfolios', 'mighty-portfolio' ),
				'parent_item_colon'  => __( 'Parent Portfolios:', 'mighty-portfolio' ),
				'not_found'          => __( 'No books found.', 'mighty-portfolio' ),
				'not_found_in_trash' => __( 'No books found in Trash.', 'mighty-portfolio' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'mighty-portfolio' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array(
					'slug' => apply_filters( 'ct_portfolio_post_slug', 'portfolio' )
				),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'thumbnail' )
			);

			register_post_type( 'mp-portfolio', $args );
		}

		public function mp_register_portfolio_category()
		{
			// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'mighty-portfolio' ),
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'mighty-portfolio' ),
				'search_items'      => __( 'Search Categories', 'mighty-portfolio' ),
				'all_items'         => __( 'All Categories', 'mighty-portfolio' ),
				'parent_item'       => __( 'Parent Category', 'mighty-portfolio' ),
				'parent_item_colon' => __( 'Parent Category:', 'mighty-portfolio' ),
				'edit_item'         => __( 'Edit Category', 'mighty-portfolio' ),
				'update_item'       => __( 'Update Category', 'mighty-portfolio' ),
				'add_new_item'      => __( 'Add New Category', 'mighty-portfolio' ),
				'new_item_name'     => __( 'New Category Name', 'mighty-portfolio' ),
				'menu_name'         => __( 'Category', 'mighty-portfolio' ),
			);
		 
			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'cat_portfolio' ),
			);
		 
			register_taxonomy( 'cat_portfolio', array( 'mp-portfolio' ), $args );
		}

		function mp_add_custom_columns( $columns )
		{
			unset( $columns['title'], $columns['date'], $columns['taxonomy-cat_portfolio'] );

			$columns['mppthumb'] = esc_html__( 'Image', 'mighty-portfolio' );
			$columns['title'] = esc_html__( 'Name', 'mighty-portfolio' );
			$columns['taxonomy-cat_portfolio'] = esc_html__( 'Categories', 'mighty-portfolio' );
			$columns['date'] = esc_html__( 'Date', 'mighty-portfolio' );

			return $columns;
		}

		function mp_render_custom_column( $column_name, $post_id )
		{
			$capacity = apply_filters( 'ct_portfolio_capacity_role', 'edit_posts' );
			if( $column_name == 'mppthumb' && current_user_can( $capacity ) ) {
				the_post_thumbnail(array(50,50));
			}
		}
	}

	$post_type = new MP_Post_Type();

endif;