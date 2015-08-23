/**
 * Created by ra on 6/27/14.
 * copyright tagDiv 2014
 * V 1.1 - better iOS 8 support
 */


var td_affix = {

    //settings, obtained from ext
    menu_selector: '', //the affix menu (this element will get the td-affix)
    menu_wrap_selector: '', //the menu wrapper / placeholder
    tds_snap_menu: '', //the panel setting


    top_offset: 0, //how much the menu is moved from the original position when it's affixed
    menu_offset: 0, //used to hide the menu on scroll
    is_requestAnimationFrame_running:false, //prevent multiple calls to requestAnimationFrame
    is_menu_affix: false, //the current state of the menu, true if the menu is affix
    is_top_menu:false, //true when the menu is at the top of the screen (0px topScroll)

    //menu offset boundaries - so we do not fire the animation event when the boundary is hit
    menu_offset_max_hit: false,
    menu_offset_min_hit: true,


    scroll_window_scrollTop_last: 0, //last scrollTop position, used to calculate the scroll direction

    /**
     * run the affix, we use the menu wrap selector to compute the menu position from top
     *
       {
            menu_selector: '.td-header-main-menu',
            menu_wrap_selector: '.td-header-menu-wrap',
            tds_snap_menu: td_util.get_backend_var('tds_snap_menu')
        }
     */
    init : function init (atts) {

        //read the settings
        td_affix.menu_selector = atts.menu_selector;
        td_affix.menu_wrap_selector = atts.menu_wrap_selector;
        td_affix.tds_snap_menu = atts.tds_snap_menu;

        //the snap menu is disabled from the panel
        if (!td_affix.tds_snap_menu) {
            return;
        }

        //compute on semi dom ready
        td_affix.compute_top();


        //recompute when all the page + logos are loaded
        jQuery(window).load(function() {
            td_affix.compute_top();

            //recompute after 1 sec for retarded phones
            setTimeout(function(){
                td_affix.compute_top();
            }, 1000);
        });




    },

    /**
     * called by td_events.js on scroll
     */
    td_events_scroll: function td_events_scroll(scrollTop) {

        //do not run if we don't have a snap menu
        if (!td_affix.tds_snap_menu) {
            return;
        }


        /*  ----------------------------------------------------------------------------
            scroll direction + delta (used by affix for now)
            to run thios code:
             - td_affix.tds_snap_menu != '' (from above)
             - td_affix.tds_snap_menu != 'snap'
         */
        if (td_affix.tds_snap_menu != 'snap') { //do not run on snap
            if ((td_affix.tds_snap_menu != 'smart_snap_mobile' || td_detect.is_phone_screen === true)) {  // different from smart_snap_mobile or td_detect.is_phone_screen === true
                //console.log('rrr');
                var scroll_direction = '';
                var scrollDelta = 0;

                //check the direction
                if (scrollTop != td_affix.scroll_window_scrollTop_last) { //compute direction only if we have different last scroll top
                    // compute the direction of the scroll
                    if (scrollTop > td_affix.scroll_window_scrollTop_last) {
                        scroll_direction = 'down';
                    } else {
                        scroll_direction = 'up';
                    }
                    //calculate the scroll delta
                    scrollDelta = Math.abs(scrollTop - td_affix.scroll_window_scrollTop_last);
                }

                td_affix.scroll_window_scrollTop_last = scrollTop;
            }
        }

        /*  ---------------------------------------------------------------------------- */



        //if the menu is in the affix state
        if (scrollTop > td_affix.top_offset || td_affix.is_top_menu === true) {

            //get the menu element
            var td_affix_menu_element = jQuery(td_affix.menu_selector);
            
            //turn affix on for it
            td_affix._affix_on(td_affix_menu_element);


            //if the menu is only with snap or we are on smart_snap_mobile + mobile, our job here in this function is done, return
            if (td_affix.tds_snap_menu == 'snap' || (td_affix.tds_snap_menu =='smart_snap_mobile' && td_detect.is_phone_screen === false)) {
                return;
            }

            /*    ---  end simple snap  ---  */


            /*  ----------------------------------------------------------------------------
                check scroll directions (we may also have scroll_direction = '', that's why we have to check for the specific state (up or down))
             */



            // boundary check - to not run the position on each scroll event
            if ((td_affix.menu_offset_max_hit === false && scroll_direction=='down') || (td_affix.menu_offset_min_hit === false && scroll_direction=='up')) {
                //request animation frame
                if (td_affix.is_requestAnimationFrame_running === false) {
                    window.requestAnimationFrame(function(){

                        var offset = 0;


                        if (scrollTop > 0) { // ios returns negative scrollTop values
                            if (scroll_direction == 'down') {

                                //compute the offset
                                offset = td_affix.menu_offset - scrollDelta;
                                if (offset < -80) {
                                    offset = -80;
                                }

                            } else if (scroll_direction == 'up') {
                                //compute the offset
                                offset = td_affix.menu_offset + scrollDelta;
                                if (offset > 0) {
                                    offset = 0;
                                }
                            }

                        }

                        //td_debug.log_live(scroll_direction + ' | scrollTop: ' + scrollTop + '  | offset: ' + offset);

                        td_affix.is_requestAnimationFrame_running = true;

                        //move the menu
                        td_util.td_move_y(td_affix_menu_element[0], offset);
                        //td_affix_menu_element.css({top: (offset) + 'px'});  //legacy menu move code

                        //check boundaries
                        if (offset == 0) {
                            td_affix.menu_offset_min_hit = true;
                        } else {
                            td_affix.menu_offset_min_hit = false;
                        }

                        if (offset == -80) {
                            td_affix.menu_offset_max_hit = true;
                            //also hide the menu when it's 100% out of view on ios - the safari header is transparent and we can see the menu
                            if(td_detect.is_ios === true) {
                                td_affix_menu_element.hide();
                            }
                        } else {
                            td_affix.menu_offset_max_hit = false;

                            if(td_detect.is_ios === true) { //ios safari fix
                                td_affix_menu_element.show();
                            }
                        }


                        td_affix.is_requestAnimationFrame_running = false;




                        td_affix.menu_offset = offset; //update the current offset of the menu


                    },td_affix_menu_element[0]);

                }
                //console.log(offset + ' ' + scroll_direction);

            } //end boundary check



        } else {
            td_affix._affix_off(jQuery(td_affix.menu_selector));
        }

    },


    /**
     * calculates the affix point (the distance from the top when affix should be enabled)
     * @see td_affix.init()
     * @see td_events
     */
    compute_top: function compute_top() {
        td_affix.top_offset = jQuery(td_affix.menu_wrap_selector).offset().top;

        //check to see if the menu is at the top of the screen
        if (td_affix.top_offset == 1) {
            //switch to affix - because the menu is at the top of the page
            //td_affix._affix_on(jQuery(td_affix.menu_selector));
            td_affix.is_top_menu = true;
        } else {
            //check to see the current top offset
            td_affix.is_top_menu = false;

        }
        td_affix.td_events_scroll(jQuery(window).scrollTop());

        //alert(td_affix.top_offset);
        //console.log('computed: ' + td_affix.top_offset);
    },



    /**
     * turns affix on for the menu element
     * @param td_affix_menu_element
     * @private
     */
    _affix_on: function _affix_on(td_affix_menu_element) {
        if (td_affix.is_menu_affix === false) {
            //make the menu fixed
            td_affix_menu_element.addClass('td-affix');

            //add body-td-affix class on body for header style 8 -> when scrolling down the window jumps 76px up when the menu is changing from header style 8 default to header style 8 affix
            jQuery('body').addClass('body-td-affix');

            td_affix.is_menu_affix = true;
        }
    },



    /**
     * Turns affix off for the menu element
     * @param td_affix_menu_element
     * @private
     */
    _affix_off: function _affix_off(td_affix_menu_element) {
        if (td_affix.is_menu_affix === true) {
            //make the menu normal
            jQuery(td_affix.menu_selector).removeClass('td-affix');

            //remove body-td-affix class on body for header style 8 -> when scrolling down the window jumps 76px up when the menu is changing from header style 8 default to header style 8 affix
            jQuery('body').removeClass('body-td-affix');

            td_affix.is_menu_affix = false;

            //move the menu to 0 (ios seems to skip animation frames)
            td_util.td_move_y(td_affix_menu_element[0], 0);

            if(td_detect.is_ios === true) {
                td_affix_menu_element.show();
            }

        }
    }



};



/*  ----------------------------------------------------------------------------
    Affix menu
 */
td_affix.init({
    menu_selector: '.td-menu-background',
    menu_wrap_selector: '.td-menu-placeholder',
    tds_snap_menu: td_util.get_backend_var('tds_snap_menu')
});


