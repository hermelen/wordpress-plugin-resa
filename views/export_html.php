<?php
global $wpdb;
$day = getdate();

$month = $day['mon'];
$year = $day['year'];

$number = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31

$days=array();

for($d=1; $d<=$number; $d++)
{
    $time=mktime(12, 0, 0, $month, $d, $year);
    if (date('m', $time)==$month) {
      $days[]=date('Y-m-d', $time);
    }
};

// Convertit une date ou un timestamp en français
function dateToFrench($date, $format)
{
    $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $french_days = array('lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim');
    $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
    return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
}

$rooms = get_posts( array(
  'meta_key' => 'page',
  'post_type' => 'page',
  'post_status' => 'publish',
  'meta_value' => 'chambre'
) );
?>
<table class="excel">
  <tr>
    <th></th>
    <th>Service</th> <?php
    foreach ($days as $key=>$day) {
      $fr_day = dateToFrench($day,'l d F');
      ?>
      <th class="<?= $key ?>"><?= $fr_day ?></th>
    <?php } ?>
  </tr>

<?php
foreach ($rooms as $room) { ?>
  <tr>
    <td><?= $room->post_title ?></td>
    <td>Couchage</td>
    <?php foreach ($days as $key=>$day) { ?>

      <?php $resa = $wpdb->get_row(
        "SELECT persons
        FROM {$wpdb->prefix}resa_day as day
        JOIN {$wpdb->prefix}resa as resa
        ON day.resa_id = resa.id
        WHERE resa.room_id = $room->ID
        AND day.thedate = \"$day\""
      ); ?>
        <td class="persons-<?= $key ?>"><?= (!empty($resa)) ? $resa->persons : ""; ?></td>
    <?php } ?>
  </tr>
  <tr>
    <td></td>
    <td>Petit-déj</td>
    <?php foreach ($days as $key=>$day) { ?>

      <?php $resa = $wpdb->get_row(
        "SELECT breakfast
        FROM {$wpdb->prefix}resa_day as day
        JOIN {$wpdb->prefix}resa as resa
        ON day.resa_id = resa.id
        WHERE resa.room_id = $room->ID
        AND day.thedate = \"$day\""
      ); ?>
        <td class="breakfast-<?= $key ?>"><?= (!empty($resa)) ? $resa->breakfast : ""; ?></td>
    <?php } ?>
  </tr>
  <tr>
    <td></td>
    <td>Déj</td>
    <?php foreach ($days as $key=>$day) { ?>

      <?php $resa = $wpdb->get_row(
        "SELECT lunch
        FROM {$wpdb->prefix}resa_day as day
        JOIN {$wpdb->prefix}resa as resa
        ON day.resa_id = resa.id
        WHERE resa.room_id = $room->ID
        AND day.thedate = \"$day\""
      ); ?>
        <td class="lunch-<?= $key ?>"><?= (!empty($resa)) ? $resa->lunch : ""; ?></td>
    <?php } ?>
  </tr>
  <tr>
    <td></td>
    <td>Dîner</td>
    <?php foreach ($days as $key=>$day) { ?>

      <?php $resa = $wpdb->get_row(
        "SELECT dinner
        FROM {$wpdb->prefix}resa_day as day
        JOIN {$wpdb->prefix}resa as resa
        ON day.resa_id = resa.id
        WHERE resa.room_id = $room->ID
        AND day.thedate = \"$day\""
      ); ?>
        <td class="dinner-<?= $key ?>"><?= (!empty($resa)) ? $resa->dinner : ""; ?></td>
    <?php } ?>
  </tr>
<?php } ?>
<tr id="total-persons">
  <td></td>
  <td>Couchages</td>
</tr>
<tr id="total-breakfast">
  <td></td>
  <td>Petit-déj</td>
</tr>
<tr id="total-lunch">
  <td></td>
  <td>Déj</td>
</tr>
<tr id="total-dinner">
  <td></td>
  <td>Dîner</td>
</tr>
</table>
