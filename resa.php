<?php
class Resa
{
  private $startdate;
  private $enddate;
  private $room;
  private $firstname;
  private $lastname;
  private $person;
  private $breakfast;
  private $lunch;
  private $dinner;
  private $booked;
  private $email;

  public function __construct($room, $start_date)
  {
    $this->setStartDate($start_date);
    $this->setRoom($room);
  }

  public static function install()
  {

    global $wpdb;

    $user_query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}resa_user (
      id INT AUTO_INCREMENT PRIMARY KEY,
      firstname VARCHAR(255) NOT NULL,
      lastname VARCHAR(255) NOT NULL,
      email VARCHAR(255),
      phone VARCHAR(255)
    )";
    // print_r($user_query);
    // wp_die();
    $wpdb->query($user_query);

    $resa_query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}resa (
      id INT AUTO_INCREMENT PRIMARY KEY,
      booked BOOLEAN,
      room_id BIGINT UNSIGNED,
      user_id INT,
      FOREIGN KEY (room_id) REFERENCES {$wpdb->prefix}posts (id),
      FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}resa_user (id)
    )";
    // print_r($resa_query);
    // wp_die();
    $wpdb->query($resa_query);

    $day_query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}resa_day (
        id INT AUTO_INCREMENT PRIMARY KEY,
        thedate DATE,
        persons INT NOT NULL,
        breakfast INT NOT NULL,
        lunch INT NOT NULL,
        dinner INT NOT NULL,
        resa_id INT NOT NULL,
        FOREIGN KEY (resa_id) REFERENCES {$wpdb->prefix}resa (id) )";
    // print_r($day_query);
    // wp_die();
    $wpdb->query($day_query);


  }

  public static function uninstall()
  {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}resa_day;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}resa;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}resa_user;");
  }

  public function setStartDate($start_date)
  {

  }

  public function setRoom($room)
  {

  }
}
?>
