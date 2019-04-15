<?php
global $wpdb;

$this_monday = strtotime( "previous monday" );
$this_sunday = strtotime("6 day", $this_monday);

$monday_s_less_1 = strtotime("-7 day", $this_monday);
$sunday_s_less_1 = strtotime("-7 day", $this_sunday);

$monday_s_plus_1 = strtotime("+7 day", $this_monday);
$sunday_s_plus_1 = strtotime("+7 day", $this_sunday);

$monday_s_plus_2 = strtotime("+14 day", $this_monday);
$sunday_s_plus_2 = strtotime("+14 day", $this_sunday);

$monday_s_plus_3 = strtotime("+21 day", $this_monday);
$sunday_s_plus_3 = strtotime("+21 day", $this_sunday);
?>

<form action="#" method="post">
  <div class="form-row mb-4">
    <div class="col-8 col-sm-6 col-md-4">
      <select class="form-control" name="week" style="height: 100%;">
        <option value="<?= $monday_s_less_1 ?>"><?= date("d M",$monday_s_less_1) ?> - <?= date("d M",$sunday_s_less_1) ?></option>
        <option value="<?= $this_monday ?>"><?= date("d M",$this_monday) ?> - <?= date("d M",$this_sunday) ?></option>
        <option value="<?= $monday_s_plus_1 ?>"><?= date("d M",$monday_s_plus_1) ?> - <?= date("d M",$sunday_s_plus_1) ?></option>
        <option value="<?= $monday_s_plus_2 ?>"><?= date("d M",$monday_s_plus_2) ?> - <?= date("d M",$sunday_s_plus_2) ?></option>
        <option value="<?= $monday_s_plus_3 ?>"><?= date("d M",$monday_s_plus_3) ?> - <?= date("d M",$sunday_s_plus_3) ?></option>
      </select>
    </div>
    <input class="btn btn-primary col-4 col-sm-3 col-md-2" type="submit" name="" value="Selection">
  </div>
</form>


<?php
if (isset($_POST['week']) && !empty($_POST['week'])) {
  $day = date($_POST['week']);

  $days = [];

  for ($i=0; $i < 7; $i++) {
    $the_day = strtotime("+".$i." day", $day);
    $days[$i] = date('Y-m-d', $the_day);
  }

  // debug($days);


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
    'post_type' => 'page',
    'post_status' => 'publish',
    'meta_key' => 'roomdating',
  ) );
  ?>
  <iframe id="txtArea1" style="display:none"></iframe>


  <table class="excel table" id="headerTable">
    <thead class="thead-dark">
      <tr>
        <th><button class="btn btn-success" id="btnExport"><i class="fas fa-file-excel"></i> Export</button></th>
        <th>Service</th> <?php
        foreach ($days as $key=>$day) {
          $fr_day = dateToFrench($day,'l d F');
          ?>
          <th class="<?= $key ?>"><?= $fr_day ?></th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>

    <?php
    foreach ($rooms as $room) { ?>
      <tr>
        <td rowspan="5"><?= $room->post_title ?></td>
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
      <tr>
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
        <td>Petit-déj(lendemain)</td>
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
        <td>Déj(lendemain)</td>
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
        <td>Note</td>
        <?php foreach ($days as $key=>$day) { ?>

          <?php $resa = $wpdb->get_row(
            "SELECT note
            FROM {$wpdb->prefix}resa_day as day
            JOIN {$wpdb->prefix}resa as resa
            ON day.resa_id = resa.id
            WHERE resa.room_id = $room->ID
            AND day.thedate = \"$day\""
          ); ?>
            <td class="note-<?= $key ?>"><?= (!empty($resa)) ? $resa->note : ""; ?></td>
        <?php } ?>
      </tr>
    <?php } ?>
    <tr id="total-dinner">
      <td rowspan="2">Jour arrivée</td>
      <td>Dîner</td>
    </tr>
    <tr id="total-persons">
      <td>Couchages</td>
    </tr>
    <tr id="total-breakfast">
      <td rowspan="3">Lendemain</td>
      <td>Petit-déj</td>
    </tr>
    <tr id="total-lunch">
      <td>Déj</td>
    </tr>
    <tr id="total-note">
      <td>Notes</td>
    </tr>
  </tbody>
  </table>
<?php }
