<?php



function td_ajax_block(){
    global $post;
    //get the data from ajax() call


    if (!empty($_POST['td_atts'])) {
        $td_atts = json_decode(stripslashes($_POST['td_atts']), true); //current block args
    } else {
        $td_atts = ''; //not ok
    }

    if (!empty($_POST['td_cur_cat'])) {
        $td_cur_cat = $_POST['td_cur_cat']; //the new id filter
    } else {
        $td_cur_cat = '';
    }

    if (!empty($_POST['td_column_number'])) {
        $td_column_number =  $_POST['td_column_number']; //the block is on x columns
    } else {
        $td_column_number = 0; //not ok
    }


    if (!empty($_POST['td_current_page'])) {
        $td_current_page = $_POST['td_current_page'];
    } else {
        $td_current_page = 1;
    }

    if (!empty($td_cur_cat)) {
        $td_atts['category_ids'] = $td_cur_cat;
        unset($td_atts['category_id']);
    }

    if (!empty($_POST['td_block_id'])) {
        $td_block_id = $_POST['td_block_id'];
    } else {
        $td_block_id = ''; //not ok
    }

    if (!empty($_POST['block_type'])) {
        $block_type = $_POST['block_type'];
    } else {
        $block_type = '';
    }


    $td_query = &td_data_source::get_wp_query($td_atts, $td_current_page); //by ref  do the query


    $buffy ='';


    $buffy .= td_global_blocks::get_instance($block_type)->inner($td_query->posts, $td_column_number, '', true);



    //pagination
    $td_hide_prev = false;
    $td_hide_next = false;
    if ($td_current_page == 1) {
        $td_hide_prev = true; //hide link on page 1
    }

    if ($td_current_page >= $td_query->max_num_pages ) {
        $td_hide_next = true; //hide link on last page
    }


    $buffyArray = array(
        'td_data' => $buffy,
        'td_block_id' => $td_block_id,
        'td_cur_cat' => $td_cur_cat,
        'td_hide_prev' => $td_hide_prev,
        'td_hide_next' => $td_hide_next
    );

    // Return the String
    die(json_encode($buffyArray));
}

// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_td_ajax_block', 'td_ajax_block' );
add_action( 'wp_ajax_td_ajax_block', 'td_ajax_block' );









function td_ajax_search() {
    $buffy = '';
	$buffy_msg = '';

    //the search string
    if (!empty($_POST['td_string'])) {
        $td_string = $_POST['td_string'];
    } else {
        $td_string = '';
    }

    //get the data
    $td_query = &td_data_source::get_wp_query_search($td_string); //by ref  do the query

    //build the results
    if (!empty($td_query->posts)) {
        foreach ($td_query->posts as $post) {
            $td_module_aj_search = new td_module_aj_search($post);
            $buffy .= $td_module_aj_search->render();
        }
    }

    if (count($td_query->posts) == 0) {
        //no results
        $buffy = '<div class="result-msg no-result">' . __td('No results') . '</div>';
    } else {
        //show the resutls
        $buffy_msg .= '<div class="result-msg"><a href="' . get_search_link($td_string) . '">' . __td('View all results') . '</a></div>';
        //add wrap
        $buffy = '<div class="td-aj-search-results">' . $buffy . '</div>' . $buffy_msg;
    }





    //prepare array for ajax
    $buffyArray = array(
        'td_data' => $buffy,
        'td_total_results' => 2,
        'td_total_in_list' => count($td_query->posts),
        'td_search_query'=> $td_string
    );





    // Return the String
    die(json_encode($buffyArray));
}
add_action( 'wp_ajax_nopriv_td_ajax_search', 'td_ajax_search' );
add_action( 'wp_ajax_td_ajax_search', 'td_ajax_search' );



/***
 * SECTION FOR MODAL LOG IN, REGISTER AND REMEMBER PASSWORD
 */

//the regular expression for email
//$td_mod_pattern_email = '/^[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-zA-Z0-9]@[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/';

//action for login
add_action( 'wp_ajax_nopriv_td_mod_login', 'td_mod_login' );
add_action( 'wp_ajax_td_mod_login', 'td_mod_login' );

/**
 * handles the ajax request from modal window for: Log In
 */
function td_mod_login(){
    global $post;

    //json login fail
    $json_login_fail = json_encode(array('login', 0, __td('User or password incorrect!')));

    //get the email address from ajax() call
    $login_email = '';
    if (!empty($_POST['email'])) {
        $login_email = $_POST['email'];
    }

    //get password from ajax() call
    $login_password = '';
    if (!empty($_POST['pass'])) {
        $login_password = $_POST['pass'];
    }

    //try to login
    if (!empty($login_email) and !empty($login_password)) {
        $obj_wp_login = td_login::login_user($login_email, $login_password);

        if (is_wp_error($obj_wp_login)) {
            die($json_login_fail);
        } else {
            die(json_encode(array('login', 1,'OK')));
        }

    } else {
        die($json_login_fail);
    }
}


//action for register
add_action( 'wp_ajax_nopriv_td_mod_register', 'td_mod_register' );
add_action( 'wp_ajax_td_mod_register', 'td_mod_register' );

/**
 * handles the ajax request from modal window for: Register
 */
function td_mod_register(){
    //if registration is open from wp-admin/Settings,  then try to create a new user
    if(get_option('users_can_register') == 1){

        global $post;

        //json predifined return text
        $json_fail = json_encode(array('register', 0, __td('Email or username incorrect!')));
        $json_user_pass_exists = json_encode(array('register', 0, __td('User or email already exists!')));

        //get the email address from ajax() call
        $register_email = '';
        if (!empty($_POST['email'])) {
            $register_email = $_POST['email'];
        }

        //get user from ajax() call
        $register_user = '';
        if (!empty($_POST['user'])) {
            $register_user = $_POST['user'];
        }

        //try to login
        if (!empty($register_email) and !empty($register_user)) {

            //check user existence before adding it
            $user_id = username_exists($register_user);

            if (!$user_id and email_exists($register_email) == false ) {

                //generate random pass
                $random_password = wp_generate_password($length=12, $include_standard_special_chars=false);

                //create user
                $user_id = wp_create_user($register_user, $random_password, $register_email);

                if (intval($user_id) > 0) {
                    //send email to $register_email
                    wp_new_user_notification($user_id, $random_password);

                    die(json_encode(array('register', 1,__td('Please check you email (index or spam folder), the password was sent there.'))));
                } else {
                    die($json_user_pass_exists);
                }

            } else {
                die($json_user_pass_exists);
            }

        } else {
            die($json_fail);
        }
    }//end if admin permits registration
}


//action for remember password
add_action( 'wp_ajax_nopriv_td_mod_remember_pass', 'td_mod_remember_pass' );
add_action( 'wp_ajax_td_mod_remember_pass', 'td_mod_remember_pass' );

/**
 * handles the ajax request from modal window for: Register
 */
function td_mod_remember_pass(){
    global $post;

    //json predifined return text
    $json_fail = json_encode(array('remember_pass', 0, __td('Email address not found!')));

    //get the email address from ajax() call
    $remember_email = '';
    if (!empty($_POST['email'])) {
        $remember_email = $_POST['email'];
    }

    if (td_login::recover_password($remember_email)) {
        die(json_encode(array('remember_pass', 1, __td('Your password is reset, check your email.'))));
    } else {
        die($json_fail);
    }
}


//action for new sidebar
add_action( 'wp_ajax_nopriv_td_ajax_new_sidebar', 'td_ajax_new_sidebar' );
add_action( 'wp_ajax_td_ajax_new_sidebar', 'td_ajax_new_sidebar' );

/**
 * adds a new sidebar in the td_option (td_008) array from wp_option table
 */
function td_ajax_new_sidebar() {
    global $post;
    $list_current_sidebars = '';

    //nr of chars displayd as name option
    $sub_str_val = 35;

    //add new sidebar
    $if_add_new_sidebar = 1;

    //get the new sidebar name from ajax() call
    $new_sidebar_name = '';
    if (!empty($_POST['sidebar'])) {
        $new_sidebar_name = trim($_POST['sidebar']);
    }

    //get theme settings (sidebars) from wp_options
    //@ to check with Radu O and see if to use this function or  td_panel_data_source::read ???
    $theme_options = get_option(TD_THEME_OPTIONS_NAME);

    //get the sidebar array if exists
    if(array_key_exists('sidebars', $theme_options) && !empty($theme_options['sidebars'])) {
        $theme_sidebars = $theme_options['sidebars'];
    }

    //default sidebar
    $list_current_sidebars .= '<div class="td-option-sidebar-wrapper"><a class="td-option-sidebar" data-area-dsp-id="xxx_replace_xxx" title="Default Sidebar">Default Sidebar</a></div>';

    if(!empty($theme_sidebars)) {
        //check to see if there is already a sidebar with that name
        foreach($theme_sidebars as $key_sidebar_option => $sidebar_option){
            if($new_sidebar_name == $sidebar_option) {
                $if_add_new_sidebar = 0;
            }

            //create a list with sidebars to be returned, the text `xxx_replace_xxx` will be replace with the id of the controler
            $list_current_sidebars .= '<div class="td-option-sidebar-wrapper"><a class="td-option-sidebar" data-area-dsp-id="xxx_replace_xxx" title="' . $sidebar_option . '">' .  substr(str_replace(array('"', "'"), '`', $sidebar_option), 0, $sub_str_val) . '</a><a class="td-delete-sidebar-option" data-sidebar-key="' . $key_sidebar_option . '"></a></div>';
        }
    }

    //check for empty strings
    if(empty($new_sidebar_name)) {
        $if_add_new_sidebar = 0;
        die(json_encode(array('td_bool_value' => '0', 'td_msg' => 'Please insert a name for your new sidebar!')));

    }

    //add the new sidebar
    if($if_add_new_sidebar == 1){
        //generating id of the sidebar in the theme_option (td_008) string in wp_option table
        $sidebar_unique_id = uniqid() . '_' . rand(1, 999999);
        $theme_sidebars[$sidebar_unique_id] = $new_sidebar_name;

        //this is the new list with sidebars
        $theme_options['sidebars'] = $theme_sidebars;

        //update the td_options into wp_option table
        update_option(TD_THEME_OPTIONS_NAME, $theme_options);

        //add the new sidebar to the existing list
        $list_current_sidebars .= '<div class="td-option-sidebar-wrapper"><a class="td-option-sidebar" data-area-dsp-id="xxx_replace_xxx" data-sidebar-key="' . $sidebar_unique_id . '" title="' . $new_sidebar_name . '">' . substr(str_replace(array('"', "'"), '`', $new_sidebar_name), 0, $sub_str_val) . '</a><a class="td-delete-sidebar-option" data-sidebar-key="' . $sidebar_unique_id . '"></a></div>';

        die(json_encode(array('td_bool_value' => '1', 'td_msg' => 'Succes', 'value_insert' => $list_current_sidebars, 'value_selected' => substr(str_replace(array('"', "'"), '`', $new_sidebar_name), 0, $sub_str_val))));

    } else {
        die(json_encode(array('td_bool_value' => '0', 'td_msg' => 'This name is already used as a sidebar name. Please use another name!')));
    }

}


//action to delete sidebar
add_action( 'wp_ajax_nopriv_td_ajax_delete_sidebar', 'td_ajax_delete_sidebar' );
add_action( 'wp_ajax_td_ajax_delete_sidebar', 'td_ajax_delete_sidebar' );

/**
 * removes a sidebar from the td_option (td_008) array from wp_option table
 */
function td_ajax_delete_sidebar() {
    global $post;

    //nr of chars displayd as name option
    $sub_str_val = 35;

    $list_current_sidebars = $value_deleted_sidebar = '';

    //get the sidebar key from ajax() call
    $sidebar_key_in_array = '';
    if (!empty($_POST['sidebar'])) {
        $sidebar_key_in_array = trim($_POST['sidebar']);
    }

    //get theme settings (sidebars) from wp_options
    //@ to check with Radu O and see if to use this function or  td_panel_data_source::read ???
    $theme_options = get_option(TD_THEME_OPTIONS_NAME);

    //get the sidebar array if exists
    if(array_key_exists('sidebars', $theme_options) && !empty($theme_options['sidebars'])) {
        $theme_sidebars = $theme_options['sidebars'];
    }

    //option for default sidebar
    $list_current_sidebars .= '<div class="td-option-sidebar-wrapper"><a class="td-option-sidebar" data-area-dsp-id="xxx_replace_xxx" title="Default Sidebar">Default Sidebar</a></div>';

    if(!empty($theme_sidebars)) {
        foreach($theme_sidebars as $key_sidebar_option => $sidebar_option){
            if($key_sidebar_option == $sidebar_key_in_array) {

                //take the value to send it back, to be mached againt all pull down controllers, to remove this option if selected
                $value_deleted_sidebar = trim($sidebar_option);

                //removes the sidebar from the array of sidebars
                unset($theme_sidebars[$key_sidebar_option]);
            } else {
                //create a list with sidebars to be returned, the text `xxx_replace_xxx` will be replace with the id of the controler
                $list_current_sidebars .= '<div class="td-option-sidebar-wrapper"><a class="td-option-sidebar" data-area-dsp-id="xxx_replace_xxx" title="' . $sidebar_option . '">' . substr(str_replace(array('"', "'"), '`', $sidebar_option), 0, $sub_str_val) . '</a><a class="td-delete-sidebar-option" data-sidebar-key="' . $key_sidebar_option . '"></a></div>';
            }
        }

        //this is the new list with sidebars
        $theme_options['sidebars'] = $theme_sidebars;

        //update the td_options into wp_option table
        update_option(TD_THEME_OPTIONS_NAME, $theme_options);

        die(json_encode(array('td_bool_value' => '1', 'td_msg' => 'Succes', 'value_insert' => $list_current_sidebars, 'value_to_march_del' => $value_deleted_sidebar)));
    }

}


/**
 * Update the view counter for single post page
 */
function td_ajax_update_views() {

    //get the post ids // iy you don't send data encoded with json the remove json_decode(stripslashes(
    if (!empty($_POST['td_post_ids'])) {
        $td_post_id = json_decode(stripslashes($_POST['td_post_ids']));

        //error check
        if (empty($td_post_id[0])) {
            $td_post_id[0] = 0;
        }

        //get the current post count
        $current_post_count = td_page_views::get_page_views($td_post_id[0]);
        //echo($current_post_count);

        $new_post_count = $current_post_count + 1;

        //update the count
        update_post_meta($td_post_id[0], td_page_views::$post_view_counter_key, $new_post_count);

        die(json_encode(array($td_post_id[0]=>$new_post_count)));
    }
}
add_action( 'wp_ajax_nopriv_td_ajax_update_views', 'td_ajax_update_views' );
add_action( 'wp_ajax_td_ajax_update_views', 'td_ajax_update_views' );


/**
 * Get the views counter for all posts on a page where unique articles is set
 */
function td_ajax_get_views() {

    //get the post ids // iy you don't send data encoded with json the remove json_decode(stripslashes(
    if (!empty($_POST['td_post_ids'])) {
        $td_post_ids = json_decode(stripslashes($_POST['td_post_ids']));

        //will hold the return array
        $buffy = array();

        //this check for arrays with values // and count($td_post_ids) > 0
        if(!empty($td_post_ids) and is_array($td_post_ids)) {

            //this check for arrays with values
            foreach($td_post_ids as $post_id) {
              $buffy[$post_id] = td_page_views::get_page_views($post_id);
            }

            //return the view counts
            die(json_encode($buffy));
        }
    }
}
add_action( 'wp_ajax_nopriv_td_ajax_get_views', 'td_ajax_get_views' );
add_action( 'wp_ajax_td_ajax_get_views', 'td_ajax_get_views' );