<?php
/**
 * Custom list table for displaying the summary table in the admin.
 *
 * @package     email-summary-pro
 * @subpackage  Includes/summary
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Summaries List Table.
 */
class WPNA_Admin_Summaries_List_Table extends WP_List_Table {

	/**
	 * Total active summaries found. Used for pagination.
	 *
	 * @access public
	 * @var int
	 */
	public $active_count = 0;

	/**
	 * Total inactive summaries found. Used for pagination.
	 *
	 * @access public
	 * @var int
	 */
	public $inactive_count = 0;
	/**
	 * Total summaries found. Used for pagination.
	 *
	 * @access public
	 * @var int
	 */
	public $total_count = 0;

	/**
	 * Class constructor.
	 *
	 * Set the tablename & trigger the parent class constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Setup the parent list table class.
		parent::__construct( array(
			'singular' => esc_html__( 'Summary', 'email-summary-pro' ), // Singular name of the listed records.
			'plural'   => esc_html__( 'Summaries', 'email-summary-pro' ), // Plural name of the listed records.
			'ajax'     => false, // No need for ajax.
		) );

		$this->get_summaries_counts();
	}

	/**
	 * Add extra markup in the toolbars before or after the list.
	 *
	 * @access public
	 * @param string $which Helps you decide if you add the markup after (bottom) or before (top) the list.
	 * @return void
	 */
	public function extra_tablenav( $which ) {
		// @codingStandardsIgnoreStart
		if ( 'top' === $which ) {
			// The code that goes before the table is here.
		} elseif ( 'bottom' === $which ) {
			// The code that goes after the table is here.
		}
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Set the columns for the table.
	 *
	 * @access public
	 * @return array The table columns.
	 */
	public function get_columns() {
		$columns = array(
			'cb'             => '<input type ="checkbox" />',
			'name'           => esc_html__( 'Name', 'email-summary-pro' ),
			'status'         => esc_html__( 'Status', 'email-summary-pro' ),
			'recipients'     => esc_html__( 'Recipients', 'email-summary-pro' ),
			'next_scheduled' => esc_html__( 'Next Scheduled', 'email-summary-pro' ),
			'actions'        => esc_html__( 'Actions', 'email-summary-pro' ),
		);

		return $columns;
	}

	/**
	 * Format the row output for each column.
	 *
	 * @access public
	 * @param  array  $item        The current row item being dealt with.
	 * @param  string $column_name The current column being outputted.
	 * @return string The output for that row for that column.
	 */
	public function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	/**
	 * Format the output for the cb column.
	 *
	 * @access public
	 * @param  array $item Row item.
	 * @return string Output for this row and column.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="summary[]" value="%d" />', absint( $item->ID ) );
	}

	/**
	 * Format the output for the name column.
	 *
	 * @access public
	 * @param  array $item Row item.
	 * @return string Output for this row and column.
	 */
	public function column_name( $item ) {
		// Create a nonce.
		$delete_nonce = wp_create_nonce( 'esp_delete_summary' );

		// @codingStandardsIgnoreLine
		$page_id = sanitize_text_field( wp_unslash( $_REQUEST['page'] ) );

		// Create row actions for this item.
		$actions = array(
			'edit'   => sprintf(
				'<a href="?page=%s&esp-action=%s&summary=%s">%s</a>',
				esc_attr( $page_id ),
				'edit_summary',
				absint( $item->ID ),
				esc_html__( 'Edit', 'email-summary-pro' )
			),
			'delete' => sprintf(
				'<a href="?page=%s&action=%s&summary=%s&_wpnonce=%s" onclick="return confirm(\'%s\');">%s</a>',
				esc_attr( $page_id ),
				'delete',
				absint( $item->ID ),
				$delete_nonce,
				esc_html__( 'Are you sure you want to delete this summary?', 'email-summary-pro' ),
				esc_html__( 'Delete', 'email-summary-pro' )
			),
		);

		// If it hasn't got a name, set it as 'untitled'.
		$name = empty( $item->name ) ? esc_html__( '(untitled)', 'email-summary-pro' ) : $item->name;

		return sprintf( '<strong>%1$s</strong>%2$s', $name, $this->row_actions( $actions ) );
	}

	/**
	 * Format the output for the status column.
	 *
	 * @access public
	 * @param  array $item Row item.
	 * @return string Output for this row and column.
	 */
	public function column_status( $item ) {
		// Style WP green if it's active.
		if ( 'active' === $item->status ) {
			return '<span style="color:#46b450;">' . $item->status . '</span>';
		}
		return $item->status;
	}

	/**
	 * Outputs the HTML for the scheduled field.
	 *
	 * Works out the tme the next summary email is scheduled for.
	 *
	 * @access public
	 * @param  array $item Row item.
	 * @return string Output for this row and column.
	 */
	public function column_next_scheduled( $item ) {
		if ( ! $item->next_scheduled ) {
			return '~';
		}

		return date( 'H:ia, jS M Y', strtotime( $item->next_scheduled ) );
	}

	/**
	 * Format the output for the cb column.
	 *
	 * @access public
	 * @param  array $item Row item.
	 * @return string Output for this row and column.
	 */
	public function column_actions( $item ) {

		// Setup the custom date selector.
		$date_selector = '<span style="border-bottom: dashed 1px;" class="js-trigger-datepicker">' . date( 'Y-m-d' ) . '</span>';
		$date_selector .= '<input type="date" class="js-datepicker hidden" id="esp-summary-date" max="' . date( 'Y-m-d' ) . '" value="' . esc_attr( date( 'Y-m-d' ) ) . '" />';

		// Resend URL.
		$resend_url = wp_nonce_url( add_query_arg( array( 'page' => 'email_summary_pro', 'esp-action' => 'resend_summary', 'summary_id' => $item->ID ), admin_url( 'options-general.php' ) ), 'resend_summary', 'esp_nonce');
		// Resend input button.
		$resend_button = sprintf(
			'<a href="%s" data-url="%s" class="js-url-action" title="%s">%s</a>',
			esc_url( $resend_url ),
			esc_url( $resend_url ),
			esc_html__( 'Resend Email Summary', 'email-summary-pro' ),
			esc_html__( 'Resend', 'email-summary-pro' )
	 	);

		// Preview URL.
		$preview_url = wp_nonce_url( add_query_arg( array( 'esp-action' => 'preview_summary', 'summary_id' => $item->ID ), get_home_url() ), 'preview_summary', 'esp_nonce' );
		// Preview input button.
		$preview_button = sprintf(
			'<a href="%s" data-url="%s" target="_blank" class="js-url-action" title="%s">%s</a>',
			esc_url( $preview_url ),
			esc_url( $preview_url ),
			esc_html__( 'Preview Email Summary', 'email-summary-pro' ),
			esc_html__( 'Preview', 'email-summary-pro' )
	 	);

		return sprintf( '<div><div>%s</div><span class="edit">%s</span>&nbsp;|&nbsp;<span class="edit">%s</span></div>', $date_selector, $resend_button, $preview_button );
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @access public
	 * @return array Columns that should be sortable.
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'           => array( 'name', false ),
			'status'         => array( 'status', false ),
			'next_scheduled' => array( 'next_scheduled', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Lists the bulk actions that can take place.
	 *
	 * @access public
	 * @return array Bulk actions to return.
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-activate'   => esc_html__( 'Activate', 'email-summary-pro' ),
			'bulk-deactivate' => esc_html__( 'Deactivate', 'email-summary-pro' ),
			'bulk-delete'     => esc_html__( 'Delete', 'email-summary-pro' ),
		);

		return $actions;
	}

	/**
	 * The text to display if there are no rows to show.
	 *
	 * @access public
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No summaries found.', 'email-summary-pro' );
	}

	/**
	 * Retrieve the summaries counts
	 *
	 * @access public
	 * @return void
	 */
	public function get_summaries_counts() {
		$summaries_count   = wp_count_posts( 'esp_summary' );
		$this->active_count   = $summaries_count->active;
		$this->inactive_count = $summaries_count->inactive;
		$this->total_count    = $summaries_count->active + $summaries_count->inactive;
	}

	/**
	 * Setup the columns, items and pagination.
	 *
	 * @access public
	 * @return void
	 */
	public function prepare_items() {
		// Setup the colum headers.
		$this->_column_headers = $this->get_column_info();

		// Get the items.
		$this->items = $this->get_items();

		// Pagination.
		$per_page    = $this->get_items_per_page( 'summaries_per_page', 20 );
		$total_items = $this->total_count;

		// REQUIRED. We also have to register our pagination options & calculations.
		$this->set_pagination_args( array(
			'total_items' => absint( $total_items ), // WE have to calculate the total number of items.
			'per_page'    => absint( $per_page ),    // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Queries the DB for the actual items.
	 *
	 * @access public
	 * @return array
	 */
	public function get_items() {

		$args = array(
			'posts_per_page' => $this->get_items_per_page( 'summaries_per_page', 20 ),
			'paged'          => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
			'post_status'    => isset( $_GET['status'] ) ? $_GET['status'] : array( 'active', 'inactive' ),
			'order'          => 'DESC',
			'orderby'        => 'ID',
		);

		// Ordering parameters.
		// @codingStandardsIgnoreLine
		if ( ! empty( $_GET['order'] ) && 'asc' === $_GET['order'] ) {
			$args['order'] = 'asc';
		}

		// Orderby parameters.
		if ( ! empty( $_GET['orderby'] ) && array_key_exists( $_GET['orderby'], $this->get_sortable_columns() ) ) {

			if ( in_array( $_GET['orderby'], array( 'name', 'ID' ), true ) ) {
				$args['orderby'] = $_GET['orderby'];
			} else {
				$args['orderby']  = 'meta_value';
				$args['meta_key'] = '_esp_summary_' . $_GET['orderby'];
			}

		}

		// Search parameters.
		if ( ! empty( $_POST['s'] ) ) {
			$args['s'] = sanitize_text_field( wp_unslash( $_POST['s'] ) );
		}

		// Get the summaries.
		$summaries = esp_get_summaries( $args );

		return $summaries;
	}

	/**
	 * Handles any bulk actions that can be performed on the table.
	 *
	 * Also handles in row deletes.
	 *
	 * @access public
	 * @return void
	 */
	public function process_bulk_action() {

		// No action specified then return.
		if ( ! $this->current_action() ) {
			return;
		}

		// Check the user has the correct privileges.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check there's a nonce and that it is valid.
		// This checks both the bulk-action nonce and the inline action URL nonce.
		// @codingStandardsIgnoreLine
		if ( empty( $_REQUEST['_wpnonce'] ) || ( ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'esp_delete_summary' ) && ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'bulk-' . $this->_args['plural'] ) ) ) {
			wp_die( 'Invalid nonce - WPNA Summary Action' );
		}

		global $wpdb;

		// Ensure it's always an array of ids.
		// @codingStandardsIgnoreLine
		$summaries = (array) $_REQUEST['summary'];
		// remive any WP added slashes.
		$summaries = wp_unslash( $summaries );
		// Validate they're all positive intergers.
		$summaries = array_map( 'absint', $summaries );

		// Record the rows affected.
		$rows_affected = array();

		switch ( $this->current_action() ) {

			// Delete rows.
			case 'delete':
			case 'bulk-delete':
				// Loop over the array of record IDs and delete them.
				foreach ( $summaries as $id ) {
					$rows_affected[] = esp_delete_summary( $id );
				}

				wp_safe_redirect(
					esc_url_raw(
						add_query_arg(
							array(
								'page'         => 'email_summary_pro',
								'esp-message' => 'summary_delete_success',
							),
							admin_url( '/admin.php' )
						)
					)
				);
				exit;

				break;

			// Change row state.
			case 'bulk-activate':
			case 'bulk-deactivate':
				// Work out the new status.
				$status = 'active';
				if ( 'bulk-deactivate' === $this->current_action() ) {
					$status = 'inactive';
				}

				// Loop over the array of record IDs and update them.
				foreach ( $summaries as $id ) {
					esp_update_summary_status( $id, $status );
				}

				break;

			// No matching action found.
			default:
				wp_die( esc_html__( 'No matching action found - WPNA Summary', 'email-summary-pro' ) );
				break;
		}

		// Redirect back with message.
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'page'         => 'email_summary_pro',
						'esp-message' => 'summary_update_success',
					),
					admin_url( '/admin.php' )
				)
			)
		);
		exit;

	}

}
