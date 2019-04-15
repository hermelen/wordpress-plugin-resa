<?php
global $wpdb;
$resas = $wpdb->get_results(
  "SELECT id, room_id
  FROM {$wpdb->prefix}resa"
);

$booked_days = [];
$dormitories_bed = [];
for ($i=0; $i < count($resas) ; $i++) {

  $resa_id = $resas[$i]->id;
  $room_id = $resas[$i]->room_id;

  $available_beds = $wpdb->get_results(
    "SELECT bed
    FROM {$wpdb->prefix}resa_day
    WHERE resa_id = $resa_id
    ORDER BY thedate"
  );

  $room_title = $wpdb->get_row(
    "SELECT post_title
    FROM {$wpdb->prefix}posts
    WHERE ID = $room_id"
  );

  if (isset($available_beds[0]->bed) && !empty($available_beds[0]->bed)) {
    $dormitories = $wpdb->get_results(
      "SELECT thedate, bed
      FROM {$wpdb->prefix}resa_day
      WHERE resa_id = $resa_id
      ORDER BY thedate"
    );
    for ($j=0; $j < count($dormitories); $j++) {
      $singleDormitory = [
        $dormitories[$j]->thedate,
        $room_title->post_title,
        $dormitories[$j]->bed
      ];
      array_push($dormitories_bed, $singleDormitory);
    }
  } else {
    $booked_days[$i]['dates'] = $wpdb->get_results(
      "SELECT thedate
      FROM {$wpdb->prefix}resa_day
      WHERE resa_id = $resa_id
      ORDER BY thedate"
    );
    $booked_days[$i]['post_title'] = $room_title->post_title;
    $booked_days[$i]['bed'] = $available_beds[0]->bed;
  }
};

for ($i=0; $i < count($booked_days) ; $i++) {
  $first = reset($booked_days[$i]['dates']);
  $last = end($booked_days[$i]['dates']);
  $title = $booked_days[$i]['post_title'];
  $bed = $booked_days[$i]['bed'];
  $booked_days[$i] = [$first, $last, $title, $bed];
}

$booked_days=json_encode($booked_days);
$dormitories_bed=json_encode($dormitories_bed);
?>

<script type="text/javascript">
  var bookedDays = <?php echo $booked_days ?>;
  var availableBed = <?php echo $dormitories_bed ?>;
</script>
