<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
    <div class="wrap">
    <h2>Hellodialog Instellingen</h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'hd-plugin-settings-group' ); ?>
        <?php do_settings_sections( 'hdplugin-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'API Key: ', 'hellodialog' ) ?></th>
                <td><input type="text" name="api_key" value="<?php echo esc_attr( get_option('api_key') ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'API Status: ', 'hellodialog' ) ?></th>
                <td><?php
                    $token = esc_attr( get_option('api_key'));
                    if ( $token !== "" ) {
                        KBApi::setToken($token);
                        $kbFields       = new KBApi('fields');
                        $fields         = $kbFields->get();
                        $decodedResult  = json_decode(json_encode($fields), true);

                        foreach ( $decodedResult as $field ) {
                            //var_dump($field);
                            if ( in_array("(#601) Invalid token (API-key)", $field)){
                                $apierror =  $field['message'];
                                echo $apierror;
                            }
                        }
                        if ( ! isset ( $apierror ) ) {
                            esc_html_e( 'API connection OK!', 'hellodialog' );
                        }
                    } else {
                        esc_html_e('Configure the API key first to check the status', 'hellodialog');
                    }

                    ?>
                </td>
            </tr>

        </table>
        <?php if ( class_exists( 'WooCommerce' ) ) {?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_attr_e( 'WooCommerce settings: ', 'hellodialog' ) ?></th>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="400"><label for="checkout_signup"><?php esc_attr_e( 'Show sign-up checkbox on WooCommerce checkout page', 'hellodialog' ); ?></label></td>
                    <td ><input id="wc_hook" name="wc_hook" type="checkbox" <?php checked( 'wc_hook' , get_option('wc_hook') ); ?> value="wc_hook" /></td>
                </tr>
                <tr>
                    <td width="400"><label for="checkout_signup"><?php esc_html_e( 'Label title for WooCommerce checkbox', 'hellodialog' ); ?></label></td><?php

                    $emp = esc_attr( get_option('wc_label') );
                    if ( !empty($emp)) {
                        ?><td><input type="text" name="wc_label" value="<?php echo esc_attr( get_option('wc_label') ); ?>" /></td><?php
                    } else {
                        ?><td><input type="text" name="wc_label" value="<?php echo esc_attr_e( 'Sign up for newsletter', 'hellodialog' ); ?>" /></td><?php
                    }

                    ?>
                </tr>
            </table>
            <?php
        } ?>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_attr_e( 'Message settings: ', 'hellodialog' ) ?></th>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="400"><label for="success_string"><?php esc_attr_e( 'Success message', 'hellodialog' ); ?></label></td>
                <?php
                $emp2 = esc_attr( get_option('success_string') );
                $allowed_html = shapeSpace_allowed_html();
                $sanitized_success_option = wp_kses(get_option('success_string'), $allowed_html);
                if ( !empty($emp2)) {
                    ?><td><input type="text" name="success_string" style="width: 400px;" value="<?php echo $sanitized_success_option ; ?>" /></td><?php
                } else {
                    ?><td><input type="text" name="success_string" style="width: 400px;" value="You have been submitted for the newsletter." /></td><?php
                }

                ?>
            </tr>
            <tr>
                <td width="400"><label for="double_string"><?php esc_attr_e( 'contact already exists', 'hellodialog' ); ?></label></td>
                <?php
                $emp3 = esc_attr( get_option('double_string') );
                $allowed_html = shapeSpace_allowed_html();
                $sanitized_success_option2 = wp_kses(get_option('double_string'), $allowed_html);
                if ( !empty($emp3)) {
                    ?><td><input type="text" name="double_string" style="width: 400px;" value="<?php echo $sanitized_success_option2 ; ?>" /></td><?php
                } else {
                    ?><td><input type="text" name="double_string" style="width: 400px;" value="You have been submitted for the newsletter." /></td><?php
                }

                ?>
            </tr>
        </table>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_attr_e( 'Display settings: ', 'hellodialog' ) ?></th>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="400"><label for="success_string"><?php esc_attr_e( 'Hide field labels', 'hellodialog' ); ?></label></td>
                <td ><input id="show_labels" name="show_labels" type="checkbox" <?php checked( 'show_labels' , get_option('show_labels') ); ?> value="show_labels" /></td>

            </tr>
        </table>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_attr_e( 'Opt-in settings: ', 'hellodialog' ) ?></th>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="400"><label for="optin_type"><?php esc_attr_e( 'Use single opt in', 'hellodialog' ); ?></label></td>
                <td ><input id="optin_type" name="optin_type" type="checkbox" <?php checked( 'optin_type' , get_option('optin_type') ); ?> value="optin_type" /></td>

            </tr>
        </table>
        <p><?php esc_attr_e( 'By enabling this option contacts will be pushed directly into Hellodialogs database. If disabled a normal opt-in will be send. ( default = disabled )', 'hellodialog' ); ?></p>

        <?php
        submit_button(); ?>
    </form>
</div>