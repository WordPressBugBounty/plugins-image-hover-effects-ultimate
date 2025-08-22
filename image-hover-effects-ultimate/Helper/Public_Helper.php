<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 *
 * @author $biplob018
 */
trait Public_Helper {


    public function admin_special_charecter( $data ) {
        $data = html_entity_decode( $data );
        $data = str_replace( "\'", "'", $data );
        $data = str_replace( '\"', '"', $data );
        return $data;
    }

	/**
     * Plugin Name Convert to View
     *
     * @since 9.3.0
     */
    public function name_converter( $data ) {
        $data = str_replace( '_', ' ', $data );
        $data = str_replace( '-', ' ', $data );
        $data = str_replace( '+', ' ', $data );
        return esc_html( ucwords( $data ) );
    }

	public function html_special_charecter( $data ) {
        $data = html_entity_decode( $data );
        $data = str_replace( "\'", "'", $data );
        $data = str_replace( '\"', '"', $data );
        $data = do_shortcode( $data, $ignore_html = false );
        return $data;
    }

	public function font_familly_charecter( $data ) {
        wp_enqueue_style( '' . $data . '', 'https://fonts.googleapis.com/css?family=' . $data . '', false, OXI_IMAGE_HOVER_PLUGIN_VERSION );
        $data = str_replace( '+', ' ', $data );
        $data = explode( ':', $data );
        $data = $data[0];
        $data = '"' . $data . '"';
        return $data;
    }

    public function shortcode_render( $styleid, $user ) {
		global $wpdb;
        if ( ! empty( $styleid ) && ! empty( $user ) && (int) $styleid ) :

			$style = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM " . esc_sql( $this->parent_table ) . " WHERE id = %d",
					(int) $styleid
				),
				ARRAY_A
			);

            if ( ! is_array( $style ) ) :
                echo '<p> Shortcode Deleted, kindly add currect Shortcode</p>';
                return;
            endif;
            if ( ! array_key_exists( 'rawdata', $style ) ) :
                $Installation = new \OXI_IMAGE_HOVER_PLUGINS\Classes\Installation();
                $Installation->plugin_upgrade_hook();
            endif;
            $rawdata = json_decode( stripslashes( $style['rawdata'] ), true );

            if ( ( ( is_array( $rawdata ) && array_key_exists( 'image_hover_dynamic_content', $rawdata ) && $rawdata['image_hover_dynamic_content'] == 'yes' ) ||
                ( is_array( $rawdata ) && array_key_exists( 'image_hover_dynamic_load', $rawdata ) && $rawdata['image_hover_dynamic_load'] == 'yes' ) ||
                ( is_array( $rawdata ) && array_key_exists( 'image_hover_dynamic_carousel', $rawdata ) && $rawdata['image_hover_dynamic_carousel'] == 'yes' ) ) && apply_filters( 'oxi-image-hover-plugin-version', false ) ) :
                $C = '\OXI_IMAGE_HOVER_PLUGINS\Modules\Compailer';
                if ( class_exists( $C ) ) :
                    new $C( $style, [], $user );
                endif;
            else :

				$child = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM " . esc_sql( $this->child_table ) . " WHERE styleid = %d ORDER BY id ASC",
                        (int) $styleid
                    ),
                    ARRAY_A
				);

                $name = explode( '-', ucfirst( $style['style_name'] ) );
                $C = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . ucfirst( $name[0] ) . '\Render\Effects' . $name[1];
                if ( class_exists( $C ) ) :
                    new $C( $style, $child, $user );
                endif;
            endif;
        endif;
    }


	public function validate_post( $files = '' ) {

        $rawdata = [];
        if ( ! empty( $files ) ) :
            $data = json_decode( stripslashes( $files ), true );
        endif;
        if ( is_array( $data ) ) :
            $rawdata = array_map( [ $this, 'allowed_html' ], $data );
        else :
            $rawdata = $this->allowed_html( $files );
        endif;

        return $rawdata;
    }

	public function effects_converter( $data ) {
        $data = explode( '-', $data );
        return esc_html( $data[0] );
    }

	public function icon_font_selector( $data ) {
        $icon = explode( ' ', $data );
        $fadata = get_option( 'oxi_addons_font_awesome' );
        $faversion = get_option( 'oxi_addons_font_awesome_version' );
        $faversion = explode( '||', $faversion );
        if ( $fadata != 'no' ) {
            wp_enqueue_style( 'font-awesome-' . $faversion[0], $faversion[1], false, OXI_IMAGE_HOVER_PLUGIN_VERSION );
        }
        $files = '<i class="' . esc_attr( $data ) . ' oxi-icons"></i>';
        return $files;
    }


    public function allowed_html( $rawdata ) {
        $allowed_tags = [
            'a' => [
                'class' => [],
                'href' => [],
                'rel' => [],
                'title' => [],
            ],
            'abbr' => [
                'title' => [],
            ],
            'b' => [],
            'br' => [],
            'blockquote' => [
                'cite' => [],
            ],
            'cite' => [
                'title' => [],
            ],
            'code' => [],
            'del' => [
                'datetime' => [],
                'title' => [],
            ],
            'dd' => [],
            'div' => [
                'class' => [],
                'title' => [],
                'style' => [],
                'id' => [],
            ],
            'table' => [
                'class' => [],
                'id' => [],
                'style' => [],
            ],
            'button' => [
                'class' => [],
                'type' => [],
                'value' => [],
            ],
            'thead' => [],
            'tbody' => [],
            'tr' => [],
            'td' => [],
            'dt' => [],
            'em' => [],
            'h1' => [],
            'h2' => [],
            'h3' => [],
            'h4' => [],
            'h5' => [],
            'h6' => [],
            'i' => [
                'class' => [],
            ],
            'img' => [
                'alt' => [],
                'class' => [],
                'height' => [],
                'src' => [],
                'width' => [],
            ],
            'li' => [
                'class' => [],
            ],
            'ol' => [
                'class' => [],
            ],
            'p' => [
                'class' => [],
            ],
            'q' => [
                'cite' => [],
                'title' => [],
            ],
            'span' => [
                'class' => [],
                'title' => [],
                'style' => [],
            ],
            'strike' => [],
            'strong' => [],
            'ul' => [
                'class' => [],
            ],
        ];
        if ( is_array( $rawdata ) ) :
            return $rawdata = array_map( [ $this, 'allowed_html' ], $rawdata );
        else :
            return wp_kses( $rawdata, $allowed_tags );
        endif;
    }
}
