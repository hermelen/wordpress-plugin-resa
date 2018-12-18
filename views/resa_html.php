<?php
global $wpdb;
$resas = $wpdb->get_results(
  "SELECT id, room_id
  FROM {$wpdb->prefix}resa"
);

$booked_days = [];
for ($i=0; $i < count($resas) ; $i++) {

  $resa_id = $resas[$i]->id;
  $room_id = $resas[$i]->room_id;

  $booked_days[$i]['dates'] = $wpdb->get_results(
    "SELECT thedate
    FROM {$wpdb->prefix}resa_day
    WHERE resa_id = $resa_id
    ORDER BY thedate"
  );
  $room_title = $wpdb->get_row(
    "SELECT post_title
    FROM {$wpdb->prefix}posts
    WHERE ID = $room_id"
  );
  $booked_days[$i]['post_title'] = $room_title->post_title;
};

for ($i=0; $i < count($booked_days) ; $i++) {
  $first = reset($booked_days[$i]['dates']);
  $last = end($booked_days[$i]['dates']);
  $title = $booked_days[$i]['post_title'];
  $booked_days[$i] = [$first, $last, $title];
}

$booked_days=json_encode($booked_days);

?>

<h1><?= get_admin_page_title() ?></h1>
<p>Bienvenue sur la page d'accueil des réservations</p>
<div class="resa_container" style="display: flex;">
  <section class='edit'></section>
  <section class="day" style="width: 70%;">
    <table>
      <tr>
        <th></th>
        <th>Chambre</th>
        <th>Client</th>
        <th>Date</th>
        <th>Personnes</th>
        <th>Petit-déj</th>
        <th>Déj</th>
        <th>Dîner</th>
        <th></th>
        <th></th>
        <th></th>
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
        if ($resa->booked != 0) {
          $days = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}resa_day WHERE resa_id = $resa_id ORDER BY thedate ASC");
        }

        $user_info = [
          $user->lastname,
          $user->firstname,
          $user->email,
          $user->phone
        ];

        if ( isset($days) && !empty($days) ) {
         if ( count($days) > 2 ) {
           $first_day = date($days[0]->thedate);
           $last_day = date($days[count($days)-1]->thedate);
           $day_before = date('Y-m-d', strtotime($first_day . ' -1 day'));
           $day_after = date('Y-m-d', strtotime($last_day . ' +1 day')); ?>
           <tr id="tr-day-<?= $days[0]->id ?>" style="background: <?php echo $color ?>">
             <td style="border-top: 1px solid black;">
               <button class="icon-btn info" data="<?= htmlspecialchars(json_encode($user_info)) ?>"><i class="fas fa-info"></i></button>
             </td>
             <td style="border-top: 1px solid black;"><?php echo $room->post_title ?></td>
             <td style="border-top: 1px solid black;"><?php echo $user->lastname." ".$user->firstname ?></td>
             <td style="border-top: 1px solid black;" class="td-date"><?php echo $days[0]->thedate ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->persons ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->breakfast ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->lunch ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->dinner ?></td>
             <td style="border-top: 1px solid black;"><button class="icon-btn edit-day" id="day-<?= $days[0]->id ?>" resa_id="<?= $resa_id ?>"><i class="far fa-edit"></button></td>
             <td style="border-top: 1px solid black;">
               <form action="#" method="post">
                 <input type="hidden" name="resa_id[0]" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="dinner[0]" value="<?php echo $days[0]->dinner ?>">
                 <input type="hidden" name="lunch[0]" value="<?php echo $days[0]->lunch ?>">
                 <input type="hidden" name="breakfast[0]" value="<?php echo $days[0]->breakfast ?>">
                 <input type="hidden" name="persons[0]" value="<?php echo $days[0]->persons ?>">
                 <input type="hidden" name="thedate[0]" value="<?= $day_before ?>">
                 <button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="fas fa-plus"></i></button>
               </form>
             </td>
             <td style="border-top: 1px solid black;">
               <form action="#" method="post">
                 <input type="hidden" name="delete_day_id" value="<?php echo $days[0]->id ?>">
                 <!-- <input type="submit" name="delete_day" value="Supprimer"> -->
                 <button type="submit" class="icon-btn"><i class="far fa-trash-alt"></i></button>
               </form>
             </td>
           </tr><?php
           for ($i=1; $i < count($days)-1 ; $i++) { ?>
             <tr id="tr-day-<?= $days[$i]->id ?>" style="background: <?php echo $color ?>">
               <td style="color: grey"></td>
               <td style="color: grey"><?php echo $room->post_title ?></td>
               <td style="color: grey"><?php echo $user->lastname." ".$user->firstname ?></td>
               <td class="td-date"><?php echo $days[$i]->thedate ?></td>
               <td><?php echo $days[$i]->persons ?></td>
               <td><?php echo $days[$i]->breakfast ?></td>
               <td><?php echo $days[$i]->lunch ?></td>
               <td><?php echo $days[$i]->dinner ?></td>
               <td><button class="icon-btn edit-day" id="day-<?= $days[$i]->id ?>" resa_id="<?= $resa_id ?>"><i class="far fa-edit"></button></td>
               <td></td>
               <td></td>
             </tr><?php
           } ?>
           <tr id="tr-day-<?= $days[count($days)-1]->id ?>" style="background: <?php echo $color ?>">
             <td></td>
             <td><?php echo $room->post_title ?></td>
             <td><?php echo $user->lastname." ".$user->firstname ?></td>
             <td class="td-date"><?php echo $days[count($days)-1]->thedate ?></td>
             <td><?php echo $days[count($days)-1]->persons ?></td>
             <td><?php echo $days[count($days)-1]->breakfast ?></td>
             <td><?php echo $days[count($days)-1]->lunch ?></td>
             <td><?php echo $days[count($days)-1]->dinner ?></td>
             <td><button class="icon-btn edit-day" id="day-<?= $days[count($days)-1]->id ?>" resa_id="<?= $resa_id ?>"><i class="far fa-edit"></button></td>
             <td>
               <form action="#" method="post">
                 <input type="hidden" name="resa_id[0]" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="dinner[0]" value="<?php echo $days[count($days)-1]->dinner ?>">
                 <input type="hidden" name="lunch[0]" value="<?php echo $days[count($days)-1]->lunch ?>">
                 <input type="hidden" name="breakfast[0]" value="<?php echo $days[count($days)-1]->breakfast ?>">
                 <input type="hidden" name="persons[0]" value="<?php echo $days[count($days)-1]->persons ?>">
                 <input type="hidden" name="thedate[0]" value="<?= $day_after ?>">
                 <button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="fas fa-plus"></i></button>
               </form>
             </td>
             <td>
               <form action="#" method="post">
                 <input type="hidden" name="delete_day_id" value="<?php echo $days[count($days)-1]->id ?>">
                 <!-- <input type="submit" name="delete_day" value="Supprimer"> -->
                 <button type="submit" class="icon-btn"><i class="far fa-trash-alt"></i></button>
               </form>
             </td>
           </tr> <?php
         } elseif (count($days) == 2) {
           $first_day = date($days[0]->thedate);
           $last_day = date($days[1]->thedate);
           $day_before = date('Y-m-d', strtotime($first_day . ' -1 day'));
           $day_after = date('Y-m-d', strtotime($last_day . ' +1 day')); ?>

           <tr id="tr-day-<?= $days[0]->id ?>" style="background: <?php echo $color ?>">
             <td style="border-top: 1px solid black;">
               <button class="icon-btn info" data="<?= htmlspecialchars(json_encode($user_info)) ?>"><i class="fas fa-info"></i></button>
             </td>
             <td style="border-top: 1px solid black;"><?php echo $room->post_title ?></td>
             <td style="border-top: 1px solid black;"><?php echo $user->lastname." ".$user->firstname ?></td>
             <td style="border-top: 1px solid black;" class="td-date"><?php echo $days[0]->thedate ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->persons ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->breakfast ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->lunch ?></td>
             <td style="border-top: 1px solid black;"><?php echo $days[0]->dinner ?></td>
             <td style="border-top: 1px solid black;"><button class="icon-btn edit-day" id="day-<?= $days[0]->id ?>" resa_id="<?= $resa_id ?>"><i class="far fa-edit"></button></td>
             <td style="border-top: 1px solid black;">
               <form action="#" method="post">
                 <input type="hidden" name="resa_id[0]" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="dinner[0]" value="<?php echo $days[0]->dinner ?>">
                 <input type="hidden" name="lunch[0]" value="<?php echo $days[0]->lunch ?>">
                 <input type="hidden" name="breakfast[0]" value="<?php echo $days[0]->breakfast ?>">
                 <input type="hidden" name="persons[0]" value="<?php echo $days[0]->persons ?>">
                 <input type="hidden" name="thedate[0]" value="<?= $day_before ?>">
                 <button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="fas fa-plus"></i></button>
               </form>
             </td>
             <td style="border-top: 1px solid black;">
               <form action="#" method="post">
                 <input type="hidden" name="delete_day_id" value="<?php echo $days[0]->id ?>">
                 <!-- <input type="submit" name="delete_day" value="Supprimer"> -->
                 <button type="submit" class="icon-btn"><i class="far fa-trash-alt"></i></button>
               </form>
             </td>
           </tr>
           <tr id="tr-day-<?= $days[1]->id ?>" style="background: <?php echo $color ?>">
             <td></td>
             <td style="color: grey;"><?php echo $room->post_title ?></td>
             <td style="color: grey;"><?php echo $user->lastname." ".$user->firstname ?></td>
             <td class="td-date"><?php echo $days[1]->thedate ?></td>
             <td><?php echo $days[1]->persons ?></td>
             <td><?php echo $days[1]->breakfast ?></td>
             <td><?php echo $days[1]->lunch ?></td>
             <td><?php echo $days[1]->dinner ?></td>
             <td><button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="far fa-edit" resa_id="<?= $resa_id ?>"></button></td>
             <td>
               <form action="#" method="post">
                 <input type="hidden" name="resa_id[0]" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="dinner[0]" value="<?php echo $days[1]->dinner ?>">
                 <input type="hidden" name="lunch[0]" value="<?php echo $days[1]->lunch ?>">
                 <input type="hidden" name="breakfast[0]" value="<?php echo $days[1]->breakfast ?>">
                 <input type="hidden" name="persons[0]" value="<?php echo $days[1]->persons ?>">
                 <input type="hidden" name="thedate[0]" value="<?= $day_after ?>">
                 <button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="fas fa-plus"></i></button>
               </form>
             </td>
             <td>
               <form action="#" method="post">
                 <input type="hidden" name="delete_day_id" value="<?php echo $days[1]->id ?>">
                 <!-- <input type="submit" name="delete_day" value="Supprimer"> -->
                 <button type="submit" class="icon-btn"><i class="far fa-trash-alt"></i></button>
               </form>
             </td>
           </tr><?php
         } else {
           $today = date($days[0]->thedate);
           $day_before = date('Y-m-d', strtotime($today . ' -1 day'));
           $day_after = date('Y-m-d', strtotime($today . ' +1 day'));?>

           <tr id="tr-day-<?= $days[0]->id ?>" style="background: <?php echo $color ?>">
             <td style="border-top: 1px solid black;">
               <button class="icon-btn info" data="<?= htmlspecialchars(json_encode($user_info)) ?>"><i class="fas fa-info"></i></button>
             </td>
             <td style="border-top: 1px solid black;"><?php echo $room->post_title ?></td>
             <td style="border-top: 1px solid black;"><?php echo $user->lastname." ".$user->firstname ?></td>
             <td class="td-date"><?php echo $days[0]->thedate ?></td>
             <td><?php echo $days[0]->persons ?></td>
             <td><?php echo $days[0]->breakfast ?></td>
             <td><?php echo $days[0]->lunch ?></td>
             <td><?php echo $days[0]->dinner ?></td>
             <td><button class="icon-btn edit-day" id="day-<?= $days[0]->id ?>"><i class="far fa-edit" resa_id="<?= $resa_id ?>"></i></button></td>
             <td>
               <form action="#" method="post">
                 <input type="hidden" name="resa_id[0]" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="dinner[0]" value="<?php echo $days[0]->dinner ?>">
                 <input type="hidden" name="lunch[0]" value="<?php echo $days[0]->lunch ?>">
                 <input type="hidden" name="breakfast[0]" value="<?php echo $days[0]->breakfast ?>">
                 <input type="hidden" name="persons[0]" value="<?php echo $days[0]->persons ?>">
                 <input type="hidden" name="thedate[0]" value="<?= $day_before ?>">
                 <button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="fas fa-plus"></i></button>
               </form>
               <form action="#" method="post">
                 <input type="hidden" name="resa_id[0]" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="dinner[0]" value="<?php echo $days[0]->dinner ?>">
                 <input type="hidden" name="lunch[0]" value="<?php echo $days[0]->lunch ?>">
                 <input type="hidden" name="breakfast[0]" value="<?php echo $days[0]->breakfast ?>">
                 <input type="hidden" name="persons[0]" value="<?php echo $days[0]->persons ?>">
                 <input type="hidden" name="thedate[0]" value="<?= $day_after ?>">
                 <button class="icon-btn edit-day" id="day-<?= $days[1]->id ?>"><i class="fas fa-plus"></i></button>
               </form>
             </td>
             <td>
               <form action="#" method="post">
                 <input type="hidden" name="delete_resa_id" value="<?php echo $resa->id ?>">
                 <input type="hidden" name="delete_day_id" value="<?php echo $days[0]->id ?>">
                 <!-- <input type="submit" name="delete_day" value="Supprimer"> -->
                 <button type="submit" class="icon-btn"><i class="far fa-trash-alt"></i></button>
               </form>
             </td>
           </tr><?php
         }
        }
      } ?>
    </table>
  </section>
  <section class="calendar" style="width: 30%;">
    <div id="calendar-widget"></div>
    <section class="user_detail"></section>
  </section>
</div>


<script type="text/javascript">
  var bookedDays = <?= $booked_days ?>;
</script>
