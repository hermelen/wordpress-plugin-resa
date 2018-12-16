<h1><?= get_admin_page_title() ?></h1>
<p>Bienvenue sur la page d'accueil des réservations</p>
<div class="resa_container" style="display: flex;">
  <section class="filter" style="width: 10%;">
  </section>
  <section class="day" style="width: 80%;">
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
      </tr><?php

      global $wpdb;

      $resas = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa");
      foreach ($resas as $resa) {
        $resa_id = $resa->id;
        $user_id = $resa->user_id;
        $room_id = $resa->room_id;

        $base_1 = substr($user_id*333, 0, 3);
        $base_2 = substr($user_id*666, 0, 3);
        $base_3 = substr($user_id*999, 0, 3);
        $color_1 = ( $base_1 < 255 ) ? $base_1 : substr($base_1, 0, 2);
        $color_2 = ( $base_2 < 255 ) ? $base_2 : substr($base_2, 0, 2);
        $color_3 = ( $base_3 < 255 ) ? $base_3 : substr($base_3, 0, 2);
        $color = "rgba(".$color_1.", ".$color_2.", ".$color_3.", 0.3)";

        $room = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE id = $room_id");
        $user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}resa_user WHERE id = $user_id");
        $days = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_day WHERE resa_id = $resa_id ORDER BY thedate ASC");

        if ( count($days) > 2 ) {
          $first_day = date($days[0]->thedate);
          $last_day = date($days[count($days)-1]->thedate);
          $day_before = date('Y-m-d', strtotime($first_day . ' -1 day'));
          $day_after = date('Y-m-d', strtotime($last_day . ' +1 day')); ?>
          <tr id="tr-day-<?= $days[0]->id ?>" style="background: <?php echo $color ?>">
            <td style="border-top: 1px solid black;"><?php echo $user->lastname ?></td>
            <td style="border-top: 1px solid black;"><?php echo $user->firstname ?></td>
            <td style="border-top: 1px solid black;"><?php echo $user->email ?></td>
            <td style="border-top: 1px solid black;"><?php echo $user->phone ?></td>
            <td style="border-top: 1px solid black;"><?php echo $room->post_title ?></td>
            <td style="border-top: 1px solid black;" class="td-date"><?php echo $days[0]->thedate ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->persons ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->breakfast ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->lunch ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->dinner ?></td>
          </tr><?php
          for ($i=1; $i < count($days)-1 ; $i++) { ?>
            <tr id="tr-day-<?= $days[$i]->id ?>" style="background: <?php echo $color ?>">
              <td style="color: grey"><?php echo $user->lastname ?></td>
              <td style="color: grey"><?php echo $user->firstname ?></td>
              <td style="color: grey"><?php echo $user->email ?></td>
              <td style="color: grey"><?php echo $user->phone ?></td>
              <td style="color: grey"><?php echo $room->post_title ?></td>
              <td class="td-date"><?php echo $days[$i]->thedate ?></td>
              <td><?php echo $days[$i]->persons ?></td>
              <td><?php echo $days[$i]->breakfast ?></td>
              <td><?php echo $days[$i]->lunch ?></td>
              <td><?php echo $days[$i]->dinner ?></td>
            </tr><?php
          } ?>
          <tr id="tr-day-<?= $days[count($days)-1]->id ?>" style="background: <?php echo $color ?>">
            <td style="color: grey"><?php echo $user->lastname ?></td>
            <td style="color: grey"><?php echo $user->firstname ?></td>
            <td style="color: grey"><?php echo $user->email ?></td>
            <td style="color: grey"><?php echo $user->phone ?></td>
            <td style="color: grey"><?php echo $room->post_title ?></td>
            <td class="td-date"><?php echo $days[count($days)-1]->thedate ?></td>
            <td><?php echo $days[count($days)-1]->persons ?></td>
            <td><?php echo $days[count($days)-1]->breakfast ?></td>
            <td><?php echo $days[count($days)-1]->lunch ?></td>
            <td><?php echo $days[count($days)-1]->dinner ?></td>
          </tr> <?php
        } elseif (count($days) == 2) {
          $first_day = date($days[0]->thedate);
          $last_day = date($days[1]->thedate);
          $day_before = date('Y-m-d', strtotime($first_day . ' -1 day'));
          $day_after = date('Y-m-d', strtotime($last_day . ' +1 day')); ?>

          <tr id="tr-day-<?= $days[0]->id ?>" style="background: <?php echo $color ?>">
            <td style="border-top: 1px solid black;"><?php echo $user->lastname ?></td>
            <td style="border-top: 1px solid black;"><?php echo $user->firstname ?></td>
            <td style="border-top: 1px solid black;"><?php echo $user->email ?></td>
            <td style="border-top: 1px solid black;"><?php echo $user->phone ?></td>
            <td style="border-top: 1px solid black;"><?php echo $room->post_title ?></td>
            <td style="border-top: 1px solid black;" class="td-date"><?php echo $days[0]->thedate ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->persons ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->breakfast ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->lunch ?></td>
            <td style="border-top: 1px solid black;"><?php echo $days[0]->dinner ?></td>
          </tr>
          <tr id="tr-day-<?= $days[1]->id ?>" style="background: <?php echo $color ?>">
            <td style="color: grey"><?php echo $user->lastname ?></td>
            <td style="color: grey"><?php echo $user->firstname ?></td>
            <td style="color: grey"><?php echo $user->email ?></td>
            <td style="color: grey"><?php echo $user->phone ?></td>
            <td style="color: grey"><?php echo $room->post_title ?></td>
            <td class="td-date"><?php echo $days[1]->thedate ?></td>
            <td><?php echo $days[1]->persons ?></td>
            <td><?php echo $days[1]->breakfast ?></td>
            <td><?php echo $days[1]->lunch ?></td>
            <td><?php echo $days[1]->dinner ?></td>
            <td><button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="far fa-edit"></button></td>
          </tr><?php
        } else {
          $today = date($days[0]->thedate);
          $day_before = date('Y-m-d', strtotime($today . ' -1 day'));
          $day_after = date('Y-m-d', strtotime($today . ' +1 day'));?>

          <tr id="tr-day-<?= $days[0]->id ?>" style="background: <?php echo $color ?>">
            <td><?php echo $user->lastname ?></td>
            <td><?php echo $user->firstname ?></td>
            <td><?php echo $user->email ?></td>
            <td><?php echo $user->phone ?></td>
            <td><?php echo $room->post_title ?></td>
            <td class="td-date"><?php echo $days[0]->thedate ?></td>
            <td><?php echo $days[0]->persons ?></td>
            <td><?php echo $days[0]->breakfast ?></td>
            <td><?php echo $days[0]->lunch ?></td>
            <td><?php echo $days[0]->dinner ?></td>
            <td><button class="icon-btn edit-day" id="day-<?= $days[0]->id ?>"><i class="far fa-edit"></i></button></td>
          </tr><?php
        }
      } ?>
    </table>
  </section>
</div>
