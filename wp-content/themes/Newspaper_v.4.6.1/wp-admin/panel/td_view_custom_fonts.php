<?php
$td_fonts_user_inserted = 0;

if(!empty($_REQUEST['td_option']) and $_REQUEST['td_option'] == 'save_fonts') {
    if(td_panel_data_source::insert_in_system_fonts_user($_POST['td_fonts_user_insert'])) {
        $td_fonts_user_inserted = 1;
    }

}?>

<div class="wrap">
<form id="td_panel_import_export_settings" name="td_panel_import_export_settings" action="?page=td_theme_panel&td_page=td_view_custom_fonts&td_option=save_fonts" method="post">
    <div class="td-container-wrap">

        <div class="td-panel-main-header">
            <img src="<?php echo get_template_directory_uri() . '/wp-admin/images/panel/panel-wrap/panel-logo.png'?>" alt=""/>
        </div>


        <div id="td-container-left">
            <div id="td-container-right">
                <div id="td-col-left">
                    <ul class="td-panel-menu">
                        <li class="td-welcome-menu">
                            <a data-td-is-back="yes" class="td-panel-menu-active" href="?page=td_theme_panel">
                                <span class="td-sp-nav-icon td-ico-welcome"></span>
                                FONTS SETTINGS
                                <span class="td-arrow"></span>
                            </a>
                        </li>

                        <li>
                            <a data-td-is-back="yes" href="?page=td_theme_panel">
                                <span class="td-sp-nav-icon td-ico-header"></span>
                                Back
                                <span class="td-arrow"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="td-col-rigth" class="td-panel-content">


                    <!-- Custom fonts files -->
                    <div  class="td-panel-active td-panel">

                    <?php echo td_panel_generator::box_start('Documentation on how to use custom fonts', false); ?>

                        <!-- Custom Font file 1 -->
                        <div class="td-box-row">
                            <div class="td-box-description td-box-description-full">
                                <span class="td-box-title"></span>
                                <p><a href="http://forum.tagdiv.com/how-to-use-custom-fonts/" target="_blank">Documentation on how to use custom fonts</a></p>
                            </div>
                            <div class="td-box-control-full td-panel-input-wide">
                            </div>
                        </div>

                    <?php echo td_panel_generator::box_end();?>
                    </div>


                    <!-- Custom fonts files -->
                    <div  class="td-panel-active td-panel">

                        <?php echo td_panel_generator::box_start('Custom Fonts - Font Files', false); ?>

                        <!-- Custom Font file 1 -->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FILE 1</span>
                                <p>Add the link to the file ( .woff format )</p>
                            </div>
                            <div class="td-box-control-full td-panel-input-wide">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'font_file_1'
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- Custom Font name 1 -->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FAMILY 1</span>
                                <p>Type your desired name for this font</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'font_family_1'
                                ));
                                ?>
                            </div>
                        </div>


                        <div class="td-box-row">
                            <div class="td-box-description"></div>
                            <div class="td-box-control-full"></div>
                        </div>



                        <!-- Custom Font file 2 -->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FILE 2</span>
                                <p>Add the link to the file ( .woff format )</p>
                            </div>
                            <div class="td-box-control-full td-panel-input-wide">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'font_file_2'
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- Custom Font name 2 -->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FAMILY 2</span>
                                <p>Type your desired name for this font</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'font_family_2'
                                ));
                                ?>
                            </div>
                        </div>


                        <div class="td-box-row">
                            <div class="td-box-description"></div>
                            <div class="td-box-control-full"></div>
                        </div>



                        <!-- Custom Font file 3 -->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FILE 3</span>
                                <p>Add the link to the file ( .woff format )</p>
                            </div>
                            <div class="td-box-control-full td-panel-input-wide">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'font_file_3'
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- Custom Font name 3 -->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FAMILY 3</span>
                                <p>Type your desired name for this font</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'font_family_3'
                                ));
                                ?>
                            </div>
                        </div>


                        <?php echo td_panel_generator::box_end();?>
                    </div>



                    <!-- typekit.com fonts -->
                    <div  class="td-panel-active td-panel">

                        <?php echo td_panel_generator::box_start('Typekit.com Fonts', false); ?>

                        <!-- javascript from typekit.com-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">Javascript Code</span>
                                <p>Copy the javascript code from typekit.com and paste it here</p>
                            </div>
                            <div class="td-box-control-full td-panel-input-wide">
                                <?php
                                echo td_panel_generator::textarea(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'typekit_js',
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- typekit.com Custom Font font family 1-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FAMILY 1</span>
                                <p>Type your desired name for this font</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'type_kit_font_family_1'
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- typekit.com Custom Font font family 2-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FAMILY 2</span>
                                <p>Type your desired name for this font</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'type_kit_font_family_2'
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- typekit.com Custom Font font family 3-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CUSTOM FONT FAMILY 3</span>
                                <p>Type your desired name for this font</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::input(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'type_kit_font_family_3'
                                ));
                                ?>
                            </div>
                        </div>

                        <?php echo td_panel_generator::box_end();?>
                    </div>



                    <!-- google fonts settings-->
                    <div  class="td-panel-active td-panel">
                        <?php echo td_panel_generator::box_start('Google Fonts Settings', false); ?>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">LATIN SUPPORT</span>
                                <p>Load the font file with support for latin charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_latin',
                                    'true_value' => 'latin',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">LATIN EXTENDED SUPPORT</span>
                                <p>Load the font file with support for latin extended charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_latin-ext',
                                    'true_value' => 'latin-ext',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CYRILLIC SUPPORT</span>
                                <p>Load the font file with support for cyrillic charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_cyrillic',
                                    'true_value' => 'cyrillic',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">CYRILLIC EXTENDED SUPPORT</span>
                                <p>Load the font file with support for cyrillic extended charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_cyrillic-ext',
                                    'true_value' => 'cyrillic-ext',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">GREEK SUPPORT</span>
                                <p>Load the font file with support for greek charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_greek',
                                    'true_value' => 'greek',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">GREEK EXTENDED SUPPORT</span>
                                <p>Load the font file with support for greek extended charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_greek-ext',
                                    'true_value' => 'greek-ext',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">DEVANAGARI SUPPORT</span>
                                <p>Load the font file with support for devanagari charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_devanagari',
                                    'true_value' => 'devanagari',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">VIETNAMESE SUPPORT</span>
                                <p>Load the font file with support for vietnamese charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_vietnamese',
                                    'true_value' => 'vietnamese',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>


                        <!-- google fonts settings-->
                        <div class="td-box-row">
                            <div class="td-box-description">
                                <span class="td-box-title">KHMER SUPPORT</span>
                                <p>Load the font file with support for khmer charset if possible</p>
                            </div>
                            <div class="td-box-control-full">
                                <?php
                                echo td_panel_generator::checkbox(array(
                                    'ds' => 'td_fonts_user_insert',
                                    'option_id' => 'g_khmer',
                                    'true_value' => 'khmer',
                                    'false_value' => ''
                                ));
                                ?>
                            </div>
                        </div>

                        <?php echo td_panel_generator::box_end();?>
                    </div>

                </div>

            </div>

        </div>


        <div class="td-clear"></div>

        <div class="td-panel-main-footer">
            <input type="submit" id="td_button_save_panel" class="td-panel-save-button" value="SAVE SETTINGS">
        </div>

    </div>

    <div class="td-clear"></div>


</form>
</div>
<?php if($td_fonts_user_inserted == 1){?><script type="text/javascript">alert('Saved Successfully!');</script><?php }?>