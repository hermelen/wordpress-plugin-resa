<?php
$rooms = get_posts( array(
  'meta_key' => 'page',
  'post_type' => 'page',
  'post_status' => 'publish',
  'meta_value' => 'chambre'
) );
$selectRooms = [];
foreach ($rooms as $room) {
  array_push($selectRooms, [$room->ID, $room->post_title]);
}
$jsonRooms = json_encode($selectRooms); ?>

<h1><?= get_admin_page_title() ?></h1>
<p>Bienvenue sur la page de gestion des réservations</p>
<section class="date"><?php
global $wpdb;
$last_resa = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}resa WHERE id=(SELECT max(id) FROM {$wpdb->prefix}resa)");
$last_resa_user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}resa_user WHERE id = $last_resa->user_id");
$last_resa_room = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = $last_resa->room_id");

if (isset($last_resa)) {
  if ($last_resa->booked == 0) { ?>
    <h2>Dernière resa non-validée:</h2>
    <form class="date-form" action="#" method="post" name="date-form" style="margin-bottom: 3em;">
      <table>
        <tr>
          <th>Client</th>
          <th>Chambre</th>
          <th><label for="start">Arrivée</label></th>
          <th><label for="end">Départ(jour inclus)</label></th>
          <th>Edition</th>
          <th>Suppression</th>
        </tr>
        <tr>
          <td><?= $last_resa_user->lastname ." ". $last_resa_user->firstname ." (". $last_resa_user->email .")" ?></td>
          <td><?= $last_resa_room->post_title ?></td>
            <td><input type="date" name="start" id="start"></td>
            <td><input type="date" name="end" id="end"></td>
            <td><button type="submit" class="add-date-to-resa" id="<?= $last_resa->id ?>">Valider les dates</button></td>
            <td><button style="color: red">inactif</button></td>
        </tr>
      </table>
    </form>
  <?php
  }
} ?>
</section>

<section class="days">

</section>

<section class="resa">

</section>


<section class="user">
  <table>
    <tr>
      <th>Ajouter Resa</th>
      <th>Nom</th>
      <th>Prénom</th>
      <th>Email</th>
      <th>Tél</th>
      <th>Edition</th>
      <th>Suppression</th>
    </tr> <?php
  global $wpdb;
  $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_user ORDER BY lastname ASC");
  foreach ($users as $user) { ?>
    <tr>
      <td><button class="add-resa-to-user" data="<?= $user->lastname ." ". $user->firstname ." (". $user->email .")" ?>" id="<?= $user->id ?>">Nlle Resa</button></td>
      <td><?= $user->lastname ?></td>
      <td><?= $user->firstname ?></td>
      <td><?= $user->email ?></td>
      <td><?= $user->phone ?></td>
      <td><a href="#"><button style="color: red">Inactif</button></a></td>
      <td><a href="#"><button style="color: red">Inactif</button></a></td>
    </tr>
  <?php } ?>
    <tr>
      <form class="" action="#" method="post">
        <td><input type="text" name="lastname" id="lastname" value=""></td>
        <td><input type="text" name="firstname" id="firstname" value=""></td>
        <td><input type="text" name="email" id="email" value=""></td>
        <td><input type="text" name="phone" id="phone" value=""></td>
        <td>
          <button type="submit" class="new-user-btn">Enreg.nouveau</button>
        </td>
        <td></td>
        <td></td>
      </form>
    </tr>
  </table>
</section>


<script type="text/javascript">
  var selectRoom = <?php echo $jsonRooms ?>;
</script>
