<?php

class pinboard_most_recent_widget extends WP_Widget {
    function pinboard_most_recent_widget() {
        $widget_ops = array(
            'classname' => 'pinboard_most_recent',
            'description' => 'Display a user\'s most recent pinboard bookmarks.'
        );
        $this->WP_Widget( 'pinboard_most_recent_widget', 'Pinboard Most Recent', $widget_ops );
    }
    
    function form($instance) {
        $defaults = array(
            'title'  => 'Most Recent Bookmarks',
            'tag'    => '',
            'number' => 10,
            'token'    => '',
        );

        $title_name = $this->get_field_name( 'title' );
        $tag_name = $this->get_field_name( 'tag' );
        $number_name = $this->get_field_name( 'number' );
        $token_name = $this->get_field_name( 'token' );

        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = esc_attr($instance['title']);
        $tag = esc_attr($instance['tag']);
        $number = esc_attr($instance['number']);
        $token = esc_attr($instance['token']);

        print "<p>Title: <input class='widefat' name='$title_name' type='text'
value='$title' /></p>";
        print "<p>Tag: <input class='widefat' name='$tag_name' type='text'
value='$tag' /></p>";
        print "<p>Number: <input class='widefat' name='$number_name' type='text'
value='$number' /></p>";
        print "<p>Token: <input class='widefat' name='$token_name' type='text'
value='$token' /></p>";
    }

    //save the widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['tag'] = strip_tags( $new_instance['tag'] );
        $instance['number'] = (int) $new_instance['number'] ? (int) $new_instance['number'] : 10;
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
        $response = pinboard_api_call(
            'posts/recent',
            array(
                'tag' => $instance['tag'],
                'auth_token' => $instance['token'],
            )
        );
        foreach ($response->posts as $post) {
          if ($post->description && $post->description != '[no title]') {
            $desc = esc_attr($post->description);
            print "<li><a href='{$post->href}'>$desc</a></li>";
          }
        }
        print $after_widget;
    }
}