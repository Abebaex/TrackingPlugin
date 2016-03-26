<?php
/*
Plugin Name: Tracking Data Shipment
Plugin URI: http://abebae.com
Description: Shipment International Aplication
Author: Abebae
Author URI: http://abebae.com
*/

function tracking_install()
{
    global $wpdb;
    global $tracking_db_version;

    $table_name = $wpdb->prefix . 'shipment';
    $sql = "CREATE TABLE $table_name (
	  ship_id int(10) NOT NULL AUTO_INCREMENT,
	  ship_awbno varchar(20) DEFAULT NULL,
	  ship_service varchar(100) DEFAULT NULL,
	  ship_date date DEFAULT NULL,
	  ship_origin varchar(255) DEFAULT NULL,
	  ship_destination varchar(255) DEFAULT NULL,
	  ship_shipper varchar(255) DEFAULT NULL,
	  ship_shipper_address varchar(255) DEFAULT NULL,
	  ship_shipper_city varchar(255) DEFAULT NULL,
	  ship_shipper_province varchar(255) DEFAULT NULL,
	  ship_shipper_country varchar(255) DEFAULT NULL,
	  ship_shipper_zipcode varchar(100) DEFAULT NULL,
	  ship_shipper_phone varchar(30) DEFAULT NULL,
	  ship_consignee varchar(255) DEFAULT NULL,
	  ship_consignee_address varchar(255) DEFAULT NULL,
	  ship_consignee_city varchar(255) DEFAULT NULL,
	  ship_consignee_province varchar(255) DEFAULT NULL,
	  ship_consignee_country varchar(255) DEFAULT NULL,
	  ship_consignee_zipcode varchar(100) DEFAULT NULL,
	  ship_consignee_phone varchar(30) DEFAULT NULL,
	  ship_parent int(10) DEFAULT '0',
	  ship_datetime datetime DEFAULT NULL,
	  ship_citycountry varchar(100) DEFAULT NULL,
	  ship_status varchar(2) DEFAULT '0',
	  ship_receiver varchar(255) DEFAULT NULL,
	  PRIMARY KEY (ship_id)
	) ";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('tracking_db_version', $tracking_db_version);
    $installed_ver = get_option('tracking_db_version');
    if ($installed_ver != $tracking_db_version) {
		$sql = "CREATE TABLE $table_name (
		  ship_id int(10) NOT NULL AUTO_INCREMENT,
		  ship_awbno varchar(20) DEFAULT NULL,
		  ship_service varchar(100) DEFAULT NULL,
		  ship_date date DEFAULT NULL,
		  ship_origin varchar(255) DEFAULT NULL,
		  ship_destination varchar(255) DEFAULT NULL,
		  ship_shipper varchar(255) DEFAULT NULL,
		  ship_shipper_address varchar(255) DEFAULT NULL,
		  ship_shipper_city varchar(255) DEFAULT NULL,
		  ship_shipper_province varchar(255) DEFAULT NULL,
		  ship_shipper_country varchar(255) DEFAULT NULL,
		  ship_shipper_zipcode varchar(100) DEFAULT NULL,
		  ship_shipper_phone varchar(30) DEFAULT NULL,
		  ship_consignee varchar(255) DEFAULT NULL,
		  ship_consignee_address varchar(255) DEFAULT NULL,
		  ship_consignee_city varchar(255) DEFAULT NULL,
		  ship_consignee_province varchar(255) DEFAULT NULL,
		  ship_consignee_country varchar(255) DEFAULT NULL,
		  ship_consignee_zipcode varchar(100) DEFAULT NULL,
		  ship_consignee_phone varchar(30) DEFAULT NULL,
		  ship_parent int(10) DEFAULT '0',
		  ship_datetime datetime DEFAULT NULL,
		  ship_citycountry varchar(100) DEFAULT NULL,
		  ship_status varchar(2) DEFAULT '0',
		  ship_receiver varchar(255) DEFAULT NULL,
		  PRIMARY KEY (ship_id)
		) ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option('tracking_db_version', $tracking_db_version);
    }
}
register_activation_hook(__FILE__, 'tracking_install');
function tracking_install_data()
{
		global $wpdb;
		$table_name = $wpdb->prefix . 'shipment';
		$wpdb->insert($table_name, array(
			'ship_awbno' => '123432345',
			'ship_service' => 'REG',
			'ship_date' => '2013-09-19',
			'ship_origin' => 'Bandung',
			'ship_destination' => 'Denpasar',
			'ship_shipper' => 'BRoDo',
			'ship_consignee' => 'Abebae',
			'ship_parent'=> '0',
			'ship_status'=> '0'
		));
}

register_activation_hook(__FILE__, 'tracking_install_data');


function tracking_update_db_check()
{
    global $tracking_db_version;
    if (get_site_option('tracking_db_version') != $tracking_db_version) {
        tracking_install();
    }
}

add_action('plugins_loaded', 'tracking_update_db_check');
/*done installer */

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Tracking_List_Table extends WP_List_Table {
    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => 'tracking',
            'plural'    => 'trackings',
            'ajax'      => false
        ) );
      add_action( 'admin_head', array( &$this, 'admin_header' ) );               
    }
	function admin_header() {
    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'my_list_test' != $page )
    return;
		echo '<style type="text/css">';
		echo '.wp-list-table .column-cb_ship_id { width: 5%; }';
		echo '</style>';
  }

  function no_items() {
    _e( 'No Data found.' );
  }

    function column_default($item, $column_name){
        switch($column_name){
            case 'ship_id':
            case 'ship_awbno':
			case 'ship_service':
			case 'ship_date':
			case 'ship_origin':
			case 'ship_destination':
			case 'ship_shipper':
			case 'ship_consignee':
			case 'ship_status':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }
    function column_ship_awbno($item){
        $actions = array(
			'details'    => sprintf('<a href="?page=%s&id=%s&details=1">Update</a>','tracking_add',$item['ship_id']),
            'edit'      => sprintf('<a href="?page=%s&id=%s">Edit</a>','tracking_add',$item['ship_id']),	
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ship_id']),
		
        );
        return sprintf('%1$s %3$s',
            /*$1%s*/ $item['ship_awbno'],
            /*$2%s*/ $item['ship_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    function column_cb($item){
        return sprintf(
           '<input type="checkbox" name="id[]" value="%s" />',
            /*$2%s*/ $item['ship_id']                
        );
    }
    function get_columns(){
        $columns = array(
            'cb' => $this->column_cb,
            'ship_awbno' => 'No.AWB',
            'ship_service' => 'Service',
			'ship_date'=> 'Date',
			'ship_origin' => 'Origin',
			'ship_destination' => 'Destination',
			'ship_consignee' => 'Consignee',
			'ship_status' => 'Status',
        );
        return $columns;
    }
    function get_sortable_columns() {
        $sortable_columns = array(
            'ship_awbno'     => array('ship_awbno',false),
            'ship_date'    => array('ship_date',false)
        );
        return $sortable_columns;
    }
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    function process_bulk_action() {
	    global $wpdb;
        $table_name = $wpdb->prefix . 'shipment'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE ship_id IN($ids)");
            }
        }	
		
    }
    function prepare_items($search = NULL) {
        global $wpdb; 
		
        $per_page = 10;
        $columns = $this->get_columns();
        $hidden = array();
		
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
		
		$table_name = $wpdb->prefix . "shipment";
		$retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE ship_parent='0'");
		
		if($search != NULL){
			$search = trim($search);
			$retrieve_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE ship_parent='0' AND ship_awbno LIKE '%%%s%%'", $search, $search));			
		}
		
		$data=array();
		foreach ($retrieve_data as $querydatum ){
			$querydatum->ship_status =  tracking_status($querydatum->ship_status);
			array_push($data, (array) $querydatum);
		}
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ship_id';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='desc') ? $result : -$result;
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
        ) );
    }   
}
function Tracking_add_menu_items(){
    add_menu_page('Tracking App', 'NMC Shippment International','activate_plugins', 'tracking_list', 'Tracking_render_list_page');
	add_submenu_page('tracking_list', 'Shipment Data ','Shipment Data ','activate_plugins', 'tracking_list', 'Tracking_render_list_page');
	add_submenu_page('tracking_list', 'Add New Data ','Add New Data ','activate_plugins', 'tracking_add', 'tracking_form_page_handler');
} 
add_action('admin_menu', 'Tracking_add_menu_items');
function Tracking_render_list_page(){
    $testListTable = new Tracking_List_Table();
	$message = '';
    if ('delete' === $testListTable->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'tracking'), count($_REQUEST['id'])) . '</p></div>';
    }
	
    if( isset($_GET['s']) ){
                $testListTable->prepare_items($_GET['s']);
        } else {
                $testListTable->prepare_items();
        }
?>

<div class="wrap">
  <div id="icon-edit" class="icon32"></div>
  <h2>Shipment International Tracking App<a href="admin.php?page=tracking_add" class="add-new-h2">Add New</a></h2>
  <form id="tracking-filter" method="get">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php 
		$testListTable->search_box( 'Search', 'ship_awbno' );
		$testListTable->display();
	?>
  </form>
</div>
<?php
}
function tracking_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'shipment'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'ship_id' => 0,
        'ship_awbno' => '',
        'ship_service' => '',
        'ship_date' => date("Y-m-d"),
		'ship_origin' => '',
		'ship_destination' => '',
		
		'ship_shipper' => '',
		'ship_shipper_address' => '',
		'ship_shipper_city' => '',
		'ship_shipper_province' => '',
		'ship_shipper_country' => '',
		'ship_shipper_zipcode' => '',
		'ship_shipper_phone' => '',
		
		'ship_consignee' => '',
		'ship_consignee_address' => '',
		'ship_consignee_city' => '',
		'ship_consignee_province' => '',
		'ship_consignee_country' => '',
		'ship_consignee_zipcode' => '',
		'ship_consignee_phone' => '',
		
		'ship_parent' => '0',
		'ship_datetime' => date("Y-m-d H:i:s"),
		'ship_citycountry' => '',
		'ship_status' => '',
		'ship_receiver' => ''
		
    );
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = tracking_validate_tracking($item);
		
        if ($item_valid === true) {
            if ($item['ship_id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['ship_id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'tracking');
                } else {
                    $notice = __('There was an error while saving item', 'tracking');
                }
            } else {
				if (isset($_REQUEST['details_add'])){
					if ($item['ship_status']=="4"){
						$item['ship_receiver'] = $item['ship_receiver'];
					}else{
						$item['ship_receiver'] = "-";
						}
					$result = $wpdb->query($wpdb->prepare("UPDATE $table_name SET ship_datetime = %s, ship_status = %s, ship_receiver = %s WHERE ship_id = %d", $item['ship_datetime'],$item['ship_status'],$item['ship_receiver'], $item['ship_id']));
					$result = $wpdb->query($wpdb->prepare("INSERT INTO $table_name (ship_parent, ship_datetime, ship_citycountry, ship_status,ship_receiver) VALUES (%d, %s, %s, %s, %s)", $item['ship_id'], $item['ship_datetime'], $item['ship_citycountry'],$item['ship_status'], $item['ship_receiver']), ARRAY_A);				
					if ($result) {
						$message = __('Item was successfully saved', 'tracking');
					} else {
						$notice = __('There was an error while saving item<br/>'.$wpdb->show_errors().'<br />'.$wpdb->print_error(), 'tracking');
					}
				}else {				
                	$result = $wpdb->update($table_name, $item, array('ship_id' => $item['ship_id']));
					if ($result) {
						$message = __('Item was successfully updated', 'tracking');
					} else {
						$notice = __('There was an error while updating item', 'tracking');
					}
				}
            }
        } else {
            $notice = $item_valid;
        }
    } else {
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ship_id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'tracking');

            }
        }
    }
	add_meta_box('trackings_form_meta_box', 'Tracking data', 'tracking_form_meta_box_handler', 'tracking', 'normal', 'default');
	
?>
<div class="wrap">
  <div class="icon32 icon32-posts-post" id="icon-edit"><br>
  </div>
  <h2>
    <?php _e('Shipment International Tracking App', 'tracking')?>
    <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tracking_list');?>">
    <?php _e('back to list', 'tracking')?>
    </a></h2>
  <?php if (!empty($notice)): ?>
  <div id="notice" class="error">
    <p><?php echo $notice ?></p>
  </div>
  <?php endif;?>
  <?php if (!empty($message)): ?>
  <div id="message" class="updated">
    <p><?php echo $message ?></p>
  </div>
  <?php endif;?>
  <form id="form" method="POST">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
    <div class="metabox-holder" id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <?php do_meta_boxes('tracking', 'normal', $item); ?>
        </div>
      </div>
    </div>
  </form>
  <style>
	  .form-table th {vertical-align:top; text-align:left; padding:10px; width:120px; font-weight:bold;}
	  .form-table td {margin-bottom:9px; padding:8px 0px; line-height:20px; font-size:12px;}
	  input[type=text],textarea{width:95%;}
  </style>
</div>
<?php
}
function tracking_form_meta_box_handler($item){ 
if (isset($_REQUEST['details_add'])){ ?>
<input type="hidden" name="ship_id" value="<?php echo $item['ship_id'] ?>"/>
<input id="ship_awbno" name="ship_awbno" type="hidden" style="width: 95%" value="<?php echo esc_attr($item['ship_awbno'])?>" size="50" required>
<table cellspacing="2" cellpadding="5" class="form-table">
  <tbody>
    <tr>
      <th valign="top">Tracking Code AWB</th>
      <td>:</td>
      <td><?php echo esc_attr($item['ship_awbno'])?></td>
    </tr>
    <tr>
      <th valign="top">Shipment Date Delivery</th>
      <td valign="top">:</td>
      <td><input type="text" id="ship_datetime" name="ship_datetime" style="width: 95%" value="<?php echo esc_attr(date("Y-m-d H:i:s"))?>" /></td>
    </tr>
    <tr>
      <th valign="top">Shipment City</th>
      <td valign="top">:</td>
      <td><input type="text" id="ship_citycountry" name="ship_citycountry" style="width: 95%" value="<?php echo esc_attr($item['ship_citycountry'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Shipment Status</th>
      <td valign="top">:</td>
      <td><select name="ship_status" id="ship_status">
          <?php for ($i=0;$i<=6;$i++ ){?>
          <option value="<?php echo $i;?>"><?php echo tracking_status($i);?></option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <th valign="top">Receiver</th>
      <td valign="top">:</td>
      <td><input type="text" id="ship_receiver" name="ship_receiver" style="width: 95%" value="<?php echo esc_attr($item['ship_receiver'])?>" /></td>
    </tr>
    <tr>
      <td colspan="3" valign="top"><input type="submit" value="<?php _e('Save', 'tracking')?>" id="submit" class="button-primary" name="submit"></td>
    </tr>
  </tbody>
</table>
<?php }elseif (isset($_REQUEST['details'])){ ?>
<table cellpadding="10" width="100%" cellspacing="0" border="1" bordercolor="#F2F2F2">
  <tbody>
    <tr>
      <th colspan="3" valign="top"><span style="font-size:15pt;">Tracking Details</span></th>
    </tr>
    <tr>
      <th valign="top">Tracking Code AWB</th>
      <th>Date</th>
      <th>Service</th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <th valign="top"><?php echo esc_attr($item['ship_awbno'])?></th>
      <th><?php echo esc_attr($item['ship_date'])?></th>
      <th><?php echo esc_attr($item['ship_service'])?></th>
    </tr>
    <tr>
      <th valign="top">Origin</th>
      <th colspan="2">Destination</th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <th valign="top"><?php echo esc_attr($item['ship_origin'])?></th>
      <th colspan="2"><?php echo esc_attr($item['ship_destination'])?></th>
    </tr>
    <tr>
      <th valign="top">Shipper</th>
      <th colspan="2">Consignee</th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <th valign="top"><span style="font-size:12pt;"><?php echo esc_attr($item['ship_shipper'])?></span><br />
      <?php echo esc_attr($item['ship_shipper_address'])?><br />
      <?php echo esc_attr($item['ship_shipper_city'])?> - <?php echo esc_attr($item['ship_shipper_province'])?> <br />
	 <?php echo get_countryname(esc_attr($item['ship_shipper_country']));?> - <?php echo esc_attr($item['ship_shipper_zipcode'])?><br />
     Phone : <?php echo esc_attr($item['ship_shipper_phone'])?>
     </th>
      <th colspan="2"><span style="font-size:12pt;"><?php echo esc_attr($item['ship_consignee'])?></span><br />
      <?php echo esc_attr($item['ship_consignee_address'])?><br />
      <?php echo esc_attr($item['ship_consignee_city'])?> - <?php echo esc_attr($item['ship_consignee_province'])?> <br />
	 <?php echo get_countryname(esc_attr($item['ship_consignee_country']));?> - <?php echo esc_attr($item['ship_consignee_zipcode'])?><br />
     Phone : <?php echo esc_attr($item['ship_consignee_phone'])?></th>
    </tr>
    <tr>
      <th align="center" colspan="3"><span style="font-size:15pt;">Shipment Status</span></th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <th align="center">Procces Date</th>
      <th align="center">City</th>
      <th align="center">Status</th>
    </tr>
    <?php global $wpdb;
      $table_name = $wpdb->prefix . 'shipment'; // do not forget about tables prefix
	  $result = $wpdb->get_results("SELECT * FROM $table_name WHERE ship_parent = ".$item['ship_id']." ORDER BY ship_id ASC");
	  if ($result) {
	  foreach($result as $row){ ?>
    <tr style="background-color:#E5E5E5;">
      <td align="center"><?php echo $row->ship_datetime;?></td>
      <td align="center"><?php echo $row->ship_citycountry;?></td>
      <td align="center"><?php echo tracking_status($row->ship_status);?></td>
    </tr>
    <?php }} else{?>
    <tr style="background-color:#E5E5E5;">
      <td align="center" colspan="3">No Data</td>
    </tr>
    <?php } ?>
    <tr style="background-color:#E5E5E5;">
      <td align="left" colspan="3"><input type="button" onclick="location.href='admin.php?page=tracking_add&id=<?php echo $item['ship_id'];?>&details_add=1';" value="Add Details Shipments" id="submit" class="button-primary" name="submit"></td>
    </tr>
  </tbody>
</table>
<?php }else{ ?>
<input type="hidden" name="ship_id" value="<?php echo $item['ship_id'] ?>"/>
<table cellspacing="2" cellpadding="5" class="form-table">
  <tbody>
    <tr>
      <th valign="top">Tracking Code AWB</th>
      <td>:</td>
      <td><input id="ship_awbno" name="ship_awbno" type="text"  value="<?php echo esc_attr($item['ship_awbno'])?>" required></td>
    </tr>
    <tr>
      <th valign="top">Service</th>
      <td>:</td>
      <td><input id="ship_service" name="ship_service" type="text"  value="<?php echo esc_attr($item['ship_service'])?>" required></td>
    </tr>
    <tr>
      <th valign="top">Date</th>
      <td>:</td>
      <td><input id="ship_date" name="ship_date" type="text"  value="<?php echo esc_attr($item['ship_date'])?>" required></td>
    </tr>
    <tr>
      <th valign="top">Origin</th>
      <td>:</td>
      <td><input id="ship_origin" name="ship_origin" type="text"  value="<?php echo esc_attr($item['ship_origin'])?>" required></td>
    </tr>
    <tr>t
      <th valign="top">Destination</th>
      <td>:</td>
      <td><input id="ship_destination" name="ship_destination" type="text"  value="<?php echo esc_attr($item['ship_destination'])?>" required></td>
    </tr>
    <tr><th colspan="3"><span style="font-size:14pt;">Shipper Detail</span></th></tr>
    <tr>
      <th valign="top">Name</th>
      <td valign="top">:</td>
      <td><input id="ship_shipper" type="text" name="ship_shipper"  value="<?php echo esc_attr($item['ship_shipper'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Address</th>
      <td valign="top">:</td>
      <td><textarea id="ship_shipper_address" name="ship_shipper_address"><?php echo esc_attr($item['ship_shipper_address'])?></textarea></td>
    </tr>
    <tr>
      <th valign="top">City</th>
      <td valign="top">:</td>
      <td><input id="ship_shipper_city" type="text" name="ship_shipper_city"  value="<?php echo esc_attr($item['ship_shipper_city'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Province</th>
      <td valign="top">:</td>
      <td><input id="ship_shipper_province" type="text" name="ship_shipper_province"  value="<?php echo esc_attr($item['ship_shipper_province'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Country</th>
      <td valign="top">:</td>
      <td><?php echo get_combo_country("ship_shipper_country", esc_attr($item['ship_shipper_country']));?></td>
    </tr>
    <tr>
      <th valign="top">Postal Code</th>
      <td valign="top">:</td>
      <td><input id="ship_shipper_zipcode" type="text" name="ship_shipper_zipcode"  value="<?php echo esc_attr($item['ship_shipper_zipcode'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Phone Number</th>
      <td valign="top">:</td>
      <td><input id="ship_shipper_phone" type="text" name="ship_shipper_phone"  value="<?php echo esc_attr($item['ship_shipper_phone'])?>" /></td>
    </tr>
    <tr><th colspan="3"><span style="font-size:14pt;">Consignee Details</span></th></tr>
    <tr>
      <th valign="top">Name</th>
      <td valign="top">:</td>
      <td><input id="ship_consignee" name="ship_consignee" type="text" value="<?php echo esc_attr($item['ship_consignee'])?>" /></td>
    </tr>
   <tr>
      <th valign="top">Address</th>
      <td valign="top">:</td>
      <td><textarea id="ship_consignee_address" name="ship_consignee_address"><?php echo esc_attr($item['ship_consignee_address'])?></textarea></td>
    </tr>
    <tr>
      <th valign="top">City</th>
      <td valign="top">:</td>
      <td><input id="ship_consignee_city" type="text" name="ship_consignee_city"  value="<?php echo esc_attr($item['ship_consignee_city'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Province</th>
      <td valign="top">:</td>
      <td><input id="ship_consignee_province" type="text" name="ship_consignee_province"  value="<?php echo esc_attr($item['ship_consignee_province'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Country</th>
      <td valign="top">:</td>
      <td><?php echo get_combo_country("ship_consignee_country", esc_attr($item['ship_consignee_country']));?></td>
    </tr>
    <tr>
    <th>Postal Code</th>
      <td valign="top">:</td>
      <td><input id="ship_consignee_zipcode" type="text" name="ship_consignee_zipcode"  value="<?php echo esc_attr($item['ship_consignee_zipcode'])?>" /></td>
    </tr>
    <tr>
      <th valign="top">Phone Number</th>
      <td valign="top">:</td>
      <td><input id="ship_consignee_phone" type="text" name="ship_consignee_phone"  value="<?php echo esc_attr($item['ship_consignee_phone'])?>" /></td>
    </tr>
    <tr>
      <td colspan="3" valign="top"><input type="submit" value="<?php _e('Save', 'tracking')?>" id="submit" class="button-primary" name="submit"></td>
    </tr>
  </tbody>
</table>
<?php
  }
}
function tracking_status($status){
	switch ($status){
		case 0: $statuss="On Process";break;
		case 1: $statuss="Manifested";break;
		case 2: $statuss="On Transit";break;
		case 3: $statuss="Received On Destination";break;
		case 4: $statuss="Delivered";break;
		case 5: $statuss="Frozen";break;
		case 6: $statuss="Restored";break;
	}
	return $statuss;
}
function tracking_validate_tracking($item)
{
    $messages = array();
    if (empty($item['ship_awbno'])) $messages[] = __('Code AWB is required', 'tracking');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}
function tracking_search($atts){
extract( shortcode_atts( array(
		'form' => '',
		'act' => '',
), $atts ) );
ob_start();
?>
<form method="post" action="<?php echo $act;?>">
  <table cellpadding="10" cellspacing="0" width="100%" class="aqua_table">
    <tr>
      <th valign="top">Trace and Tracking</th>
    </tr>
    <tr>
      <td>Please enter Number AWB <br />
        Then Click 'Search'<br /><textarea id="ship_awbno" name="ship_awbno" style="width: 95%" rows="3"></textarea>
      <input id="submit" name="submit" value="Search" type="submit"></td>
    </tr>
  </table>
</form>
<?php $output_string=ob_get_contents();
	ob_end_clean(); 
	return $output_string;	
}
function tracking_process($atts){
	extract( shortcode_atts( array(
		'proses' => '',
		'detilurl' => '',
		'sep' => '&',
), $atts ) );
    global $wpdb;
	$table_name = $wpdb->prefix . 'shipment'; // do not forget about tables prefix
	if (isset($_POST['ship_awbno'])){		
		$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE ship_parent='0' AND ship_awbno = '%s'",$_POST['ship_awbno'])); 
	}
	?>
<table border="1" cellpadding="10" cellspacing="0" class="aqua_table">
  <tr>
    <th>No. AWB</th>
    <th>Destination</th>
    <th>Consignee</th>
    <th>Date Received</th>
    <th>Receiver</th>
    <th>Status</th>
  </tr>
  <?php foreach($result as $result){ ?>
  <tr>
    <td><a href="<?php echo $detilurl.$sep;?>awbno=<?php echo esc_attr($result->ship_awbno)?>" title="Details Tracking"><?php echo esc_attr($result->ship_awbno)?></a></td>
    <td><?php echo esc_attr($result->ship_destination)?></td>
    <td><?php echo esc_attr($result->ship_consignee)?></td>
    <td><?php echo esc_attr($result->ship_datetime)?></td>
    <td><?php echo esc_attr($result->ship_receiver)?></td>
    <td><?php echo esc_attr(tracking_status($result->ship_status))?></td>
  </tr>
  <?php } ?>
</table>
<?php 
}
function tracking_details(){
    global $wpdb;
	$table_name = $wpdb->prefix . 'shipment'; // do not forget about tables prefix
	if (isset($_POST['ship_awbno'])){		
		$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE ship_parent='0' AND ship_awbno = '%s'",$_POST['ship_awbno'])); 
	}
	if ($result){
		ob_start();
	  foreach($result as $result){ ?>
<style>.aqua_table th {text-align: center;}</style>
<table cellpadding="10" width="100%" cellspacing="0" border="1" bordercolor="#F2F2F2" class="aqua_table">
  <tbody>
    <tr>
      <th colspan="3" valign="top"><span style="font-size:15pt;">Tracking Details</span></th>
    </tr>
    <tr>
      <th valign="top">Tracking Code AWB</th>
      <th>Date</th>
      <th>Service</th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <td align="center"><?php echo esc_attr($result->ship_awbno)?></td>
      <td align="center"><?php echo esc_attr($result->ship_date)?></td>
      <td align="center"><?php echo esc_attr($result->ship_service)?></td>
    </tr>
    <tr>
      <th valign="top">Origin</th>
      <th colspan="2">Destination</th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <td align="center"><?php echo esc_attr($result->ship_origin)?></td>
      <td colspan="2" align="center"><?php echo esc_attr($result->ship_destination)?></td>
    </tr>
    <tr>
      <th valign="top">Shipper</th>
      <th colspan="2">Consignee</th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <td align="center"><strong><?php echo esc_attr($result->ship_shipper)?></strong><br />
<?php echo esc_attr($result->ship_shipper_address)?> <br />
<?php echo esc_attr($result->ship_shipper_city)?> - <?php echo esc_attr($result->ship_shipper_province)?><br />
<?php echo get_countryname(esc_attr($result->ship_shipper_country));?> - <?php echo esc_attr($result->ship_shipper_zipcode)?><br />
Phone : <?php echo esc_attr($result->ship_shipper_phone)?></td>
      <td colspan="2" align="center"><strong><?php echo esc_attr($result->ship_consignee)?></strong><br />
<?php echo esc_attr($result->ship_consignee_address)?> <br />
<?php echo esc_attr($result->ship_consignee_city)?> - <?php echo esc_attr($result->ship_consignee_province)?><br />
<?php echo get_countryname(esc_attr($result->ship_consignee_country));?> - <?php echo esc_attr($result->ship_consignee_zipcode)?><br />
Phone : <?php echo esc_attr($result->ship_consignee_phone)?></td>
    </tr>
    <tr>
      <th align="center" colspan="3"><span style="font-size:15pt;">Shipment Status</span></th>
    </tr>
    <tr style="background-color:#FFFFFF;">
      <th align="center">Procces Date</th>
      <th align="center">City</th>
      <th align="center">Status</th>
    </tr>
    <?php
	  $result2 = $wpdb->get_results("SELECT * FROM $table_name WHERE ship_parent = ".$result->ship_id." ORDER BY ship_id ASC");
	  if ($result2) {
		foreach($result2 as $row){ 
		$city = $row->ship_citycountry;
		?>
    <tr style="background-color:#E5E5E5;">
      <td align="center"><?php echo $row->ship_datetime;?></td>
      <td align="center"><?php echo $row->ship_citycountry;?></td>
      <td align="center"><?php echo tracking_status($row->ship_status);?></td>
    </tr>
    <?php }} else{?>
    <tr style="background-color:#E5E5E5;">
      <td align="center" colspan="3">No Data</td>
    </tr>
    <?php } ?>
    <?php if ($city){ ?>
    <tr>
      <td colspan="3">
              <div class="sixteen columns" style="width: 890px;">
                <div id="google_map">
                  <iframe src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo urlencode($city); ?>&amp;aq=&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo urlencode($city); ?>&amp;t=m&amp;z=14&amp;output=embed"></iframe>
                </div>
            </div>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<?php }

$output_string=ob_get_contents();
	ob_end_clean(); 
	}
	return $output_string;	
}
add_shortcode( 'tracking_search', 'tracking_search' );
add_shortcode( 'tracking_process', 'tracking_process' );
add_shortcode( 'tracking_details', 'tracking_details' );
/*add country xml*/
function get_combo_country($name="country",$select=NULL){
$xml=simplexml_load_file( plugin_dir_path( __FILE__ )."country.xml");
?>
<select name="<?php echo $name;?>" id="<?php echo $name;?>">
<?php 
foreach($xml->country as $country){ 
if ($select == $country['countryCode']){
		$selected = "selected";
	}else{
		$selected = "";
	} ?> 
	<option value="<?php echo $country['countryCode'];?>" <?php echo $selected;?>><?php echo $country['countryName'];?></option>
<?php } ?>
</select>
<?php }
function get_countryname($n){
$xml=simplexml_load_file( plugin_dir_path( __FILE__ )."country.xml");
	foreach($xml->country as $country){ 
		if ($n == $country['countryCode']){
			return $country['countryName'];
		}		 
	} 
}
