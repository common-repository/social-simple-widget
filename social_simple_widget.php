<?php
/*
* Plugin Name: Social Simple Widget
* Description: Simply link your social profile
* Author: mrinal013
* Author URI: https://github.com/mrinal013
* Version: 2.3
* License: GPLv3
* Text Domain: social-simple-widget


Social Simple Widget is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Social Simple Widget is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Social Simple Widget. If not, see http://www.gnu.org/licenses/gpl-3.0.html.
*/
defined ('ABSPATH') or die("Direct Access is not allowed");


class Social_Simple_Widget extends WP_Widget {

	public function __construct() {

		//parent::__construct( strtolower( __CLASS__ ), 'Social Simple Widget' );
		parent::__construct(
			'social_simple_widget', // Base ID
			'Social Simple Widget', // Name

			array( 'description' => __( 'Simply link your social profile', 'social-simple-widget' ), ) // Args
		);

		add_action("wp_enqueue_scripts", array( $this, "front_style" ) );
		add_action("admin_enqueue_scripts", array( $this, "back_style" ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'print_scripts' ), 9999 );
	}

	/**
	 * Print scripts.
	 *
	 * @since 1.0
	 */
	public function print_scripts() {
		?>
		<script>
			( function( $ ){
				function initColorPicker( widget ) {
					widget.find( '.color-picker' ).wpColorPicker( {
						change: _.throttle( function() { // For Customizer
							$(this).trigger( 'change' );
						}, 3000 )
					});
				}

				function onFormUpdate( event, widget ) {
					initColorPicker( widget );
				}

				$( document ).on( 'widget-added widget-updated', onFormUpdate );

				$( document ).ready( function() {
					$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
						initColorPicker( $( this ) );
					} );
				} );
			}( jQuery ) );
		</script>
		<?php
	}

	public function widget( $args, $instance ) {

		$url = $instance['fields'];

		$title_center = isset( $instance['title_center'] ) ? $instance['title_center'] : false;

		$link_open = isset( $instance['link_open'] ) ? $instance['link_open'] : false;

		$target = ($link_open) ? "_blank" : "_self";

		$size = empty($instance['size']) ? '' : $instance['size'];

		$style = empty($instance['style']) ? '' : $instance['style'];
		//echo var_dump($instance['style']);

		// Colors
		$color1 = ( ! empty( $instance['color1'] ) ) ? $instance['color1'] : '#fff';
		$color2 = ( ! empty( $instance['color2'] ) ) ? $instance['color2'] : '#000';

		if ( ! empty( $size ) ) {
			if ( 'Small' === $size ) { $size = 'lg'; }
			elseif ( 'Medium' === $size ) { $size = '3x'; }
			elseif ( 'Large' === $size ) { $size = '5x'; }
		}

		if ( $title_center ) echo "<style>h2{text-align:center;}</style>";

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'];
		}

		foreach ( $url as $value ) {

			$domains = array(
					'angellist', 'adn', 'android', 'apple', 'bitbucket', 'behance', 'btc',
					'cc-discover', 'cc-amex', 'cc-paypal', 'cc-mastercard', 'css3', 'drupal',
					'expeditedssl', 'facebook', 'flickr', 'gratipay', 'html5',
					'plus', 'twitter', 'linkedin', 'soundcloud',
					'instagram', 'github', 'joomla', 'maxcdn', 'deviantart', 'pinterest'
					);


			foreach ( $domains as $domain ) {
				if ( false !== strpos( $value, $domain ) ) {
					$site = $domain;
					break;
				}
				else {
					$site = ' ';
				}
			}

			if ( filter_var( $value, FILTER_VALIDATE_URL ) ) { ?>
			<a href="<?php echo esc_attr( $value ); ?>" target="<?php echo esc_attr( $target ); ?>" title="<?php echo esc_attr( $site ); ?>" class="anchor <?php echo $site; ?>">
				<span class="fa-stack fa-<?php echo esc_attr( $size ); ?>" aria-hidden="true">
				  <i class="fa <?php if( $style == 2 ) echo ' fa-circle'; elseif( $style == 3 ) echo ' fa-square'; elseif( $style == 4 ) echo ' fa-square-o'; else echo ' '; ?> fa-stack-2x" style="color:<?php if(!empty($color2)) echo $color2; ?>"></i>
				  <i class="fa fa-<?php echo $site; ?> <?php if( ( $style == 2 ) || ( $style == 3 ) ) echo ' fa-inverse'; else echo ' '; ?> fa-stack-1x" style="color: <?php if(!empty($color1)) echo $color1; ?>"></i>
				</span>
			</a>

			<?php
			}
			else {	?>
				<span title="<?php echo $site; ?>" class="<?php echo $site; ?> fa-stack fa-<?php echo $size; ?>" aria-hidden="true">
				  <i class="fa <?php if( $style == 2 ) echo ' fa-circle'; elseif( $style == 3 ) echo ' fa-square'; elseif( $style == 4 ) echo ' fa-square-o'; else echo ' '; ?> fa-stack-2x" style="color: <?php echo $color2; ?>"></i>
				  <i class="fa fa-<?php echo $site; ?> <?php if( ( $style == 2 ) || ( $style == 3 ) ) echo ' fa-inverse'; else echo ' '; ?> fa-stack-1x" style="color:<?php echo $color1; ?>"></i>
				  <i class="fa <?php if( $style == 2 ) echo ' fa-circle'; elseif( $style == 3 ) echo ' fa-square'; elseif( $style == 4 ) echo ' fa-square-o'; else echo ' '; ?> fa-stack-2x" style="color: <?php if(!empty($color2)) echo $color2; ?>"></i>
				  <i class="fa fa-<?php echo $site; ?> <?php if( ( $style == 2 ) || ( $style == 3 ) ) echo ' fa-inverse'; else echo ' '; ?> fa-stack-1x" style="color:<?php if(!empty($color1)) echo $color1; ?>"></i>
				</span>
				<?php
			}
		}
		echo $args['after_widget'];
	}


	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['fields'] = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['title_center'] = isset( $new_instance['title_center'] ) ? (bool) $new_instance['title_center'] : false;
		$instance['link_open'] = isset( $new_instance['link_open'] ) ? (bool) $new_instance['link_open'] : false;

		$instance['size'] = $new_instance['size'];

		$instance['style'] = ( isset( $new_instance['style'] ) && $new_instance['style'] > 0 && $new_instance['style'] < 5 ) ? (int) $new_instance['style'] : 0;

		$instance[ 'color1' ] = strip_tags( $new_instance['color1'] );
		$instance[ 'color2' ] = strip_tags( $new_instance['color2'] );


        if ( isset ( $new_instance['fields'] ) ) {
            foreach ( $new_instance['fields'] as $value ) {
                if ( '' !== trim( $value ) )
                    $instance['fields'][] = $value;
            }
        }
		return $instance;
	}

		public function form( $instance ) {

			$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Widget title', 'social-simple-widget' );
			$title_align = isset( $instance['title_align'] ) ? (bool) $instance['title_align'] : false;

			$title_center = isset( $instance['title_center'] ) ? (bool) $instance['title_center'] : false;

			$link_open = isset( $instance['link_open'] ) ? (bool) $instance['link_open'] : false;

		    $fields = isset ( $instance['fields'] ) ? $instance['fields'] : array();

		    $size = isset ( $instance['size'] ) ? $instance['size'] : array();

		    $style = ( isset( $instance['style'] ) && is_numeric( $instance['style'] ) ) ? (int) $instance['style'] : 1;

		    $field_num = count( $fields );
		    $fields[ $field_num ] = '';
		    $fields_html = array();
		    $fields_counter = 0;

		    $color1 = isset( $instance['color1'] ) ? $instance['color1'] : '#000';
			$color2 = isset( $instance['color2'] ) ? $instance['color2'] : '#000';


		    ?>
			<div class="ssw-settings">

				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget title', 'social-simple-widget' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
				</p>

				<p>
				    <input class="checkbox" type="checkbox"<?php checked( $title_center ); ?> id="<?php echo $this->get_field_id( 'title_center' ); ?>" name="<?php echo $this->get_field_name( 'title_center' ); ?>" />
					<label for="<?php echo $this->get_field_id( 'title_center' ); ?>"><?php _e( 'Title Center', 'social-simple-widget' ); ?></label>
				</p>

			</div>
		    <?php
		    foreach ( $fields as $name => $value )
		    {
		        $fields_html[] = sprintf(
		            '<input type="url" name="%1$s[%2$s]" value="%3$s" class="widefat feature%2$s"><br/>',
		            $this->get_field_name( 'fields' ),
		            $fields_counter,
		            esc_attr( $value )
		        );
		        $fields_counter += 1;
		        if ($fields_counter == $field_num) break;
		    }
		    ?>
		    <div class="ssw-links">
		    <?php
		    print 'Your Social Link <br />' . join( '', $fields_html );
		    ?>
		    <script>
		    var fieldname = <?php echo json_encode($this->get_field_name( 'fields' )) ?>;
		    var fieldnum = <?php echo json_encode($fields_counter - 1) ?>;

		    jQuery(function($) {
		        var count = fieldnum;
		        $('.<?php echo $this->get_field_id('addfeature') ?>').click(function() {
		            $( ".feature"+count).after("<input type='url' name='"+fieldname+"["+(count+1) +"]' value='' class='widefat feature"+ (count+1) +"'>" );
		            count++;

		        });
		    });
			</script>

		    <?php
		    echo '<br/><input class="button '.$this->get_field_id('addfeature').'" type="button" value="' . __( 'More Social', 'myvps' ) . '" id="addfeature" />';
		    ?>
		    <p>
		    <input class="checkbox" type="checkbox"<?php checked( $link_open ); ?> id="<?php echo $this->get_field_id( 'link_open' ); ?>" name="<?php echo $this->get_field_name( 'link_open' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'link_open' ); ?>"><?php _e( 'Open in new window?', 'social-simple-widget' ); ?></label>
			</p>

		    <p>
		      	<label for="<?php echo $this->get_field_id('icon-size'); ?>">Size:
		        	<select class='' id="<?php echo $this->get_field_id('size'); ?>"
		                name="<?php echo $this->get_field_name('size'); ?>" type="text">
		          		<option value='Small'<?php echo ($size=='Small')?'selected':''; ?>>Small</option>
		          		<option value='Medium'<?php echo ($size=='Medium')?'selected':''; ?>>Medium</option>
		          		<option value='Large'<?php echo ($size=='Large')?'selected':''; ?>>Large</option>
		        	</select>
		      	</label>
		    </p>

            <p>
            <legend>Style:</legend>
                <input type="radio" id="<?php echo ($this->get_field_id( 'style' ) . '-1') ?>" name="<?php echo ($this->get_field_name( 'style' )) ?>" value="1" <?php checked( $style == 1, true) ?>>
                <label for="<?php echo ($this->get_field_id( 'style' ) . '-1' ) ?>"><?php _e('Only Icon', 'social-simple-widget') ?></label> <br />

                <input type="radio" id="<?php echo ($this->get_field_id( 'style' ) . '-2') ?>" name="<?php echo ($this->get_field_name( 'style' )) ?>" value="2" <?php checked( $style == 2, true) ?>>
                <label for="<?php echo ($this->get_field_id( 'style' ) . '-2' ) ?>"><?php _e('Round fill') ?></label> <br />

                <input type="radio" id="<?php echo ($this->get_field_id( 'style' ) . '-3') ?>" name="<?php echo ($this->get_field_name( 'style' )) ?>" value="3" <?php checked( $style == 3, true) ?>>
                <label for="<?php echo ($this->get_field_id( 'style' ) . '-3' ) ?>"><?php _e('Square fill') ?></label> <br />

                <input type="radio" id="<?php echo ($this->get_field_id( 'style' ) . '-4') ?>" name="<?php echo ($this->get_field_name( 'style' )) ?>" value="4" <?php checked( $style == 4, true) ?>>
                <label for="<?php echo ($this->get_field_id( 'style' ) . '-4' ) ?>"><?php _e('Square border') ?></label> <br />
            </p>

			<p>
			<label for="<?php echo $this->get_field_id( 'color1' ); ?>"><?php _e( 'Icon Color:' ); ?></label><br>
			<input type="text" name="<?php echo $this->get_field_name( 'color1' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'color1' ); ?>" value="<?php echo $color1; ?>" data-default-color="#fff" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'color2' ); ?>"><?php _e( 'Background Color:' ); ?></label><br>
			<input type="text" name="<?php echo $this->get_field_name( 'color2' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'color2' ); ?>" value="<?php echo $color2; ?>" data-default-color="#f00" />
		</p>


		     <p class="ssw_alert">Please check urls are correct in frontend</p>

			</div>
			<?php
		}

		public function front_style() {
            wp_enqueue_style( 'fa-style', plugins_url('social-simple-widget/css/font-awesome.min.css'));
            wp_enqueue_style('frontend-css', plugins_url('social-simple-widget/css/f_style.css'));
		}

		public function back_style($hook_suffix) {
			wp_enqueue_style('backend-css', plugins_url('social-simple-widget/css/b_style.css'));

			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}
			wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'underscore' );
		
		}

} // class Social_Simple_Widget

function init_social_simple_widget(){
	register_widget("Social_Simple_Widget");
}
add_action("widgets_init","init_social_simple_widget");