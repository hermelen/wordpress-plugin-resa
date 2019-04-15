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
    wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' );
    wp_enqueue_style( 'full-calendar-style', get_template_directory_uri() . '/node_modules/fullcalendar/dist/fullcalendar.min.css');
    wp_enqueue_style( 'font-awesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css' );
    wp_enqueue_style( 'my-plugin-style', plugins_url( '/_inc/css/style.css', __FILE__ ));

    wp_register_script( 'my-plugin-script', plugins_url( '/_inc/app.js', __FILE__ ));
    wp_register_script( 'my-plugin-ajax', plugins_url( '/_inc/ajax.js', __FILE__ ));

  	wp_register_script( 'moment', get_template_directory_uri() . '/node_modules/moment/min/moment.min.js', array(), '20181211', true );
  	wp_register_script( 'full-calendar-scripts', get_template_directory_uri() . '/node_modules/fullcalendar/dist/fullcalendar.min.js', array(), '20181211', true );
  	wp_register_script( 'full-calendar-locale-scripts', get_template_directory_uri() . '/node_modules/fullcalendar/dist/locale/fr.js', array(), '20181211', true );
}

function my_plugin_admin_scripts() {
    wp_enqueue_script( 'my-plugin-script' );
    wp_enqueue_script( 'my-plugin-ajax' );
    wp_enqueue_script( 'moment' );
    wp_enqueue_script( 'full-calendar-scripts' );
    wp_enqueue_script( 'full-calendar-locale-scripts' );
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
    add_action('wp_loaded', array($this, 'delete_day'));
    add_action('wp_loaded', array($this, 'delete_resa'));




    // register_uninstall_hook(__FILE__, array('RoomDatingPlugin', 'uninstall'));
    register_deactivation_hook(__FILE__, array('Resa', 'uninstall'));
  }

  public function add_admin_menu()
  {
    $resa = add_menu_page('Réservations', 'Resa', 'manage_options', 'roomdating', array($this, 'resa_html'));
    $manage = add_submenu_page('roomdating', 'Gestion des réservations', 'Gérer', 'manage_options', 'manageroomdating', array($this, 'manage_html'));
    $export = add_submenu_page('roomdating', 'Export.xls', 'Export', 'manage_options', 'exportroomdating', array($this, 'export_html'));
    add_action('admin_print_scripts-' . $resa, 'my_plugin_admin_scripts');
    add_action('admin_print_scripts-' . $manage, 'my_plugin_admin_scripts');
    add_action('admin_print_scripts-' . $export, 'my_plugin_admin_scripts');
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
    if (isset($_POST['resa_firstname']) && !empty($_POST['resa_firstname'])) { $firstname = $_POST['resa_firstname']; };
    if (isset($_POST['resa_lastname']) && !empty($_POST['resa_lastname'])) { $lastname = $_POST['resa_lastname']; };
    if (isset($_POST['resa_phone']) && !empty($_POST['resa_phone'])) { $phone = $_POST['resa_phone']; };
    if (isset($_POST['resa_email']) && !empty($_POST['resa_email'])) { $email = $_POST['resa_email'];
    if (isset($_POST['resa_id']) && !empty($_POST['resa_id'])) { $id = $_POST['resa_id']; };
      global $wpdb;
      if (isset($id) && !empty($id)) {
        $wpdb->update(
          "{$wpdb->prefix}resa_user",
          array(
            'firstname'=>$firstname,
            'lastname' =>$lastname,
            'email'    => $email,
            'phone'    =>$phone
          ),
          array( 'id' => $id )
        );
      } else {
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
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) { $user_id = $_POST['user_id'];
    };
    if (isset($_POST['room_id']) && !empty($_POST['room_id'])) { $room_id = $_POST['room_id'];
      global $wpdb;
      $wpdb->insert("{$wpdb->prefix}resa", array(
        'booked'=>0,
        'room_id'=>$room_id,
        'user_id'=>$user_id
      ));
    };
  }

  public function delete_resa()
  {
    if (isset($_POST['resa_id']) && !empty($_POST['resa_id'])) { $resa_id = $_POST['resa_id'];
    };
    if (isset($_POST['delete_day']) && !empty($_POST['delete_day'])) { $delete_day = $_POST['delete_day'];
      global $wpdb;
      $wpdb->delete( "{$wpdb->prefix}resa", array( 'id' => $resa_id ) );
    };
  }

  public function save_day()
  {
    if (isset($_POST['thedate']) && !empty($_POST['thedate'])) {
      foreach ($_POST['thedate'] as $key => $value) {
        if (isset($_POST['resa_id'][$key]) && !empty($_POST['resa_id'][$key])) { $resa_id = $_POST['resa_id'][$key]; };
        if (isset($_POST['persons'][$key]) && !empty($_POST['persons'][$key])) { $persons = $_POST['persons'][$key]; } else { $persons = 0; };
        if (isset($_POST['dinner'][$key]) && !empty($_POST['dinner'][$key])) { $dinner = $_POST['dinner'][$key]; } else { $dinner = 0; };
        if (isset($_POST['lunch'][$key]) && !empty($_POST['lunch'][$key])) { $lunch = $_POST['lunch'][$key]; } else { $lunch = 0; };
        if (isset($_POST['breakfast'][$key]) && !empty($_POST['breakfast'][$key])) { $breakfast = $_POST['breakfast'][$key]; } else { $breakfast = 0; };
        if (isset($_POST['note'][$key]) && !empty($_POST['note'][$key])) { $note = $_POST['note'][$key]; } else { $note = 0; };
        if (isset($_POST['id'][$key]) && !empty($_POST['id'][$key])) { $id = $_POST['id'][$key]; };
        if (isset($_POST['thedate'][$key]) && !empty($_POST['thedate'][$key])) {
          $thedate = $_POST['thedate'][$key];
          global $wpdb;
          if (isset($id) && !empty($id)) {
            $wpdb->update(
              "{$wpdb->prefix}resa_day",
              array(
                'persons'   => $persons,
                'breakfast' => $breakfast,
                'lunch'     => $lunch,
                'dinner'    => $dinner,
                'note'      => $note
              ),
              array( 'id' => $id )
            );
          } else {
            $this_resa = $wpdb->get_row(
              "SELECT room_id, user_id
              FROM {$wpdb->prefix}resa
              WHERE id = $resa_id"
            );
            $room_id = $this_resa->room_id;
            if (isset(get_post_meta( $room_id )['chambre'])) {
              $exist_days = $wpdb->get_results(
                "SELECT *
                FROM {$wpdb->prefix}resa_day as day
                JOIN {$wpdb->prefix}resa as resa
                ON day.resa_id = resa.id
                WHERE thedate = \"$thedate\"
                AND resa.room_id = $room_id"
              );
              if (!empty($exist_days)) {
                $the_room_name = $wpdb->get_row(
                  "SELECT post_title
                  FROM {$wpdb->prefix}posts
                  WHERE id = $room_id"
                ); ?>
                <?php
                foreach ($exist_days as $exist_day) {
                  $date = new DateTime($exist_day->thedate); ?>
                  <section class="alert"  style="color: white; background: red; margin: 0 15% 0 15%">
                    <p>En date du <?= $date->format('d/m/Y') ?>, une réservation pour le <?= $the_room_name->post_title ?> est déjà enregistrée.</p>
                    <p>Toutes les dates antérieures ont été annulée.</p>
                  </section>
                  <?php
                }
                $wpdb->delete( "{$wpdb->prefix}resa_day", array( 'resa_id' => $resa_id ) );
                $wpdb->update( "{$wpdb->prefix}resa", array( 'booked' => 0 ), array( 'id' => $resa_id ) );
              } else {
                $wpdb->insert("{$wpdb->prefix}resa_day", array(
                  'resa_id'   => $resa_id,
                  'note'      => $note,
                  'dinner'    => $dinner,
                  'lunch'     => $lunch,
                  'breakfast' => $breakfast,
                  'persons'   => $persons,
                  'thedate'   => $thedate
                ));
              }
            } elseif (isset(get_post_meta( $room_id )['dortoir'])) {
              $exist_days = $wpdb->get_results(
                "SELECT *
                FROM {$wpdb->prefix}resa_day as day
                JOIN {$wpdb->prefix}resa as resa
                ON day.resa_id = resa.id
                WHERE day.thedate = \"$thedate\"
                AND resa.room_id = $room_id"
              );
              if (!empty($exist_days)) {
                foreach ($exist_days as $exist_day) {
                  $room_data = $wpdb->get_row(
                    "SELECT bed, resa_id
                    FROM {$wpdb->prefix}resa_day
                    WHERE resa_id = $exist_day->id"
                  );
                  (int)$previous_beds = $room_data->bed;
                  (int)$previous_resa_id = $room_data->resa_id;
                  $bed = $previous_beds - $persons;
                  $wpdb->insert("{$wpdb->prefix}resa_day", array(
                    'resa_id'   => $resa_id,
                    'note'      => $note,
                    'dinner'    => $dinner,
                    'lunch'     => $lunch,
                    'breakfast' => $breakfast,
                    'persons'   => $persons,
                    'thedate'   => $thedate,
                    'bed'       => $bed
                  ));
                  $wpdb->update("{$wpdb->prefix}resa_day",
                    array(
                      'bed'       => $bed
                    ),
                    array(
                      'resa_id' => $previous_resa_id,
                      'thedate' => $thedate
                    )
                  );
                }
              } else {
                (int)$total_bed = get_post_meta( $room_id )['lits'][0];
                $bed = $total_bed - $persons;
                $wpdb->insert("{$wpdb->prefix}resa_day", array(
                  'resa_id'   => $resa_id,
                  'note'      => $note,
                  'dinner'    => $dinner,
                  'lunch'     => $lunch,
                  'breakfast' => $breakfast,
                  'persons'   => $persons,
                  'thedate'   => $thedate,
                  'bed'       => $bed
                ));
              }
            }
          }
        }
        if (isset($resa_id) && !empty($resa_id)) {
          $wpdb->update("{$wpdb->prefix}resa", array('booked'=>1), array('id'=>$_POST['resa_id'][0]));
        }
      }
    }
  }

  public function delete_day()
  {
    if (isset($_POST['delete_day_id']) && !empty($_POST['delete_day_id'])) {
      $delete_day_id = $_POST['delete_day_id'];
      global $wpdb;
      $wpdb->delete( "{$wpdb->prefix}resa_day", array( 'id' => $delete_day_id ) );
      if (isset($_POST['delete_resa_id']) && !empty($_POST['delete_resa_id'])) {
        $delete_resa_id = $_POST['delete_resa_id'];
        $wpdb->delete( "{$wpdb->prefix}resa", array( 'id' => $delete_resa_id ) );
      };
    };
  }
}

new RoomDatingPlugin();
