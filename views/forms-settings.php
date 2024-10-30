<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$token = esc_attr( get_option('api_key'));
if ( $token !== "" ) {

    KBApi::setToken($token);
    $kbFields       = new KBApi('fields');
    $fields         = $kbFields->get();
    $decodedResult  = json_decode(json_encode($fields), true); ?>


<div class="wrap">
    <h2>Placeholder settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'placeholder-settings-group' );
        do_settings_sections( 'placeholder-settings-group' );

                function show_fields ($mainArray) { ?>
                    <table id="apifields">
                        <tr>
                            <th><p><b><?php     esc_html_e( 'Fieldname', 'hellodialog' )         ?></b></p></th>
                            <th><p><b><?php     esc_html_e( 'Data type', 'hellodialog' )         ?></b></p></th>
                            <th><p><b><?php     esc_html_e( 'Placeholder', 'hellodialog' )              ?></b></p></th>
                        </tr>
                        <?php

                        foreach ( $mainArray as $singlearray ) {

                            if ( $singlearray['user_viewable'] == 1 ) {
                                echo "<tr>";
                                echo "<td><p>" . $singlearray['name'] . "</p></td>";
                                echo "<td><p>" . $singlearray['type'] . "</p></td>";
                                $placeholderoption = str_replace(' ', '', 'placeholder_'.$singlearray['name']);
                                echo "<td><p>";

                                if ( $singlearray['type'] != "Multiselect" && $singlearray['type'] != "Date" ) :
                                echo "<input type='text' name='".$placeholderoption."' value='".esc_attr( get_option($placeholderoption) )."'>";
                                endif;
                                echo "</p></td>";
                                echo "</tr>";
                            }
                        }
                    echo "</table>";
                }

                show_fields($decodedResult);
            } else {
                esc_html_e( 'Set API key to show available fields', 'hellodialog' );
            } ?>
    <?php submit_button(); ?>
    </form>
</div>
<?php ?>