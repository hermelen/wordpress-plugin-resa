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
if (isset($last_resa) && !empty($last_resa)) {
  $last_resa_user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}resa_user WHERE id = $last_resa->user_id");
}
if (isset($last_resa) && !empty($last_resa)) {
  $last_resa_room = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = $last_resa->room_id");
}

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
      <th>Nom</th>
      <th>Prénom</th>
      <th>Email</th>
      <th>Tél</th>
      <th>Résa</th>
      <th>Client</th>
    </tr> <?php
  global $wpdb;
  $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_user ORDER BY lastname ASC");
  foreach ($users as $user) { ?>
    <tr id="tr-user-<?= $user->id ?>">
      <td><?= $user->lastname ?></td>
      <td><?= $user->firstname ?></td>
      <td><?= $user->email ?></td>
      <td><?= $user->phone ?></td>
      <td><button class="add-resa-to-user" data="<?= $user->lastname ." ". $user->firstname ." (". $user->email .")" ?>" id="<?= $user->id ?>"><i class="fas fa-plus"></i> Nlle Resa</button></td>
      <td><button class="edit-user" id="user-<?= $user->id ?>"><i class="fas fa-edit"></i> Modifier ce client</button></td>
    </tr>
  <?php } ?>
    <tr>
      <form class="" action="#" method="post">
        <td style="border-top: 1px solid black;"><input type="text" name="lastname" id="lastname" value="" placeholder="Nom nouveau client"></td>
        <td style="border-top: 1px solid black;"><input type="text" name="firstname" id="firstname" value="" placeholder="Prénom nouveau client"></td>
        <td style="border-top: 1px solid black;"><input type="text" name="email" id="email" value="" placeholder="e-mail nouveau client"></td>
        <td style="border-top: 1px solid black;"><input type="text" name="phone" id="phone" value="" placeholder="téléphone nouveau client"></td>
        <td style="border-top: 1px solid black;"></td>
        <td style="border-top: 1px solid black;">
          <button type="submit" class="new-user-btn" style="width: 100%"><i class="fas fa-plus"></i> Ajouter un client</button>
        </td>
      </form>
    </tr>
  </table>
</section>


<script type="text/javascript">
  var selectRoom = <?php echo $jsonRooms ?>;
</script>
