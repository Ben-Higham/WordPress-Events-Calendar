<?php

defined('ABSPATH') or die;

class Belv_Upcoming_Events_Widget extends WP_Widget
{
    public function __construct()
    {

        parent::__construct(
            'belv_events_upcoming_events',
            __('Upcoming Events', 'belv_events_widget_domain'),
            array(
                'classname'   => 'belv_events_upcoming',
                'description' => __('Upcoming events from the calendar', 'belv_events'),
            )
        );
    }

    // Widget Front End
    function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        $buttontext = $instance['button_text'];
        $buttonlink = $instance['button_link'];

        global $wpdb;

        $table = $wpdb->prefix . 'belv_calendar';
        $sql_query = "SELECT * FROM $table WHERE date >= curdate() order by date LIMIT 6;";

        $events =  $wpdb->get_results($sql_query);
        $upcoming = array();

        foreach ($events as $event) {
            $newevent = array(
                "title" => $event->title,
                "date" => $event->date,
                "time" => $event->time,
                "datetime" => date("Y-m-d H:i", strtotime($event->date . $event->time)),
                "link" => $event->link,
            );
            array_push($upcoming, $newevent);
        }

        function date_compare($a, $b)
        {
            $t1 = strtotime($a['datetime']);
            $t2 = strtotime($b['datetime']);
            return $t1 - $t2;
        }
        usort($upcoming, 'date_compare');

?>
        <section id="upcoming-meetings">
            <?php
            echo $args['before_widget'];

            if (!empty($title)) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            ?><div class="upcoming-events-grid">
                <?php
                for ($x = 0; $x < 3; $x++) {
                ?>
                    <div class="upcoming-event">
                        <div class="upcoming-icon">
                            <div class="upcoming-month"><?php echo date("F", strtotime($upcoming[$x]['date'])); ?></div>
                            <div class="upcoming-date">
                                <?php echo date("j", strtotime($upcoming[$x]['date'])); ?>
                                <div class="upcoming-day"><?php echo date("l", strtotime($upcoming[$x]['date'])); ?></div>
                            </div>
                        </div>
                        <div class="upcoming-title">
                            <p><?php echo $upcoming[$x]['title']; ?></p>
                            <p><?php echo $upcoming[$x]['time'] ?></p>
                        </div>
                    </div>
                <?php
                }
                ?> </div>
            <?php
            if (!empty($buttontext)) {
                echo '<div class="section-cta"><a href="' . $buttonlink . '" class="cta-button cta-button-secondary">' . $buttontext . '</a></div>';
            }
            
            echo $args['after_widget'];
            ?>
        </section>
    <?php
    }

    // Widget backend
    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $buttontext = esc_attr($instance['button_text']);
        $buttonlink = esc_attr($instance['button_link']);
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'sermon-manager-for-wordpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?= $this->get_field_id('image'); ?>">Button text:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo $buttontext; ?>" />
        </p>
        <p>
            <label for="<?= $this->get_field_id('image'); ?>">Button link:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('button_link'); ?>" name="<?php echo $this->get_field_name('button_link'); ?>" type="text" value="<?php echo $buttonlink; ?>" />
        </p>
<?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['button_text'] = $new_instance['button_text'];
        $instance['button_link'] = $new_instance['button_link'];
        return $instance;
    }
}

add_action('widgets_init', function () {
    register_widget('Belv_Upcoming_Events_Widget');
});
