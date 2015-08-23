<?php
class td_util {


    static $authors_array_cache = ''; //cache the results from  create_array_authors

    /**
     * reading the theme settings
     * if we are in demo mode looks for cookies -- REMOVED COOKIE SUPPORT  18/09/2014
     * else takes the settings from database
    */
    static function read_once_theme_settings() {
            td_global::$td_options = get_option(TD_THEME_OPTIONS_NAME);
    }

    //returns the $class if the variable is not empty or false
    static function if_show($variable, $class) {
        if ($variable !== false and !empty($variable)) {
            return ' ' . $class;
        } else {
            return '';
        }
    }

    //returns the class if the variable is empty or false
    static function if_not_show($variable, $class){
        if ($variable === false or empty($variable)) {
            return ' ' . $class;
        } else {
            return '';
        }
    }


    static function get_category_option($category_id, $option_id) {
        if (isset(td_global::$td_options['category_options'][$category_id][$option_id])) {
            return td_global::$td_options['category_options'][$category_id][$option_id];
        } else {
            return '';
        }
    }


    /**
     * reads an ad from our data
     * @param $ad_position_id - header / sidebar etc...
     */
    static function get_td_ads($ad_position_id) {
        //print_r(td_global::$td_options);
        if (isset(td_global::$td_options['td_ads'][$ad_position_id])) {
            return td_global::$td_options['td_ads'];
        } else {
            return '';
        }
    }


    /**
     * Checks to see if a adspot is enabled (ex: it has ad code in it)
     * @param $ad_spot_id
     */
    static function is_ad_spot_enabled($ad_spot_id) {
        if (isset(td_global::$td_options['td_ads'][$ad_spot_id]['ad_code'])) {
            return true;
        } else {
            return false;
        }
    }


    //reads a theme option from wp
    static function get_option($optionName, $default_value = '') {
        //$theme_options = get_option(TD_THEME_OPTIONS_NAME);

        if (!empty(td_global::$td_options[$optionName])) {
            return td_global::$td_options[$optionName];
        } else {
            if (!empty($default_value)) {
                return $default_value;
            } else {
                return;
            }
        }
    }

    //updates a theme option @todo sa updateze globala td_util::$td_options
    static function update_option($optionName, $newValue) {
        //td_global::$td_options = get_option(TD_THEME_OPTIONS_NAME);

        td_global::$td_options[$optionName] = $newValue;



        update_option(TD_THEME_OPTIONS_NAME, td_global::$td_options);
    }



     /**
     * Used only on slide big to cut the title to make it wrap
     *
     * @param $cut_parms
     * @param $title
     * @return string
     */
    static function cut_title($cut_parms, $title) {
        //trim and get the excerpt
        $title = html_entity_decode(trim($title), ENT_QUOTES);

        $title = td_util::excerpt($title,$cut_parms['excerpt']);

        //get an array of chars
        $title_chars = str_split($title);
        //$title_chars = preg_split('/(?=(.{16})*$)/u', $title);

        $buffy = '';
        $current_char_on_line = 0;
        $has_to_cut = false; //when true, the string will be cut

        foreach ($title_chars as $title_char) {
            //check if we reached the limit
            if ($cut_parms['char_per_line'] == $current_char_on_line) {
                $has_to_cut = true;
                //$current_char_on_line = 0;
            } else {
                $current_char_on_line++;
            }

            if ($title_char == ' ' and $has_to_cut === true) {
                //we have to cut, it's a white space so we ignore it (not added to buffy)
                $buffy .= $cut_parms['line_wrap_end'] . $cut_parms['line_wrap_start'];
                $has_to_cut = false;
                $current_char_on_line = 0;
            } else {
                //normal loop
                $buffy .= $title_char;
            }

        }

        //wrap the string
        return $cut_parms['line_wrap_start'] . $buffy . $cut_parms['line_wrap_end'];
    }


    /*
     * gets the blog page url (only if the blog page is configured in theme customizer)
     */
    static function get_home_url() {
        if( get_option('show_on_front') == 'page') {
            $posts_page_id = get_option( 'page_for_posts');
            return get_permalink($posts_page_id);
        } else {
            return false;
        }
    }


    //gets the sidebar setting or default if no sidebar is selected for a specific setting id
    static function show_sidebar($template_id) {
        $tds_cur_sidebar = td_util::get_option('tds_' . $template_id . '_sidebar');
        if (!empty($tds_cur_sidebar)) {
            dynamic_sidebar($tds_cur_sidebar);
        } else {
            //show default
            if (!dynamic_sidebar(TD_THEME_NAME . ' default')) {
                ?>
                <!-- .no sidebar -->
            <?php
            }
        }
    }


    static function get_image_attachment_data($post_id, $size = 'thumbnail', $count = 1 ) {
        $objMeta = array();
        $meta = '';// (stdClass)
        $args = array(
            'numberposts' => $count,
            'post_parent' => $post_id,
            'post_type' => 'attachment',
            'nopaging' => false,
            'post_mime_type' => 'image',
            'order' => 'ASC', // change this to reverse the order
            'orderby' => 'menu_order ID', // select which type of sorting
            'post_status' => 'any'
        );

        $attachments = get_children($args);

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $meta = new stdClass();
                $meta->ID = $attachment->ID;
                $meta->title = $attachment->post_title;
                $meta->caption = $attachment->post_excerpt;
                $meta->description = $attachment->post_content;
                $meta->alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);

                // Image properties
                $props = wp_get_attachment_image_src( $attachment->ID, $size, false );

                $meta->properties['url'] = $props[0];
                $meta->properties['width'] = $props[1];
                $meta->properties['height'] = $props[2];

                $objMeta[] = $meta;
            }

            return ( count( $attachments ) == 1 ) ? $meta : $objMeta;
        }
    }


    //converts a sidebar name to an id that can be used by word press
    static function sidebar_name_to_id($sidebar_name) {
        $clean_name = str_replace(array(' '), '-', trim($sidebar_name));
        $clean_name = str_replace(array("'", '"'), '', trim($clean_name));
        return strtolower($clean_name);
    }



    /*  ----------------------------------------------------------------------------
        used by the css compiler in /includes/app/td_css_generator.php
     */
    static function adjustBrightness($hex, $steps) {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Format the hex color string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Get decimal values
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));

        // Adjust number of steps and keep it inside 0 to 255
        $r = max(0,min(255,$r + $steps));
        $g = max(0,min(255,$g + $steps));
        $b = max(0,min(255,$b + $steps));

        $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
        $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
        $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

        return '#'.$r_hex.$g_hex.$b_hex;
    }


    //converts a hex to rgba
    static function hex2rgba($hex, $opacity) {
        if ( $hex[0] == '#' ) {
            $hex = substr( $hex, 1 );
        }
        if ( strlen( $hex ) == 6 ) {
            list( $r, $g, $b ) = array( $hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5] );
        } elseif ( strlen( $hex ) == 3 ) {
            list( $r, $g, $b ) = array( $hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2] );
        } else {
            return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return "rgba($r, $g, $b, $opacity)";
    }


    static function author_meta() {
        if (is_single()) {
            global $post;
            $td_post_author = get_the_author_meta('display_name', $post->post_author);
            if ($td_post_author) {
                echo '<meta name="author" content="'.$td_post_author.'">'."\n";
            }
        }
    }


    /**
     * create $td_authors array in format id_author => display_name_author
     * @return array id_author => display_name_author
     */
    static function create_array_authors() {

        if (is_admin()) {

            //return the cache if available
            if (self::$authors_array_cache != '') {
                return self::$authors_array_cache;
            }

            $td_authors = array();
            $td_return_obj_authors = get_users('role=Administrator');

            $td_authors[' - No author filter - '] = '';
            foreach($td_return_obj_authors as $obj_autor){
                $auth_id = $obj_autor->ID;
                $auth_name = $obj_autor->display_name;

                $td_authors[$auth_name] = $auth_id;
            }

            self::$authors_array_cache = $td_authors;

            //print_r($td_authors);
            return $td_authors;
        }
    }




    /**
     * returns a string containing the numbers of words or chars for the content
     *
     * @param $post_content - the content thats need to be cut
     * @param $limit        - limit to cut
     * @param string $show_shortcodes - if shortcodes
     * @return string
     */
    static function excerpt($post_content, $limit, $show_shortcodes = '') {
        //REMOVE shortscodes and tags
        if ($show_shortcodes == '') {
            $post_content = preg_replace('`\[[^\]]*\]`','',$post_content);
        }

        $post_content = stripslashes(wp_filter_nohtml_kses($post_content));

        /*only for problems when you need to remove links from content; not 100% bullet prof
        $post_content = htmlentities($post_content, null, 'utf-8');
        $post_content = str_replace("&nbsp;", "", $post_content);
        $post_content = html_entity_decode($post_content, null, 'utf-8');

        //$post_content = preg_replace('(((ht|f)tp(s?)\://){1}\S+)','',$post_content);//Radu A
        $pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";//radu o
        $post_content = preg_replace($pattern,'',$post_content);*/


        //excerpt for letters
        if (td_util::get_option('tds_excerpts_type') == 'letters') {

            $ret_excerpt = mb_substr($post_content, 0, $limit);
            if (mb_strlen($post_content)>=$limit) {
                $ret_excerpt = $ret_excerpt.'...';
            }

            //excerpt for words
        } else {
            /*removed and moved to check this first thing when reaches thsi function
             * if ($show_shortcodes == '') {
                $post_content = preg_replace('`\[[^\]]*\]`','',$post_content);
            }

            $post_content = stripslashes(wp_filter_nohtml_kses($post_content));*/

            $excerpt = explode(' ', $post_content, $limit);




            if (count($excerpt)>=$limit) {
                array_pop($excerpt);
                $excerpt = implode(" ",$excerpt).'...';
            } else {
                $excerpt = implode(" ",$excerpt);
            }


            $excerpt = esc_attr(strip_tags($excerpt));



            if (trim($excerpt) == '...') {
                return '';
            }

            $ret_excerpt = $excerpt;
        }
        return $ret_excerpt;
    }


    /**
     * generates a category tree
     * @param bool $add_all_category = if true ads - All categories - at the begining of the list (used for dropdowns)
     * @return mixed
     */
    static function get_category2id_array($add_all_category = true) {



        if (is_admin() === false) {
            return;
        }

        $categories = get_categories(array(
            'hide_empty' => 0
        ));

        $td_category2id_array_walker = new td_category2id_array_walker;
        $td_category2id_array_walker->walk($categories, 4);

        if ($add_all_category === true) {
            $categories_buffer['- All categories -'] = '';
            return array_merge(
                $categories_buffer,
                $td_category2id_array_walker->td_array_buffer
            );
        } else {
            return $td_category2id_array_walker->td_array_buffer;
        }
    }


    //generates one breadcrumb
    static function get_html5_breadcrumb($display_name, $title_attribute, $url) {
        return '<span itemscope itemtype="' .  td_global::$http_or_https . '://data-vocabulary.org/Breadcrumb"><a title="' . $title_attribute . '" class="entry-crumb" itemprop="url" href="' . $url . '"><span itemprop="title">' . $display_name . '</span></a></span>';
    }


    /**
     * get the featured image of a post
     * @param $post_id
     * @param $thumb_type
     * @return string
     */
    static function get_featured_image_src($post_id, $thumb_type) {
        $attachment_id = get_post_thumbnail_id($post_id);
        $td_temp_image_url = wp_get_attachment_image_src($attachment_id, $thumb_type);

        if (!empty($td_temp_image_url[0])) {
            return $td_temp_image_url[0];
        } else {
            return '';
        }
    }

    /**
     * get information about an attachment
     * @param $attachment_id
     * @param string $thumbType
     * @return array
     */
    static function attachment_get_full_info($attachment_id, $thumbType = 'full') {
        $attachment = get_post( $attachment_id );

        $image_src_array = self::attachment_get_src($attachment_id, $thumbType);

        return array (
            'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true ),
            'caption' => $attachment->post_excerpt,
            'description' => $attachment->post_content,
            'href' => get_permalink($attachment->ID),
            'src' => $image_src_array['src'],
            'title' => $attachment->post_title,
            'width' => $image_src_array['width'],
            'height' => $image_src_array['height']
        );
    }


    /**
     * Safe way to get an attachment image src + width and height. It always returns the array
     * @param $attachment_id
     * @param string $thumbType
     * @return mixed
     */
    static function attachment_get_src($attachment_id, $thumbType = 'full') {
        $image_src_array = wp_get_attachment_image_src($attachment_id, $thumbType);
        $buffy = array();

        //init the variable returned from wp_get_attachment_image_src
        if (empty($image_src_array[0])) {
            $buffy['src'] = '';
        } else {
            $buffy['src'] = $image_src_array[0];
        }

        if (empty($image_src_array[1])) {
            $buffy['width'] = '';
        } else {
            $buffy['width'] = $image_src_array[1];
        }


        if (empty($image_src_array[2])) {
            $buffy['height'] = '';
        } else {
            $buffy['height'] = $image_src_array[2];
        }

        return $buffy;
    }


}//end class td_util

//read the theme settings once
td_util::read_once_theme_settings();


class td_category2id_array_walker extends Walker {
    var $tree_type = 'category';
    var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

    var $td_array_buffer = array();

    function start_lvl( &$output, $depth = 0, $args = array() ) {
    }

    function end_lvl( &$output, $depth = 0, $args = array() ) {
    }


    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        $this->td_array_buffer[str_repeat(' - ', $depth) .  $category->name] = $category->term_id;
    }


    function end_el( &$output, $page, $depth = 0, $args = array() ) {
    }

}


/*  ----------------------------------------------------------------------------
    mbstring support - if missing from host
 */

if (!function_exists('mb_strlen')) {
    function mb_strlen ($string) {
        return strlen($string);
    }
}

if (!function_exists('mb_strpos')) {
    function mb_strpos($haystack,$needle,$offset=0) {
        return strpos($haystack,$needle,$offset);
    }
}
if (!function_exists('mb_strrpos')) {
    function mb_strrpos ($haystack,$needle,$offset=0) {
        return strrpos($haystack,$needle,$offset);
    }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string) {
        return strtolower($string);
    }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($string){
        return strtoupper($string);
    }
}
if (!function_exists('mb_substr')) {
    function mb_substr($string,$start,$length) {
        return substr($string,$start,$length);
    }
}