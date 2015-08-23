<?php

//print_r($_POST);

class td_panel_data_source {




    /**
     * Reads an individual setting - only one setting!
     * @param $read_array -
     * 'ds' => 'data source ID',
      'item_id' = > 'the category id for example', - OPTIONAL category id or author id or page id
     * 'option_id' => 'the option id ex: background'
     * @return returns the value of the setting
     */
    static function read($read_array) {
        switch ($read_array['ds']) {
            case 'td_category':
                return td_util::get_category_option($read_array['item_id'], $read_array['option_id']);
                break;

            case 'td_option':
                return td_util::get_option($read_array['option_id']);//htmlspecialchars()
                break;

            case 'wp_option':
                return htmlspecialchars(get_option($read_array['option_id']));
                break;

            case 'td_homepage':
                // here we get all the options for the homepage (including widgets?)
                break;

            case 'td_page_option':

                break;

            case 'td_widget':

                break;

            //author metadata
            case 'td_author':
                return get_the_author_meta($read_array['option_id'], $read_array['item_id']);
                break;


            //wordpress theme mod datasource
            case 'wp_theme_mod':
                return htmlspecialchars(get_theme_mod($read_array['option_id']));
                break;


            //wordpress usermenu to menu spot datasource
            case 'wp_theme_menu_spot':
                $menu_spots_array = get_theme_mod('nav_menu_locations');
                //check to see if there is a menu assigned to that particular option_id (menu id)
                if (isset($menu_spots_array[$read_array['option_id']])) {
                    return $menu_spots_array[$read_array['option_id']];
                } else {
                    return '';
                }
                break;


            //translation data source
            case 'td_translate':
                //get all the translations (they are stored in the td_008 variable)
                $translations = td_util::get_option('td_translation_map_user');
                if (!empty($translations[$read_array['option_id']])) {
                    return $translations[$read_array['option_id']];//htmlspecialchars()
                } else {
                    return '';
                }
                //return td_util::get_option($read_array['option_id']);
                break;


            //read the ads parameters
            //[ds] => td_ads [option_id] => current_ad_type [item_id] => header - has to become [item_id][option_id]
            case 'td_ads':
                //get all the ad spots (they are stored in the td_008 variable)
                $ads = td_util::get_option('td_ads');
                if (!empty($ads[$read_array['item_id']]) and !empty($ads[$read_array['item_id']][$read_array['option_id']])) {
                    return htmlspecialchars($ads[$read_array['item_id']][$read_array['option_id']]);
                } else {
                    return '';
                }
                break;


            case 'td_fonts_user_insert':
                $fonts_user_inserted = td_util::get_option('td_fonts_user_inserted');
                if(!empty($fonts_user_inserted[$read_array['option_id']])) {
                    return $fonts_user_inserted[$read_array['option_id']];
                }
                break;


            case 'td_fonts':
                $fonts_user_inserted = td_util::get_option('td_fonts');
                if(!empty($fonts_user_inserted[$read_array['item_id']][$read_array['option_id']])) {
                    return $fonts_user_inserted[$read_array['item_id']][$read_array['option_id']];
                }
                break;
        }
    }



    /*
     * Updates all the settings for all of the types  [setting_type][etc]
     * this called at the end of this file
     * this function updates the form - first it reads all the settings from wordpress and then it saves them after the update
    */
    static function update() {

        //load all the theme's settings
        td_global::$td_options = get_option(TD_THEME_OPTIONS_NAME);


        /*  ----------------------------------------------------------------------------
            save the data
         */

        foreach ($_POST as $post_data_source => $post_value) {
            switch ($post_data_source) {

                case 'td_category':
                    self::update_category($post_value);
                    break;

                case 'td_option':
                    self::update_td_option($post_value);
                    break;

                case 'wp_option':
                    self::update_wp_option($post_value);
                    break;

                case 'td_homepage':
                    break;

                case 'td_page_option':
                    break;

                case 'td_author':
                    self::update_td_author($post_value);
                    break;

                case 'wp_widget':
                    self::update_wp_widget($post_value);
                    break;

                case 'wp_theme_mod':
                    self::update_wp_theme_mod($post_value);
                    break;

                case 'wp_theme_menu_spot':
                    self::update_wp_theme_menu_spot($post_value);
                    break;

                case 'td_translate':
                    self::update_td_translate($post_value);
                    break;

                case 'td_ads':
                    self::update_td_ads($post_value);
                    break;

                case 'td_fonts':
                    self::update_td_fonts($post_value);
                    break;

            }
        }

        //compile user css if any
        td_global::$td_options['tds_user_compile_css'] = td_css_generator();


        //save all the themes settings (td_options + td_category)
        update_option(TD_THEME_OPTIONS_NAME, td_global::$td_options );
    }


    /*  ----------------------------------------------------------------------------
        The functions that update the options from the form on post
     */


    /**
     * updates the ads
     * @param $wp_option_array
     */
    private static function update_td_ads($wp_option_array) {


        //temp vars
        $ad_code = $explode_ad_code = '';

        //pass tru the array, check what type it is, extract the info about it, if google ad
        foreach($wp_option_array as $box_add => $values){
            if(!empty($values['ad_code'])) {
                $ad_code = stripcslashes($values['ad_code']);

                //check to see if it is google ad
                if(preg_match('/googlesyndication.com/', $ad_code)){
                    $wp_option_array[$box_add]['current_ad_type'] = 'google';

                    //test to see if if google ad asincron
                    if(preg_match('/data-ad-client=/', $ad_code)){
                        //$wp_option_array[$box_add]['current_ad_type'] = 'google async';

                        //*** GOOGLE ASINCRON *************

                        //get g_data_ad_client
                        $explode_ad_code = explode('data-ad-client', $ad_code);
                        preg_match('/"([a-zA-Z0-9-\s]+)"/', $explode_ad_code[1], $matches_add_client);
                        $wp_option_array[$box_add]['g_data_ad_client'] = str_replace(array('"', ' '), array(''), $matches_add_client[1]);

                        //get g_data_ad_slot
                        $explode_ad_code = explode('data-ad-slot', $ad_code);
                        preg_match('/"([a-zA-Z0-9\s]+)"/', $explode_ad_code[1], $matches_add_slot);
                        $wp_option_array[$box_add]['g_data_ad_slot'] = str_replace(array('"', ' '), array(''), $matches_add_slot[1]);

                    } else {

                        //*** GOOGLE SINCRON *************

                        //get g_data_ad_client
                        $explode_ad_code = explode('google_ad_client', $ad_code);
                        preg_match('/"([a-zA-Z0-9-\s]+)"/', $explode_ad_code[1], $matches_add_client);
                        $wp_option_array[$box_add]['g_data_ad_client'] = str_replace(array('"', ' '), array(''), $matches_add_client[1]);

                        //get g_data_ad_slot
                        $explode_ad_code = explode('google_ad_slot', $ad_code);
                        preg_match('/"([a-zA-Z0-9\s]+)"/', $explode_ad_code[1], $matches_add_slot);
                        $wp_option_array[$box_add]['g_data_ad_slot'] = str_replace(array('"', ' '), array(''), $matches_add_slot[1]);
                    }

                } else {
                    $wp_option_array[$box_add]['current_ad_type'] = 'other';
                }
            }else {
                if($box_add == 'background_click') {
                    //check if we have something in the link input
                    if(trim(empty($values['link']))){
                        //remove if no link
                        unset($wp_option_array[$box_add]);
                    }
                } else {
                    //remove if no ad code
                    unset($wp_option_array[$box_add]);
                }
            }
        }

        //print_r($wp_option_array);
        td_global::$td_options['td_ads'] = $wp_option_array;
    }


    /**
     * Updates the td translation map array form td_008
     * @param $wp_option_array
     */
    private static function update_td_translate($wp_option_array) {
        td_global::$td_options['td_translation_map_user'] = $wp_option_array;
    }



    /**
     * this function updates each menu spot to a user created menu.
     * @param $wp_option_array
     * Array ( [option_id] => options_value, [option_id] => options_value )
     */
    private static function update_wp_theme_menu_spot($wp_option_array) {
        $menu_spots_array = get_theme_mod('nav_menu_locations');

        foreach ($wp_option_array as $option_id => $option_value) {
            $menu_spots_array[$option_id] = $option_value;
        }

        set_theme_mod('nav_menu_locations', $menu_spots_array);
    }



    /**
     * updates all the thememods
     * @param $wp_option_array
     * Array ( [option_id] => options_value, [option_id] => options_value )
     */
    private static function update_wp_theme_mod($wp_option_array) {
        //get defaults array
        $default_array = $_POST['td_default'];

        foreach ($wp_option_array as $option_id => $option_value) {
            //check for default values
            if(!empty($default_array['td_option'][$option_id]) and strtolower($default_array['td_option'][$option_id]) == strtolower($option_value)) {
                $option_value = '';
            }

            set_theme_mod($option_id, $option_value);
        }
    }


    /**
     * @param $wp_widgets_array
     * Array (
     * [testing] => Array (  //sidebar name
     *  [td_block4_widget] => Array (  //widget name
     *      [sort] => ra    //att_key => att value
     *      [custom_title] => test )
     *  )
     * )
     */
    private static function update_wp_widget($wp_widgets_array) {
        //print_r($wp_widgets_array);
        $td_demo_site = new td_demo_site();
        foreach($wp_widgets_array as $sidebar => $widgets) {
            foreach ($widgets as $widget_name => $widget_atts) {
                $td_demo_site->add_widget_to_sidebar($sidebar, $widget_name, $widget_atts);
            }
        }
    }


    /**
     * @param $wp_option_array
     * Array ( [option_id] => options_value, [option_id] => options_value )
     */
    private static function update_wp_option($wp_option_array) {
        foreach ($wp_option_array as $option_id => $option_value) {
            update_option($option_id, $option_value);
        }
    }


    /**
     * @param $td_option_array
     * Array ( [option_id] => options_value, [option_id_2] => options_value )
     */
    private static function update_td_option($td_option_array) {
        //get defaults array
        $default_array = $_POST['td_default'];

        foreach($td_option_array as $options_id => $option_value) {
            //check for default values
            if(!empty($default_array['td_option'][$options_id]) and strtolower($default_array['td_option'][$options_id]) == strtolower($option_value)) {
                $option_value = '';
            }
            td_global::$td_options[$options_id] = $option_value;
        }
    }


    /**
     * @param $td_author_array
     * Array (
     * [author_id] => Array (
     *  [option_id] => options_value),
     *  [option_id_2] => options_value)
     * ),
     * [author_id_2] => Array (
     *  [option_id] => options_value),
     *  [option_id_2] => options_value)
     * )
     */
    private static function update_td_author($td_author_array) {
        foreach ($td_author_array as $author_id => $author_options) {
            foreach ($author_options as $author_option => $author_option_value) {
                update_user_meta($author_id, $author_option, $author_option_value);
            }
        }

    }


    /**
     * @param $category_array
     * Array (
     * [category_id] => Array (
     *  [option_id] => options_value),
     *  [option_id_2] => options_value)
     * ),
     * [category_id_2] => Array (
     *  [option_id] => options_value),
     *  [option_id_2] => options_value)
     * )
     */
    private static function update_category($category_array) {
        //get defaults array
        $default_array = $_POST['td_default'];

        foreach ($category_array as $category_id => $category_options) {
            foreach ($category_options as $category_option_id => $category_option_value) {
                //check for default values

                if(!empty($default_array['td_category'][$category_id][$category_option_id]) and strtolower($default_array['td_category'][$category_id][$category_option_id]) == strtolower($category_option_value)) {
                    $category_option_value = '';
                }

                self::update_category_option($category_id, $category_option_id, $category_option_value);
            }
        }

    }



    //update a category setting - it deletes the settings if there are empty
    //it is also used by the import script
    public static function update_category_option($category_id, $option_id, $new_value) {
        if ($new_value != '') {
            td_global::$td_options['category_options'][$category_id][$option_id] = $new_value;

        } else {
            //delete the option from the parent category
            unset(td_global::$td_options['category_options'][$category_id][$option_id]);

            //also delete the parrent if there are no more options
            if (isset(td_global::$td_options['category_options'][$category_id]) and count(td_global::$td_options['category_options'][$category_id], COUNT_RECURSIVE) == 0) {
                unset(td_global::$td_options['category_options'][$category_id]);
            }
        }
    }


    /**
     * insert user fonts
     * @param $user_font_option_array
     */
    public static function insert_in_system_fonts_user($user_font_option_array) {
        //save the inserted user fonts into themes td_options
        td_global::$td_options['td_fonts_user_inserted'] = $user_font_option_array;

        //save all the themes settings
        if(update_option(TD_THEME_OPTIONS_NAME, td_global::$td_options )) {
            return true;
        } else{
            return false;
        }
    }



    /**
     * @used to save the fonts
     *
     * $user_custom_fonts_array : font(s) modified by user and sent from the theme panel
     */
    private static function update_td_fonts($user_custom_fonts_array) {
        //get defaults array for color picker
        $default_array = $_POST['td_default'];

        /*
        $js_buffer = used for fonts who use javascript to add @font-face (typekit.com)
        $css_buffer = used for font link to files
        $css_files = used to pull fonts from google
        */

        //declare variable
        $js_buffer = $css_buffer = $css_files = $temp_css_google_files = $temp_css_google_character_set = '';

        //collect google fonts from all fields
        $temp_google_fonts = array();

        //collect all place_name arrays in this array
        $td_fonts_save = array();

        foreach ($user_custom_fonts_array as $font_place => $font_options) {

            $td_fonts_save[$font_place] = array();
            foreach ($font_options as $font_option_id => $font_option_value) {

                //if the $font_option_value is not empty then added to the place name font array
                if(!empty($font_option_value)) {
                    $td_fonts_save[$font_place][$font_option_id] = $font_option_value;

                    //check field values for buffer outputs
                    if($font_option_id == 'font_family') {
                        $explode_font_family = explode('_', $font_option_value);

                        $id_font = $explode_font_family[1];

                        switch ($explode_font_family[0]) {
                            //fonts from files (links to files)
                            case 'file':
                                $font_file_link = td_global::$td_options['td_fonts_user_inserted']['font_file_' . $id_font];
                                $font_file_family = td_global::$td_options['td_fonts_user_inserted']['font_family_' . $id_font];

                                $css_buffer .= '
                                                @font-face {
                                                  font-family: "' . $font_file_family . '";
                                                  src: local("' . $font_file_family . '"), url("' . $font_file_link . '") format("woff");
                                                }
                                ';

                                break;

                            //fonts from type kit
                            case 'tk':
                                $js_buffer = td_global::$td_options['td_fonts_user_inserted']['typekit_js'];
                                break;

                            //fonts from font stacks
                            case 'fs':
                                /*$css_buffer .= '
                                    @font-face {
                                        font-family: ' . td_fonts::$font_stack_list['fs_' . $id_font] .';
                                    }
                                ';*/
                                break;

                            //fonts from google
                            case 'g':
                                if(!in_array($id_font, $temp_google_fonts)) {
                                    $temp_google_fonts[] = $id_font;
                                }
                                break;
                        }

                    }
                }

                //check the color font option for non empty values
                if(!empty($default_array['td_fonts'][$font_place]['color'])) {
                    $td_fonts_save[$font_place]['color'] = $default_array['td_fonts'][$font_place]['color'];
                }

            }

            //if the array for the place name is empty then remove it from the td_fonts array
            if(empty($td_fonts_save[$font_place])){
                unset($td_fonts_save[$font_place]);
            }
        }



        //check sections from db and add them to the saving array if ar not set to empty by the user
        $font_sections_from_db = td_util::get_option('td_fonts');//get the fonts from db
        foreach(td_fonts::$typography_sections as $section_id => $section_name) {

            //check each item from section, and delete the empty ones
            $typo_section = array_filter($user_custom_fonts_array[$section_id]);

            //if the section is set but empty, don't added to the  $td_fonts_save
            if(isset($user_custom_fonts_array[$section_id]) and empty($typo_section)) {
                //do nothing
            } else {
                //if the section exists in the database but is not in the saving array, then added to the saving array
                if (array_key_exists($section_id, $font_sections_from_db) and !empty($font_sections_from_db[$section_id]) and !array_key_exists($section_id, $td_fonts_save)) {
                    $td_fonts_save[$section_id] = $font_sections_from_db[$section_id];
                }
            }
        }




        /**
         * form css_files buffer for google
        **/
        //add to google style link the fonts names
        if(!empty($temp_google_fonts)) {
            foreach($temp_google_fonts as $font_id_from_list) {
                if(!empty($temp_css_google_files)) {
                    $temp_css_google_files .= '|';
                }
                $temp_css_google_files .= str_replace(' ', '+', td_fonts::$font_names_google_list[$font_id_from_list]) . ':400,700';
            }
        }


        //check the character set saved in the database
        $array_google_char_set = array(
                                        'g_latin',
                                        'g_latin-ext',
                                        'g_cyrillic',
                                        'g_cyrillic-ext',
                                        'g_greek',
                                        'g_greek-ext',
                                        'g_devanagari',
                                        'g_vietnamese',
                                        'g_khmer'
        );
        foreach($array_google_char_set as $val_charset) {
            if(!empty(td_global::$td_options['td_fonts_user_inserted'][$val_charset])) {
                if(!empty($temp_css_google_character_set)){
                    $temp_css_google_character_set .= ',';
                }
                $temp_css_google_character_set .= td_global::$td_options['td_fonts_user_inserted'][$val_charset];
            }
        }



        //form the google css files buffer
        if(!empty($temp_css_google_files)) {
            $css_files = "://fonts.googleapis.com/css?family=" . $temp_css_google_files;

            if(!empty($temp_css_google_character_set)) {
                $css_files .= '&subset=' . $temp_css_google_character_set;
            }
        }

        //add the user font settings to the option string that going to the database
        td_global::$td_options['td_fonts'] = $td_fonts_save;

        //add the font buffers to the option string that going to the database
        td_global::$td_options['td_fonts_js_buffer'] = $js_buffer;
        td_global::$td_options['td_fonts_css_buffer'] = $css_buffer;
        td_global::$td_options['td_fonts_css_files'] = $css_files;

    }



    static function ajax_view_panel_loading(){

        //if user is logged in and can switch themes
        if (current_user_can('switch_themes')) {

            //DECODE PANEL PARAMS

            //json decode ajax_view
            if (!empty($_POST['td_ajax_view'])) {
                $td_ajax_view = $_POST['td_ajax_view'];//json_decode(stripslashes($_POST['td_view']), true);//didn't work with json_decode
            }

            //json decode category_id (used by category panel)
            if (!empty($_POST['category_id'])) {
                $td_category_id = json_decode(stripslashes($_POST['category_id']), true);
            }

            //json decode category_id (used by typography panel)
            if (!empty($_POST['section_id'])) {
                $td_section_id = $_POST['section_id'];//$td_section_id = json_decode(stripslashes($_POST['section_id']), true);//didn't work with json_decode
            }


            //FORMAT PANEL AND SEND DATA BACK
            $buffy = '';
            switch ($td_ajax_view) {
                case 'td_category':
                    include_once('ajax_views/td_category.php');

                    $buffy = td_category_form_ajax($td_category_id) . '<div class="td-clear"></div>';
                    break;


                case 'td_translations':
                    include_once('ajax_views/td_translations.php');

                    $buffy = td_translations_form_ajax() . '<div class="td-clear"></div>';
                    break;


                case 'td_theme_fonts':
                    if (!empty($td_section_id)) {

                        include_once('ajax_views/td_theme_fonts.php');

                        //call the generate function to create the fonts control pannel
                        $object_custom_typography_ajax = new td_panel_custom_typography_ajax();

                        $buffy = $object_custom_typography_ajax->td_custom_typology_generate_font_controls($td_section_id) . '<div class="td-clear"></div>';
                    }
                    break;
            }


            //return the view counts
            die(json_encode($buffy));

        } else {

            die();
        }//end if user can switch themes
    }

}


//save the panel classical way
//td_panel_data_source::update();


//AJAX FORM SAVING
add_action( 'wp_ajax_nopriv_td_ajax_update_panel', array('td_panel_data_source', 'update') );
add_action( 'wp_ajax_td_ajax_update_panel', array('td_panel_data_source', 'update') );

//print_r($_POST);


//AJAX VIEW PANEL LOADING
add_action( 'wp_ajax_nopriv_td_ajax_view_panel_loading', array('td_panel_data_source', 'ajax_view_panel_loading') );
add_action( 'wp_ajax_td_ajax_view_panel_loading', array('td_panel_data_source', 'ajax_view_panel_loading') );