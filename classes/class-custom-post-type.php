<?php

/**
 * 
 * 
 */

class CustomPostType {

	private $post_args;

	private $text_domain = 'hellosunshine';

	public function __construct( object $post ) {
		$this->set_post_args( $post );
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}

	private function set_post_args( $post_type ) {
		$this->post_args           = array(
			'name'                  => $post_type->post_name,
			'Names'                 => ( $post_type->Names ) ? $post_type->Names : ucfirst( $post_type->post_name ),
			'menu_icon'             => $post_type->menu_icon ?? false,
			'supports'              => $post_type->supports ?? false,
			'category'              => $post_type->category ?? false,
			'public'                => $post_type->public ?? true,
			'menu_position'         => $post_type->menu_position ?? null,
			'hierarchical'          => $post_type->hierarchical ?? false,
			'show_ui'               => $post_type->show_ui ?? true,
			'show_in_nav_menus'     => $post_type->show_in_nav_menus ?? true,
			'has_archive'           => $post_type->has_archive ?? true,
			'rewrite'               => $post_type->rewrite ?? true,
			'query_var'             => $post_type->query_var ?? true,
			'show_in_rest'          => $post_type->show_in_rest ?? true,
			'rest_base'             => $post_type->rest_base ?? $post_type->post_name,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
		$this->post_args['labels'] = array(
			'name'                  => __( '' . $this->post_args['Names'] . '', $this->text_domain ),
			'singular_name'         => __( '' . $this->post_args['Names'] . '', $this->text_domain ),
			'all_items'             => __( 'All ' . $this->post_args['Names'] . '', $this->text_domain ),
			'archives'              => __( '' . $this->post_args['Names'] . ' Archives', $this->text_domain ),
			'attributes'            => __( '' . $this->post_args['Names'] . ' Attributes', $this->text_domain ),
			'insert_into_item'      => __( 'Insert into ' . $this->post_args['Names'] . '', $this->text_domain ),
			'uploaded_to_this_item' => __( 'Uploaded to this ' . $this->post_args['Names'] . '', $this->text_domain ),
			'featured_image'        => _x( 'Featured Image', $this->post_args['name'], $this->text_domain ),
			'set_featured_image'    => _x( 'Set featured image', $this->post_args['name'], $this->text_domain ),
			'remove_featured_image' => _x( 'Remove featured image', $this->post_args['name'], $this->text_domain ),
			'use_featured_image'    => _x( 'Use as featured image', $this->post_args['name'], $this->text_domain ),
			'filter_items_list'     => __( 'Filter ' . $this->post_args['Names'] . ' list', $this->text_domain ),
			'items_list_navigation' => __( '' . $this->post_args['Names'] . ' list navigation', $this->text_domain ),
			'items_list'            => __( '' . $this->post_args['Names'] . ' list', $this->text_domain ),
			'new_item'              => __( 'New ' . $this->post_args['Names'] . '', $this->text_domain ),
			'add_new'               => __( 'Add New', $this->text_domain ),
			'add_new_item'          => __( 'Add New ' . $this->post_args['Names'] . '', $this->text_domain ),
			'edit_item'             => __( 'Edit ' . $this->post_args['Names'] . '', $this->text_domain ),
			'view_item'             => __( 'View ' . $this->post_args['Names'] . '', $this->text_domain ),
			'view_items'            => __( 'View ' . $this->post_args['Names'] . '', $this->text_domain ),
			'search_items'          => __( 'Search ' . $this->post_args['Names'] . '', $this->text_domain ),
			'not_found'             => __( 'No ' . $this->post_args['Names'] . ' found', $this->text_domain ),
			'not_found_in_trash'    => __( 'No ' . $this->post_args['Names'] . ' found in trash', $this->text_domain ),
			'parent_item_colon'     => __( 'Parent ' . $this->post_args['Names'] . ':', $this->text_domain ),
			'menu_name'             => __( '' . $this->post_args['Names'] . '', $this->text_domain ),
		);
	}

	public function init() {
		register_post_type(
			$this->post_args['name'],
			$this->post_args
		);

		if ( $this->post_args['category'] ) {
			register_taxonomy(
				$this->post_args['name'] . '_category',
				array( $this->post_args['name'] ),
				array(
					'hierarchical'      => true,
					'label'             => __( ucfirst( $this->post_args['name'] ) . ' Categories', $this->text_domain ),
					'singular_label'    => __( ucfirst( $this->post_args['name'] ) . ' Category', $this->text_domain ),
					'rewrite'           => true,
					'public'            => true,
					'show_admin_column' => true,
				)
			);
		}
	}

	/**
	 * Sets the post updated messages for the `custom` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `custom` post type.
	 */
	public function updated_messages( $messages ) {
		global $post;

		$permalink = get_permalink( $post );

		$messages[ $this->name ] = array(
			0  => '', // Unused. Messages start at index 1.
			/* translators: %s: post permalink */
			1  => sprintf( __( '' . $this->post_args['Names'] . ' updated. <a target="_blank" href="%s">View ' . $this->post_args['Names'] . '</a>', $this->text_domain ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', $this->text_domain ),
			3  => __( 'Custom field deleted.', $this->text_domain ),
			4  => __( '' . $this->post_args['Names'] . ' updated.', $this->text_domain ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( '' . $this->post_args['Names'] . ' restored to revision from %s', $this->text_domain ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/* translators: %s: post permalink */
			6  => sprintf( __( '' . $this->post_args['Names'] . ' published. <a href="%s">View ' . $this->post_args['Names'] . '</a>', $this->text_domain ), esc_url( $permalink ) ),
			7  => __( '' . $this->post_args['Names'] . ' saved.', $this->text_domain ),
			/* translators: %s: post permalink */
			8  => sprintf( __( '' . $this->post_args['Names'] . ' submitted. <a target="_blank" href="%s">Preview ' . $this->post_args['Names'] . '</a>', $this->text_domain ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			9  => sprintf( __( '' . $this->post_args['Names'] . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $this->post_args['Names'] . '</a>', $this->text_domain ), date_i18n( __( 'M j, Y @ G:i', $this->text_domain ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			/* translators: %s: post permalink */
			10 => sprintf( __( '' . $this->post_args['Names'] . ' draft updated. <a target="_blank" href="%s">Preview ' . $this->post_args['Names'] . '</a>', $this->text_domain ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);

		return $messages;
	}


	/**
	 * Sets the bulk post updated messages for the `custom` post type.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array Bulk messages for the `custom` post type.
	 */
	public function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;

		$bulk_messages[ $this->post_args['name'] ] = array(
			/* translators: %s: Number of ' . $this->post_args['Names'] . '. */
			'updated'   => _n( '%s ' . $this->post_args['Names'] . ' updated.', '%s ' . $this->post_args['Names'] . ' updated.', $bulk_counts['updated'], $this->text_domain ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 ' . $this->post_args['Names'] . ' not updated, somebody is editing it.', $this->text_domain ) :
							/* translators: %s: Number of ' . $this->post_args['Names'] . '. */
							_n( '%s ' . $this->post_args['Names'] . ' not updated, somebody is editing it.', '%s ' . $this->post_args['Names'] . ' not updated, somebody is editing them.', $bulk_counts['locked'], $this->text_domain ),
			/* translators: %s: Number of ' . $this->post_args['Names'] . '. */
			'deleted'   => _n( '%s ' . $this->post_args['Names'] . ' permanently deleted.', '%s ' . $this->post_args['Names'] . ' permanently deleted.', $bulk_counts['deleted'], $this->text_domain ),
			/* translators: %s: Number of ' . $this->post_args['Names'] . '. */
			'trashed'   => _n( '%s ' . $this->post_args['Names'] . ' moved to the Trash.', '%s ' . $this->post_args['Names'] . ' moved to the Trash.', $bulk_counts['trashed'], $this->text_domain ),
			/* translators: %s: Number of ' . $this->post_args['Names'] . '. */
			'untrashed' => _n( '%s ' . $this->post_args['Names'] . ' restored from the Trash.', '%s ' . $this->post_args['Names'] . ' restored from the Trash.', $bulk_counts['untrashed'], $this->text_domain ),
		);

		return $bulk_messages;
	}
}
