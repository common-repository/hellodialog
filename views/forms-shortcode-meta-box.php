<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_nonce_field( basename( __FILE__ ), 'hd_nonce' ); ?>
<p><?php esc_html_e('Use the following shortcode in a post, page or widget to use this form in front-end','hellodialog');?></p>
<pre>[hellodialog id="<?php echo $post->ID;?>"]</pre>