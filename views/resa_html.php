<?php
global $wpdb;
$resas = $wpdb->get_results(
  "SELECT id, room_id
  FROM {$wpdb->prefix}resa"
);

$booked_days = [];
for ($i=0; $i < count($resas) ; $i++) {
  $resa_id = $resas[$i]->id;
  $booked_days[$i] = $wpdb->get_results(
    "SELECT thedate, resa_id
    FROM {$wpdb->prefix}resa_day
    WHERE resa_id = $resa_id
    ORDER BY thedate"
  );
  $room_id = $resas[$i]->room_id;
  $room_title = $wpdb->get_row(
    "SELECT post_title
    FROM {$wpdb->prefix}posts
    WHERE ID = $room_id"
  );
  print_r($room_title);
  array_push($booked_days[$i][0], $room_title);
  echo "<pre>";
  print_r($booked_days[$i][0]);
  echo "</pre>";
};



for ($i=0; $i < count($booked_days) ; $i++) {
  $resa_id = $booked_days[$i][$i]->resa_id;
  print_r($resa_id);
  $first = reset($booked_days[$i]);
  $last = end($booked_days[$i]);
  $title = $booked_days[$i][0]->post_title;
  $booked_days[$i] = [$first, $last, $title];
};

$booked_days=json_encode($booked_days); ?>



<h1><?= get_admin_page_title() ?></h1>
<p>Bienvenue sur la page d'accueil des réservations</p> <?php
global $wpdb;
$days = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_day ORDER BY resa_id DESC, thedate ASC"); ?>
<div>
  <div id="calendar-widget"></div>
</div>
<div>
  <table>
    <tr>
      <th>Nom</th>
      <th>Prénom</th>
      <th>Email</th>
      <th>Tél</th>
      <th>Chambre</th>
      <th>Date</th>
      <th>Personnes</th>
      <th>Petit-déj</th>
      <th>Déj</th>
      <th>Dîner</th>
      <th>Edition</th>
      <th>Suppression</th>
    </tr>
  <?php foreach ($days as $day) {
    $resa = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa WHERE id = $day->resa_id");
    $user_id = $resa[0]->user_id;
    $user = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_user WHERE id = $user_id"); ?>
    <tr>
      <td><?= $user[0]->lastname ?></td>
      <td><?= $user[0]->firstname ?></td>
      <td><?= $user[0]->email ?></td>
      <td><?= $user[0]->phone ?></td>
      <td><?= get_post($resa[0]->room_id)->post_title ?></td>
      <td><?= $day->thedate ?></td>
      <td><?= $day->persons ?></td>
      <td><?= $day->breakfast ?></td>
      <td><?= $day->lunch ?></td>
      <td><?= $day->dinner ?></td>
      <td><a href="#"><button>Modifier</button></a></td>
      <td><a href="#"><button>Supprimer</button></a></td>
    </tr>
  <?php } ?>
  </table>
</div>


<script type="text/javascript">
  var bookedDays = <?= $booked_days ?>;
  console.log(bookedDays);
</script>
