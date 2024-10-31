<?php

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class AJF_Nearby_Locations_Types_Table extends WP_List_Table {
    
  function __construct() {

    global $status, $page;

    parent::__construct(array(
      'singular'  => 'location_type',
      'plural'    => 'location_types',
      'ajax'      => false
    ));
  }

  function column_default($item, $column_name) {
    switch($column_name) {
      case 'name':
      case 'order':
        return esc_html($item[$column_name]);
      default:
        return print_r(esc_html($item), true);
    }
  }

  function column_name($item) {      
    // Build row actions
    $actions = array(
      'edit'   => sprintf('<a href="?page=%s&action=%s&'.$this->_args['singular'].'=%s">Edit</a>', esc_attr(sanitize_key($_REQUEST['page'])), 'edit', $item['id']),
      'delete' => sprintf('<a href="?page=%s&action=%s&'.$this->_args['singular'].'=%s">Delete</a>', esc_attr(sanitize_key($_REQUEST['page'])), 'delete', $item['id']),
    );
    
    // Return the title contents
    return sprintf('%1$s%2$s',
      /*$1%s*/ $item['name'],
      /*$2%s*/ $this->row_actions($actions)
    );
  }

  function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="%1$s[]" value="%2$s" />',
      /*$1%s*/ $this->_args['singular'],
      /*$2%s*/ $item['id']
    );
  }

  function get_columns() {
    $columns = array(
      'cb'    => '<input type="checkbox" />',
      'name'  => 'Location Type',
      'order' => 'Order'
    );
    return $columns;
  }

  function get_sortable_columns() {
    $sortable_columns = array(
      'name' => array('name', false)
    );
    return $sortable_columns;
  }

  function get_bulk_actions() {
    $actions = array(
      'delete' => 'Delete'
    );
    return $actions;
  }

  function process_bulk_action() {
    
    global $wpdb;

    // get the location(s) to perform actions on
    $location_type = $_GET[$this->_args['singular']];

    // detect when a bulk action is being triggered...
    if ('delete' === $this->current_action()) {

      // make sections an array, if it is not already
      if (!is_array($location_type)) {
        $location_type = [$location_type];
      }

      $location_type_ids = implode(",", $location_type);

      // remove the sections from the database
      $table_name = $wpdb->prefix . "ajf_nl_sections";
      $wpdb->query("DELETE FROM $table_name WHERE id IN (" . $location_type_ids . ")");

      // update the location that had this section type
      $table_name = $wpdb->prefix . "ajf_nl_locations";
      $wpdb->query("UPDATE $table_name SET section_id = '-99' WHERE section_id IN (" . $location_type_ids . ")");
    }
  }

  function prepare_items() {

    global $wpdb;
    $per_page = 20;
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    
    $this->_column_headers = array($columns, $hidden, $sortable);
    $this->process_bulk_action();

    $table_name = $wpdb->prefix . "ajf_nl_sections"; 
    $data = $wpdb->get_results("SELECT * FROM $table_name", "ARRAY_A");

    function usort_reorder($a,$b) {
      $orderby = sanitize_sql_orderby($_REQUEST['orderby']);
      $order = sanitize_key($_REQUEST['order']);

      $orderby = !empty($orderby) ? $orderby : 'order';
      $order = !empty($order) ? $order : 'asc';
      $result = strcmp($a[$orderby], $b[$orderby]);
      return ($order === 'asc') ? $result : -$result;
    }
    usort($data, 'usort_reorder');

    $current_page = $this->get_pagenum();
    $total_items = count($data);
    $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
    $this->items = $data;

    $this->set_pagination_args( array(
      'total_items' => $total_items,
      'per_page'    => $per_page,
      'total_pages' => ceil($total_items/$per_page)
    ));
  }
}

function ajf_nearby_locations_render_list_page() {
    
  // Create an instance of our package class...
  $testListTable = new AJF_Nearby_Locations_Types_Table();
  // Fetch, prepare, sort, and filter our data...
  $testListTable->prepare_items();
  
  ?>

  <div class="wrap">

    <div id="icon-users" class="icon32"><br/></div>
    <h2>Location Types</h2>
    
    <form method="get">
      <!-- For plugins, we also need to ensure that the form posts back to our current page -->
      <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_key($_REQUEST['page'])); ?>" />
      <!-- Now we can render the completed list table -->
      <?php $testListTable->display(); ?>
    </form>

  </div>

  <?php
}

ajf_nearby_locations_render_list_page();