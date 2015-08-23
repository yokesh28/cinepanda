<?php
class td_data_source {

    static $fake_loop_offset = 0; //used by the found row hook in templates to fix pagination. The blocks do not use this since we use custom pagination there.


    /**
     * converts a pagebuilde array to a wordpress query args array
     * creates the $args array from shortcodes - used by the pagebuilde + widgets + by the metabox_to_args
     * @param string $atts : the shortcode string
     * @param string $paged : page number  /1  or  /2
     * @return array
     */
    static function shortcode_to_args($atts = '', $paged = '') {
        extract(shortcode_atts(
                array(
                    'category_ids' => '',
                    'category_id' => '',
                    'tag_slug' => '',
                    'sort' => '',
                    'limit' => '', /*'limit' => 5,*/
                    'autors_id' => '',
                    'installed_post_types' => '',
                    'posts_per_page' => '',
                    'offset' => ''
                ),
                $atts
            )
        );

        //init the array
        $wp_query_args = array(
            'ignore_sticky_posts' => 1
        );

        //the query goes only via $category_ids - for both options ($category_ids and $category_id) also $category_ids overwrites $category_id
        if (!empty($category_id) and empty($category_ids)) {
            $category_ids = $category_id;
        }


        if (!empty($category_ids)) {
            $wp_query_args['cat'] = $category_ids;
        }

        if (!empty($tag_slug)) {
            $wp_query_args['tag'] = str_replace(' ', '-', $tag_slug);
        }

       // $current_day = date('j');

        switch ($sort) {
            case 'featured':
                if (!empty($category_ids)) {
                    //for each category, get the object and compose the slug
                    $cat_id_array = explode (',', $category_ids);

                    foreach ($cat_id_array as &$cat_id) {
                        $cat_id = trim($cat_id);

                        //get the category object
                        $td_tmp_cat_obj =  get_category($cat_id);

                        //make the $args
                        if (empty($wp_query_args['category_name'])) {
                            $wp_query_args['category_name'] = $td_tmp_cat_obj->slug; //get by slug (we get the children categories too)
                        } else {
                            $wp_query_args['category_name'] .= ',' . $td_tmp_cat_obj->slug; //get by slug (we get the children categories too)
                        }
                        unset($td_tmp_cat_obj);
                    }
                }

                $wp_query_args['cat'] = get_cat_ID(TD_FEATURED_CAT); //add the fetured cat
                break;
            case 'popular':
                $wp_query_args['meta_key'] = td_page_views::$post_view_counter_key;
                $wp_query_args['orderby'] = 'meta_value_num';
                $wp_query_args['order'] = 'DESC';
                break;
            case 'popular7':
                $wp_query_args['meta_key'] = td_page_views::$post_view_counter_7_day_total;
                $wp_query_args['orderby'] = 'meta_value_num';
                $wp_query_args['order'] = 'DESC';
                break;
            case 'review_high':
                $wp_query_args['meta_key'] = td_review::$td_review_key;
                $wp_query_args['orderby'] = 'meta_value_num';
                $wp_query_args['order'] = 'DESC';
                break;
            case 'random_posts':
                $wp_query_args['orderby'] = 'rand';
                break;
            case 'alphabetical_order':
                $wp_query_args['orderby'] = 'title';
                $wp_query_args['order'] = 'ASC';
                break;
            case 'comment_count':
                $wp_query_args['orderby'] = 'comment_count';
                $wp_query_args['order'] = 'DESC';
                break;
            case 'random_today':
                $wp_query_args['orderby'] = 'rand';
                $wp_query_args['year'] = date('Y');
                $wp_query_args['monthnum'] = date('n');
                $wp_query_args['day'] = date('j');
                break;
            case 'random_7_day':
                $wp_query_args['orderby'] = 'rand';
                $wp_query_args['date_query'] = array(
                            'column' => 'post_date_gmt',
                            'after' => '1 week ago'
                            );
                break;
        }

        if (!empty($autors_id)) {
            $wp_query_args['author'] = $autors_id;
        }

        //add post_type to query
        if (!empty($installed_post_types)) {
            $array_selected_post_types = array();
            $expl_installed_post_types = explode(',', $installed_post_types);

            foreach ($expl_installed_post_types as $val_this_post_type) {
                if (trim($val_this_post_type) != '') {
                    $array_selected_post_types[] = trim($val_this_post_type);
                }
            }

            $wp_query_args['post_type'] = $array_selected_post_types;//$installed_post_types;
        }

        //only show published posts
        $wp_query_args['post_status'] = 'publish';

        //show only unique posts if that setting is enabled on the template
        if (td_unique_posts::$unique_articles_enabled == true) {
            $wp_query_args['post__not_in'] = td_unique_posts::$rendered_posts_ids;
        }



        //custom pagination limit
        if (empty($limit)) {
            $limit = get_option('posts_per_page');
        }
        $wp_query_args['posts_per_page'] = $limit;


        //custom pagination
        if (!empty($paged)) {
            $wp_query_args['paged'] = $paged;
        } else {
            $wp_query_args['paged'] = 1;
        }


        // offset + custom pagination - if we have offset, wordpress overwrites the pagination and works with offset + limit
        if (!empty($offset) and $paged > 1) {
            $wp_query_args['offset'] = $offset + ( ($paged - 1) * $limit) ;
        } else {
            $wp_query_args['offset'] = $offset ;
        }


        //set this variable to pass it to the filter that fixes the pagination on the templates with fake loops. It is not used on blocks because the blocks have custom pagination
        self::$fake_loop_offset = $offset;


        //print_r($wp_query_args);

        return $wp_query_args;
    }




    /**
     * converts a post metabox value array to a wordpress query args array
     * @param $td_homepage_loop_filter - the post loop filer metadata array
     * @param string $paged
     * @return array
     */
    static function metabox_to_args($td_homepage_loop_filter, $paged = '') {


        $wp_query_args = self::shortcode_to_args($td_homepage_loop_filter, $paged);



        //$wp_query_args['paged'] = $paged;

        if (!empty($td_homepage_loop_filter['show_featured_posts'])) {
            if (empty($wp_query_args['cat'])) {
                $wp_query_args['cat'] = '-' . get_cat_ID(TD_FEATURED_CAT);
            } else {
                $wp_query_args['cat'] .= ',-' . get_cat_ID(TD_FEATURED_CAT);
            }
        }


        $wp_query_args['ignore_sticky_posts'] = 0;

        // custom pagination for the fake template loops
        if (isset($wp_query_args['offset']) and $wp_query_args['offset'] > 0) {
            //fix reported posts for the fake loops
            add_filter('found_posts', array(__CLASS__, 'hook_fix_offset_pagination'), 1, 2 );
        }


        //print_r($wp_query_args);

        return $wp_query_args;
    }

    // custom pagination for the fake template loops - used by hook
    static function hook_fix_offset_pagination($found_posts, $query) {
        remove_filter('found_posts','hook_fix_offset_pagination');
        return $found_posts - td_data_source::$fake_loop_offset;
    }





    /**
     * is used by all the blocks
     * @param string $atts
     * @param string $paged - is used by ajax
     * @return WP_Query
     */
    static function &get_wp_query ($atts = '', $paged = '') { //by ref
        $args = self::shortcode_to_args($atts, $paged);
        $td_query = new WP_Query($args);
        return $td_query;
    }


    /**
     * used by the ajax search feature
     * @param $search_string
     * @return WP_Query
     */
    static function &get_wp_query_search($search_string) {
        $args = array(
            's' => $search_string,
            'post_type' => array('post'),
            'posts_per_page' => 4,
            'post_status' => 'publish'
        );

        $td_query = new WP_Query($args);
        return $td_query;
    }







}

