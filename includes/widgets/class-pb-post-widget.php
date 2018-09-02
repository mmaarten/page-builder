<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Post_Widget extends PB_Widget
{
	protected $post_type  = null;
	protected $taxonomies = array();

	public function __construct( $post_type )
	{
		$this->post_type = get_post_type_object( $post_type );
		$this->taxonomies = get_taxonomies( array
		(
			'object_type' => (array) $this->post_type->name,
			'public'      => true
		), 'objects' );

		parent::__construct( "post_{$this->post_type->name}", ucfirst( $this->post_type->labels->name ), array
		(
			'description' => sprintf( __( 'Displays %s.' ), strtolower( $this->post_type->labels->name ) ),
			'features'    => array
			( 
				'id', 
				'class', 
				'margin_top', 
				'margin_bottom', 
				'padding_top', 
				'padding_bottom', 
			)
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		// Specific Posts

		$this->add_field( array
		(
			'key'           => 'posts',
			'name'          => 'posts',
			'title'         => __( 'Specific Posts' ),
			'description'   => __( 'Optional' ),
			'type'          => 'post',
			'post_type'     => $this->post_type->name,
			'multiple'      => true,
			'default_value' => array(),
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		// Per Page

		$this->add_field( array
		(
			'key'           => 'posts_per_page',
			'name'          => 'posts_per_page',
			'title'         => __( 'Per Page' ),
			'description'   => '',
			'type'          => 'number',
			'default_value' => get_option( 'posts_per_page', 10 ),
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		// Paging

		$this->add_field( array
		(
			'key'           => 'paging',
			'name'          => 'paging',
			'title'         => __( 'Paging' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => true,
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));
		
		// Context

		$this->add_field( array
		(
			'key'           => 'context',
			'name'          => 'context',
			'title'         => __( 'Context' ),
			'description'   => __( 'Provide a contextual key to alter/extend the settings programatorically.' ),
			'type'          => 'text',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));

		// Terms

		if ( $this->taxonomies ) 
		{	
			$this->add_field( array
			(
				'key'           => 'terms_tab',
				'name'          => 'terms_tab',
				'title'         => __( 'Terms' ),
				'description'   => '',
				'type'          => 'tab',
				'default_value' => '',
				'order'         => PB_ORDER_TAB_GENERAL + 50
			));

			$order = 50;

			foreach ( $this->taxonomies as $taxonomy ) 
			{
				$this->add_field( array
				(
					'key'         => "{$taxonomy->name}_terms",
					'name'        => "{$taxonomy->name}_terms",
					'title'       => $taxonomy->labels->name,
					'description' => '',
					'type'        => 'repeater',
					'order'       => $order + 10
				));

				$this->add_field( array
				(
					'key'           => "{$taxonomy->name}_terms_term",
					'name'          => 'term',
					'title'         => __( 'Term' ),
					'description'   => '',
					'type'          => 'term',
					'taxonomy'      => $taxonomy->name,
					'default_value' => '',
					'parent'        => "{$taxonomy->name}_terms",
					'order'         => 10
				));

				$this->add_field( array
				(
					'key'           => "{$taxonomy->name}_terms_operator",
					'name'          => 'operator',
					'title'         => __( 'Operator' ),
					'description'   => '',
					'type'          => 'select',
					'choices'       => array
					(
						'IN'     => __( 'IN' ),
						'NOT IN' => __( 'NOT IN' )
					),
					'default_value' => 'IN',
					'parent'        => "{$taxonomy->name}_terms",
					'order'         => 20
				));

				$order += 10;
			}
		}

		// Meta

		$this->add_field( array
		(
			'key'           => 'meta_tab',
			'name'          => 'meta_tab',
			'title'         => __( 'Meta' ),
			'description'   => '',
			'type'          => 'tab',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 200
		));

		$this->add_field( array
		(
			'key'         => 'meta',
			'name'        => 'meta',
			'title'       => __( 'Meta' ),
			'description' => '',
			'type'        => 'repeater',
			'order'       => PB_ORDER_TAB_GENERAL + 210
		));

		$this->add_field( array
		(
			'key'           => 'meta_key',
			'name'          => 'key',
			'title'         => __( 'Key' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'parent'        => 'meta'
		));

		$this->add_field( array
		(
			'key'           => 'meta_compare',
			'name'          => 'compare',
			'title'         => __( 'Compare' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'=' 		  => __( '=' ), 
				'!=' 		  => __( '!=' ), 
				'>'			  => __( '>' ), 
				'>=' 		  => __( '>=' ), 
				'<' 		  => __( '<' ), 
				'<=' 		  => __( '<=' ), 
				'LIKE'        => __( 'LIKE' ), 
				'NOT LIKE'    => __( 'NOT LIKE' ), 
				'IN'          => __( 'IN' ), 
				'NOT IN'      => __( 'NOT IN' ), 
				'BETWEEN'     => __( 'BETWEEN' ), 
				'NOT BETWEEN' => __( 'NOT BETWEEN' ), 
				'NOT EXISTS'  => __( 'NOT EXISTS' ), 
				'REGEXP'      => __( 'REGEXP' ), 
				'NOT REGEXP'  => __( 'NOT REGEXP' ), 
				'RLIKE'       => __( 'RLIKE' )
			),
			'default_value' => '=',
			'parent'        => 'meta'
		));

		$this->add_field( array
		(
			'key'           => 'meta_value',
			'name'          => 'value',
			'title'         => __( 'Value' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'parent'        => 'meta'
		));

		// Order

		$this->add_field( array
		(
			'key'           => 'order_tab',
			'name'          => 'order_tab',
			'title'         => __( 'Order' ),
			'description'   => '',
			'type'          => 'tab',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 230
		));

		$this->add_field( array
		(
			'key'           => 'order_by',
			'name'          => 'order_by',
			'title'         => __( 'Order By' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'author'         => __( 'Author' ),
				'title'          => __( 'Title' ),
				'date'           => __( 'Date' ),
				'modified'       => __( 'Date modified' ),
				'rand'           => __( 'Random' ),
				'comment_count'  => __( 'Comment count' ),
				'menu_order'     => __( 'Menu order' ),
				'post__in'       => __( 'Specific Posts' ),
				'meta_value'     => __( 'Meta value' ),
				'meta_value_num' => __( 'Numeric meta value' )
			),
			'default_value' => 'date',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 240
		));

		$this->add_field( array
		(
			'key'           => 'order',
			'name'          => 'order',
			'title'         => __( 'Order' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'ASC'  => __( 'Ascending' ),
				'DESC' => __( 'Descending' )
			),
			'default_value' => 'DESC',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 250
		));

		$this->add_field( array
		(
			'key'           => 'order_meta_key',
			'name'          => 'order_meta_key',
			'title'         => __( 'Meta Key' ),
			'description'   => __( "Only effective when 'Order By' is set to 'Meta Value' or 'Numeric meta value'." ),
			'type'          => 'text',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 260
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */

		// Column Width

		$this->add_field( array
		(
			'key'           => 'columns',
			'name'          => 'columns',
			'title'         => __( 'Column Width' ),
			'description'   => '',
			'type'          => 'group',
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			$this->add_field( array
			(
				'key'           => "tab_$breakpoint",
				'name'          => "tab_$breakpoint",
				'title'         => strtoupper( $breakpoint ),
				'description'   => '',
				'type'          => 'tab',
				'default_value' => '',
				'parent'        => 'columns'
			));

			$this->add_field( array
			(
				'key'           => "{$breakpoint}_width",
				'name'          => $breakpoint,
				'title'         => '',
				'description'   => '',
				'type'          => 'select',
				'choices'       => pb_get_column_choices( $breakpoint ),
				'default_value' => $breakpoint == 'xs' ? 4 : '',
				'parent'        => 'columns'
			));
		}

		// Post Template

		$this->add_field( array
		(
			'key'           => 'post_template',
			'name'          => 'post_template',
			'title'         => __( 'Custom Post Template' ),
			'description'   => __( 'Location: page-builder/templates/grid-item-{template_name}.php' ),
			'type'          => 'text',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_LAYOUT + 20
		));

		/* -------------------------------------------------------- */

		add_action( "wp_ajax_pb_post_{$this->id}_widget_load"		 , array( $this, 'load' ) );
		add_action( "wp_ajax_nopriv_pb_post_{$this->id}_widget_load", array( $this, 'load' ) );
	}

	public function widget_html_attributes( $atts, $instance )
	{
		$atts['class'] .= ' pb-post-widget';

		return $atts;
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$post  = pb()->widgets->post;
		$model = pb()->widgets->model;

		// gets id from widget

		if ( preg_match( '/id="([a-z0-9-_]+)"/i', $args['before_widget'], $matches ) )
		{
			$id = $matches[1];
		}

		else
		{
			$id = ''; // TODO: Make sure we have an id.
		}

		$options = array
		(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		);

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<form method="post">
			
			<?php do_action( 'pb/post_widget_form', $this ); ?>

			<?php wp_nonce_field( "post_{$this->post_type->name}_widget", PB_NONCE_NAME ); ?>

			<input type="hidden" name="action" value="pb_post_<?php echo esc_attr( $this->id ); ?>_widget_load">
			<input type="hidden" name="post" value="<?php echo esc_attr( $post->ID ); ?>">
			<input type="hidden" name="model" value="<?php echo esc_attr( $model['id'] ); ?>">
			<input type="hidden" name="paged" value="1">
			<input type="hidden" name="context" value="<?php echo esc_attr( $instance['context'] ); ?>">

		</form>

		<div class="pb-entries"></div>

		<script type="text/javascript">
			
			jQuery( document ).ready( function( $ )
			{
				$( '#<?php echo esc_js( $id ); ?>' ).postGrid( <?php echo json_encode( $options ); ?> );
			});

		</script>

		<?php

		echo $args['after_widget'];
	}

	protected function build_query( $instance )
	{
		/**
		 * Common
		 * -----------------------------------------------------------
		 */

		$query_args = array
		(
			'post_type'      => $this->post_type->name,
			'post__in'       => $instance['posts'],
			'orderby'        => $instance['order_by'],
			'order'          => $instance['order'],
			'posts_per_page' => $instance['posts_per_page'],
			'no_found_rows'  => $instance['paging'] ? false : true 
		);

		/**
		 * Meta Query
		 * -----------------------------------------------------------
		 */

		if ( $instance['meta'] ) 
		{
			foreach ( $instance['meta'] as $data ) 
			{
				$query_args['meta_query'][] = array
				(
					'key'     => $data['key'],
					'value'   => $data['value'],
					'compare' => $data['compare']
				);
			}
		}

		/**
		 * Tax Query
		 * -----------------------------------------------------------
		 */

		foreach ( $this->taxonomies as $taxonomy ) 
		{
			if ( ! $instance[ "{$taxonomy->name}_terms" ] ) 
			{
				continue;
			}

			$operators = array();

			foreach ( $instance[ "{$taxonomy->name}_terms" ] as $row ) 
			{
				$operators[ $row['operator'] ][] = $row['term'];
			}

			foreach ( $operators as $operator => $terms ) 
			{
				$query_args['tax_query'][] = array
				(
					'taxonomy' => $taxonomy->name,
					'field'    => 'term_id',
					'terms'    => $terms,
					'operator' => $operator
				);
			}
		}

		/**
		 * Order
		 * -----------------------------------------------------------
		 */

		if ( $instance['order_by'] == 'meta_value' || $instance['order_by'] == 'meta_value_num' ) 
		{
			$query_args['meta_key'] = $instance['order_meta_key'];
		}

		/**
		 * Context
		 * -----------------------------------------------------------
		 */

		if ( $instance['context'] ) 
		{
			$query_args['pb_context'] = $instance['context'];
		}

		/**
		 * Filter
		 * -----------------------------------------------------------
		 */

		$query_args = apply_filters( 'pb/post_widget_query_args', $query_args, $instance['context'], $this, $instance );

		/* -------------------------------------------------------- */

		return $query_args;
	}

	public function render( $instance )
	{

	}

	public function load()
	{
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		check_ajax_referer( "post_{$this->post_type->name}_widget", PB_NONCE_NAME );

		$post_id  = isset( $_POST['post'] ) ? $_POST['post'] : 0;
		$model_id = isset( $_POST['model'] ) ? $_POST['model'] : '';
		$paged    = isset( $_POST['paged'] ) ? $_POST['paged'] : 0;
		$context  = isset( $_POST['context'] ) ? $_POST['context'] : '';

		$models = pb()->models->get_post_models( $post_id );

		$model = $models[ $model_id ];

		$instance = wp_parse_args( $model['data'], $this->get_defaults() );

		/**
		 * WP Query
		 * -----------------------------------------------------------
		 */

		$query_args = $this->build_query( $instance );
		$query_args['paged'] = intval( $paged );

		$the_query = new WP_Query( $query_args );

		/**
		 * Templates
		 * -----------------------------------------------------------
		 */

		$templates = array
		(
			"grid-item-{$instance['post_template']}.php",
			'grid-item.php'
		);

		/* -------------------------------------------------------- */

		$data = array
		(
			'content'       => '',
			'post_count'    => $the_query->post_count,
			'found_posts'   => $the_query->found_posts,
			'paged'         => intval( $the_query->get( 'paged' ) ),
			'max_num_pages' => $the_query->max_num_pages,
			'query_vars'    => $the_query->query_vars
		);

		ob_start();

		if ( $the_query->have_posts() ) 
		{
			echo '<div class="row">';

			while ( $the_query->have_posts() ) 
			{
				$the_query->the_post();

				printf( '<div class="%s">', pb_get_column_class( $instance['columns'] ) );

				pb()->templates->locate( $templates, true, false );

				echo '</div>';
			}

			echo '</div>';

			pb_post_pagination( $the_query );

			wp_reset_postdata();
		}

		$data['content'] = ob_get_clean();

		wp_send_json_success( $data );
	}
}
