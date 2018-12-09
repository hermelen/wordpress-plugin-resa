<h1><?= get_admin_page_title() ?></h1>
<p>Bienvenue sur la page d'accueil des réservations</p> <?php
global $wpdb;
$days = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_day ORDER BY resa_id DESC, thedate ASC"); ?>
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
