<?php
global $wpdb;
$day = getdate();

$month = $day['mon'];
$year = $day['year'];

// echo $month."/".$year;


$number = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31

// echo $number;

$days=array();

for($d=1; $d<=$number; $d++)
{
    $time=mktime(12, 0, 0, $month, $d, $year);
    if (date('m', $time)==$month) {
      $days[]=date('Y-m-d', $time);
    }
};

$rooms = get_posts( array(
  'meta_key' => 'page',
  'post_type' => 'page',
  'post_status' => 'publish',
  'meta_value' => 'chambre'
) );
?>

<table>
  <tr>
    <th>Chambre</th>
    <th>Service</th>
    <?php
    foreach ($days as $day) { ?>
      <th><?= $day ?></th>
    <?php
    }
    ?>
  </tr>
    <?php
    foreach ($rooms as $room) {
      foreach ($days as $day) {
        $resa = $wpdb->get_row(
        "SELECT *
        FROM {$wpdb->prefix}resa_day as day
        JOIN {$wpdb->prefix}resa as resa
        ON day.resa_id = resa.room_id
        WHERE resa.room_id = $room->ID"  
        )?>
      ?>
      <tr>
        <td><?= $room->post_title ?></td>
        <td>Petit-déjeuner</td>
        <?php
        foreach ($days as $day) {
          $resa = $wpdb->get_row(
          "SELECT "
          )?>
          <td>
            <?php
            echo (!empty($day->breakfast)) ? $day->breakfast;
            ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td><?= $room->post_title ?></td>
        <td>Déjeuner</td>
        <?php
        foreach ($days as $day) { ?>
          <td>
            <?php
            !empty($day->breakfast): echo $day->lunch
            ?>
          </td>
        <?php } ?>
      </tr>
      <?php } ?>
</table>
