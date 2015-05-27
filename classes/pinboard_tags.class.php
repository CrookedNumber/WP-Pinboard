<?php
class pinboard_tags_widget extends WP_Widget {
    function pinboard_tags_widget() {
        $widget_ops = array(
            'classname' => 'pinboard_tags',
            'description' => 'Display a user\'s tags.'
        );
        $this->WP_Widget( 'pinboard_tags_widget', 'Pinboard Tags', $widget_ops );
    }
    
    function form($instance) {
        $defaults = array(
            'title'  => 'Tags',
            'token'    => '',
        );

        $title_name = $this->get_field_name( 'title' );
        $token_name = $this->get_field_name( 'token' );

        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = esc_attr($instance['title']);
        $token = esc_attr($instance['token']);

        print "<p>Title: <input class='widefat' name='$title_name' type='text'
value='$title' /></p>";
        print "<p>Token: <input class='widefat' name='$token_name' type='text'
value='$token' /></p>";
    }

    //save the widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['token'] = strip_tags( $new_instance['token'] );
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        print $before_widget;
        $title = apply_filters( 'widget_title', $instance['title'] );
        if ( !empty( $title ) ) {
            print $before_title . $title . $after_title;
        };
        $response = pinboard_api_call('tags/get', array('auth_token' => $instance['token']));
        $user = pinboard_extract_user($instance['token']);

        $tags = (array) $response;
        ksort($tags);
        foreach ($tags as $tag => $count) {
            $text = esc_attr($tag) . " ($count)";
            $href = "https://pinboard.in/u:$user/t:" . urlencode($tag);
            print "<li><a href='$href'>$text</a></li>";
        }
        print $after_widget;
    }
}
