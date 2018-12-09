<?php
/*
Plugin Name: Room Dating
Plugin URI: http://hermelen.com/wordpress/plugins/dating
Description: Plugin for room and meal reservation
Version: 0.1
Author: Hermelen PERIS
Author URI: http://hermelen.com
License: GPL2
*/ ?>
<?php
function my_plugin_admin_init() {
    wp_enqueue_style( 'my-plugin-style', plugins_url( '/_inc/style.css', __FILE__ ));
    wp_register_script( 'my-plugin-script', plugins_url( '/_inc/app.js', __FILE__ ));
    wp_register_script( 'my-plugin-ajax', plugins_url( '/_inc/ajax.js', __FILE__ ));
}

function my_plugin_admin_scripts() {
    wp_enqueue_script( 'my-plugin-script' );
    wp_enqueue_script( 'my-plugin-ajax' );
}

add_action('admin_init','my_plugin_admin_init');



include_once plugin_dir_path( __FILE__ ).'/roomdatingwidget.php';
include_once plugin_dir_path( __FILE__ ).'/resa.php';

class RoomDatingPlugin
{
  public function __construct() {
    add_action('widgets_init', function(){register_widget('RoomDatingWidget');});
    register_activation_hook(__FILE__, array('Resa', 'install'));
    add_action('admin_menu', array($this, 'add_admin_menu'), 20);
    add_action('wp_loaded', array($this, 'save_user'));
    add_action('wp_loaded', array($this, 'save_resa'));
    add_action('wp_loaded', array($this, 'save_day'));



    register_uninstall_hook(__FILE__, array('RoomDatingPlugin', 'uninstall'));
    // register_deactivation_hook(__FILE__, array('Resa', 'uninstall'));
  }

  public function add_admin_menu()
  {
    $resa = add_menu_page('Réservations', 'Resa', 'manage_options', 'roomdating', array($this, 'resa_html'));
    $manage = add_submenu_page('roomdating', 'Gestion des réservations', 'Gérer', 'manage_options', 'manageroomdating', array($this, 'manage_html'));
    $export = add_submenu_page('roomdating', 'Export.xls', 'Export', 'manage_options', 'exportroomdating', array($this, 'export_html'));
    // add_action('admin_print_scripts-' . $resa, 'my_plugin_admin_scripts');
    add_action('admin_print_scripts-' . $manage, 'my_plugin_admin_scripts');
    // add_action('admin_print_scripts-' . $export, 'my_plugin_admin_scripts');
  }


  public function resa_html()
  {
    include_once plugin_dir_path( __FILE__ ).'/views/resa_html.php';
  }

  public function manage_html()
  {
    include_once plugin_dir_path( __FILE__ ).'/views/manage_html.php';
  }

  public function export_html()
  {
    include_once plugin_dir_path( __FILE__ ).'/views/export_html.php';
  }

  public function save_user()
  {
    if (isset($_POST['firstname']) && !empty($_POST['firstname'])) { $firstname = $_POST['firstname']; };
    if (isset($_POST['lastname']) && !empty($_POST['lastname'])) { $lastname = $_POST['lastname']; };
    if (isset($_POST['phone']) && !empty($_POST['phone'])) { $phone = $_POST['phone']; };
    if (isset($_POST['email']) && !empty($_POST['email'])) { $email = $_POST['email'];
      global $wpdb;
      $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}resa_user WHERE email = '$email'");
      if (is_null($row)) {
        $wpdb->insert("{$wpdb->prefix}resa_user", array(
          'firstname'=>$firstname,
          'lastname'=>$lastname,
          'email' => $email,
          'phone'=>$phone
        ));
      }
    };

  }

  public function save_resa()
  {
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) { $user_id = $_POST['user_id']; };
    if (isset($_POST['room_id']) && !empty($_POST['room_id'])) { $room_id = $_POST['room_id'];
      global $wpdb;
      $wpdb->insert("{$wpdb->prefix}resa", array(
        'room_id'=>$room_id,
        'user_id'=>$user_id,
        'booked'=>0
      ));
    };
  }

  public function save_day()
  {
    if (isset($_POST['resa_id']) && !empty($_POST['resa_id'])) {
      foreach ($_POST['thedate'] as $key => $value) {
        if (isset($_POST['resa_id'][$key]) && !empty($_POST['resa_id'][$key])) { $resa_id = $_POST['resa_id'][$key]; };
        if (isset($_POST['dinner'][$key]) && !empty($_POST['dinner'][$key])) { $dinner = $_POST['dinner'][$key]; };
        if (isset($_POST['lunch'][$key]) && !empty($_POST['lunch'][$key])) { $lunch = $_POST['lunch'][$key]; };
        if (isset($_POST['breakfast'][$key]) && !empty($_POST['breakfast'][$key])) { $breakfast = $_POST['breakfast'][$key]; };
        if (isset($_POST['persons'][$key]) && !empty($_POST['persons'][$key])) { $persons = $_POST['persons'][$key]; };
        if (isset($_POST['thedate'][$key]) && !empty($_POST['thedate'][$key])) { $thedate = $_POST['thedate'][$key];
          global $wpdb;
          $wpdb->insert("{$wpdb->prefix}resa_day", array(
            'resa_id'   => $resa_id,
            'dinner'    => $dinner,
            'lunch'     => $lunch,
            'breakfast' => $breakfast,
            'persons'   => $persons,
            'thedate'   => $thedate
          ));
        };
      }
      $wpdb->update("{$wpdb->prefix}resa", array('booked'=>1), array('id'=>$_POST['resa_id'][0]));
    }
  }

  // $_POST['name'][$key]; // make something with it
  // $_POST['example'][$key];  // it will get the same index $key

}

new RoomDatingPlugin();
