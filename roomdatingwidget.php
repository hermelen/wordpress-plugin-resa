<?php


class RoomDatingWidget extends WP_Widget
{
  public function __construct() {
      parent::__construct('room_dating', 'Room Dating', array('description' => 'Formulaire d\'enregistrement de résa.'));
  }

  public function widget($args, $instance) {?>
    <h5><?= ($instance['title']) ? $instance['title'] : '' ?></h5>
    <div id="calendar-widget" ></div>
    <?php
  }

  public function form($instance) {
    $title = isset($instance['title']) ? $instance['title'] : '';
    ?>
    <p>
        <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
    </p>
    <?php
  }
}
