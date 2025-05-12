<?php

namespace MeuMouse\Woo_Custom_Installments\Views;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Core\Calculate_Values;
use MeuMouse\Woo_Custom_Installments\Core\Calculate_Installments;
use MeuMouse\Woo_Custom_Installments\Core\Helpers;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Render components
 *
 * @since 5.2.5
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Components {

	/**
	 * Get assets directory
	 * 
	 * @since 5.4.0
	 */
	public $assets_url = WOO_CUSTOM_INSTALLMENTS_ASSETS;


	/**
	 * Build selector units
	 * 
	 * @since 5.2.5
	 * @return array
	 */
	public static function selector_units() {
		return apply_filters( 'woo_custom_installments_selector_units', array(
			'px' => esc_html__( 'Pixel', 'woo-custom-installments' ),
			'em' => esc_html__( 'EM', 'woo-custom-installments' ),
			'rem' => esc_html__( 'REM', 'woo-custom-installments' ),
			'%' => esc_html__( '%', 'woo-custom-installments' ),
		));
	}


	/**
	 * Build font weight units
	 *
	 * @since 5.2.5
	 * @return array
	 */
	public static function font_weight_units() {
		return apply_filters( 'woo_custom_installments_font_weight_units', array(
			'100' => esc_html__( '100', 'woo-custom-installments' ),
			'200' => esc_html__( '200', 'woo-custom-installments' ),
			'300' => esc_html__( '300', 'woo-custom-installments' ),
			'400' => esc_html__( '400', 'woo-custom-installments' ),
			'500' => esc_html__( '500', 'woo-custom-installments' ),
			'600' => esc_html__( '600', 'woo-custom-installments' ),
			'700' => esc_html__( '700', 'woo-custom-installments' ),
			'800' => esc_html__( '800', 'woo-custom-installments' ),
			'900' => esc_html__( '900', 'woo-custom-installments' ),
		));
	}


	/**
	 * Generate random ID for design control settings
	 * 
	 * @since 5.2.5
	 * @param string $prefix | Prefix ID
	 * @return string
	 */
	public static function generate_random_id( $prefix = '' ) {
		return $prefix . substr( uniqid(), 0, 4 );
	}


	/**
	 * Render margin editor control
	 * 
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function margin_control( $element, $device_type, $styles ) {
		if ( isset( $styles['margin'] ) ) :
			$positions = array(
				'top' => esc_html__( 'Superior', 'woo-custom-installments' ),
				'right' => esc_html__( 'Direita', 'woo-custom-installments' ),
				'bottom' => esc_html__( 'Inferior', 'woo-custom-installments' ),
				'left' => esc_html__( 'Esquerda', 'woo-custom-installments' ),
			); ?>

			<div class="margin-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Margem:', 'woo-custom-installments' ); ?></span>

				<ul class="design-control-group">
					<?php foreach ( $positions as $position => $label ) :
						if ( isset( $styles['margin'][ $position ] ) ) :
							$input_id = self::generate_random_id( "{$element}_margin_control_{$position}_{$device_type}_" ); ?>
							
							<li class="design-control-item <?php echo esc_attr( $position ); ?>">
								<input id="<?php echo esc_attr( $input_id ); ?>" 
										type="text" 
										name="elements_design[<?php echo esc_attr( $element ); ?>][styles][<?php echo esc_attr( $device_type ); ?>][margin][<?php echo esc_attr( $position ); ?>]" 
										class="design-control-input form-control set-margin"
										data-device="<?php echo esc_attr( $device_type ); ?>"
										data-box-position="<?php echo esc_attr( $position ); ?>"
										data-element="<?php echo esc_attr( $element ); ?>"
										data-property="margin"
										value="<?php echo esc_attr( $styles['margin'][ $position ] ); ?>">
								<label for="<?php echo esc_attr( $input_id ); ?>" class="design-control-label"><?php echo esc_html( $label ); ?></label>
							</li>
						<?php endif;
					endforeach;

					if ( isset( $styles['margin']['unit'] ) ) :
						$unit_id = self::generate_random_id( "{$element}_margin_control_unit_{$device_type}_" ); ?>
						
						<li class="design-control-item unit">
							<select id="<?php echo esc_attr( $unit_id ); ?>" 
									name="elements_design[<?php echo esc_attr( $element ); ?>][styles][<?php echo esc_attr( $device_type ); ?>][margin][unit]" 
									class="design-control-select form-select get-unit set-margin-unit" data-device="<?php echo esc_attr( $device_type ); ?>" data-element="<?php echo esc_attr( $element ); ?>">
								<?php foreach ( self::selector_units() as $unit => $unit_title ) : ?>
									<option value="<?php echo esc_attr( $unit ); ?>" 
											class="margin-unit <?php echo esc_attr( $unit ); ?>" 
											<?php selected( $styles['margin']['unit'], $unit ); ?>>
										<?php echo esc_html( $unit_title ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif;
	}


	/**
	 * Render padding editor control
	 * 
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function padding_control( $element, $device_type, $styles ) {
		if ( isset( $styles['padding'] ) ) :
			$positions = array(
				'top' => esc_html__( 'Superior', 'woo-custom-installments' ),
				'right' => esc_html__( 'Direita', 'woo-custom-installments' ),
				'bottom' => esc_html__( 'Inferior', 'woo-custom-installments' ),
				'left' => esc_html__( 'Esquerda', 'woo-custom-installments' ),
			); ?>

			<div class="padding-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Preenchimento:', 'woo-custom-installments' ); ?></span>

				<ul class="design-control-group">
					<?php foreach ( $positions as $position => $label ) :
						if ( isset( $styles['padding'][ $position ] ) ) :
							$input_id = self::generate_random_id( "{$element}_padding_control_{$position}_{$device_type}_" ); ?>
							
							<li class="design-control-item <?php echo esc_attr( $position ); ?>">
								<input id="<?php echo esc_attr( $input_id ); ?>" 
										type="text" 
										name="elements_design[<?php echo esc_attr( $element ); ?>][styles][<?php echo esc_attr( $device_type ); ?>][padding][<?php echo esc_attr( $position ); ?>]" 
										class="design-control-input form-control set-padding"
										data-device="<?php echo esc_attr( $device_type ); ?>"
										data-box-position="<?php echo esc_attr( $position ); ?>"
										data-element="<?php echo esc_attr( $element ); ?>"
										data-property="padding"
										value="<?php echo esc_attr( $styles['padding'][ $position ] ); ?>">
								<label for="<?php echo esc_attr( $input_id ); ?>" class="design-control-label"><?php echo esc_html( $label ); ?></label>
							</li>
						<?php endif;
					endforeach;

					if ( isset( $styles['padding']['unit'] ) ) :
						$unit_id = self::generate_random_id( "{$element}_padding_control_unit_{$device_type}_" ); ?>
						
						<li class="design-control-item unit">
							<select id="<?php echo esc_attr( $unit_id ); ?>" 
									name="elements_design[<?php echo esc_attr( $element ); ?>][styles][<?php echo esc_attr( $device_type ); ?>][padding][unit]" 
									class="design-control-select form-select set-padding-unit" data-device="<?php echo esc_attr( $device_type ); ?>" data-element="<?php echo esc_attr( $element ); ?>">
								<?php foreach ( self::selector_units() as $unit => $unit_title ) : ?>
									<option value="<?php echo esc_attr( $unit ); ?>" 
											class="padding-unit get-unit <?php echo esc_attr( $unit ); ?>" 
											<?php selected( $styles['padding']['unit'], $unit ); ?>>
										<?php echo esc_html( $unit_title ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif;
	}


	/**
	 * Render background color editor control
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function background_color_control( $element, $device_type, $styles ) {
		if ( isset( $styles ) ) :
			$background_color_id = self::generate_random_id( $element . '_background_color_control_' . $device_type . '_' ); ?>

			<div class="background-color-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Cor de fundo:', 'woo-custom-installments' ) ?></span>

				<div class="input-group color-container">
				<input type="text" id="<?php esc_attr_e( $background_color_id ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][styles][<?php esc_attr_e( $device_type ) ?>][background_color]" class="form-control input-color set-background-color" data-device="<?php esc_attr_e( $device_type ) ?>" data-property="background-color" data-element="<?php echo esc_attr( $element ); ?>" data-format="rgb" data-opacity="1" data-position="bottom" value="<?php esc_attr_e( $styles['background_color'] ) ?>" size="25">
				
				<button class="btn btn-outline-secondary btn-icon reset-color wci-tooltip" data-color="<?php esc_attr_e( $styles['default_background_color'] ) ?>" data-text="<?php esc_html_e( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
					<svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
				</button>
				</div>
			</div>
		<?php endif;
	}


	/**
	 * Render font color editor control
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function font_color_control( $element, $device_type, $styles ) {
		if ( isset( $styles ) ) :
			$font_color_id = self::generate_random_id( $element . '_font_color_control_' . $device_type . '_' ); ?>

			<div class="font-color-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Cor do texto:', 'woo-custom-installments' ) ?></span>

				<div class="input-group color-container">
				<input type="text" id="<?php esc_attr_e( $font_color_id ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][styles][<?php esc_attr_e( $device_type ) ?>][font_color]" class="design-control-input form-control input-color set-font-color" data-element="<?php echo esc_attr( $element ); ?>" data-property="color" data-device="<?php esc_attr_e( $device_type ) ?>" data-format="rgb" data-opacity="1" data-position="bottom" value="<?php esc_attr_e( $styles['font_color'] ) ?>">
				
				<button class="btn btn-outline-secondary btn-icon reset-color wci-tooltip" data-color="<?php esc_attr_e( $styles['default_font_color'] ) ?>" data-text="<?php esc_html_e( 'Redefinir para cor padrão', 'woo-custom-installments' ) ?>">
					<svg class="icon-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
				</button>
				</div>
			</div>
		<?php endif;
	}


	/**
	 * Render font size editor control
	 *
	 * @since 5.2.5
	 * @version 5.2.6
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function font_size_control( $element, $device_type, $styles ) {
		if ( isset( $styles ) ) : ?>
			<div class="font-color-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Tamanho do texto:', 'woo-custom-installments' ) ?></span>

				<div class="input-group color-container design-control-group">
				<input type="text" id="<?php esc_attr_e( self::generate_random_id( $element . '_font_size_control_' . $device_type . '_' ) ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][styles][<?php esc_attr_e( $device_type ) ?>][font_size]" class="design-control-input form-control set-font-size input-control-wd-10" value="<?php esc_attr_e( $styles['font_size'] ) ?>" data-element="<?php echo esc_attr( $element ); ?>" data-property="font-size" data-device="<?php esc_attr_e( $device_type ) ?>">
				
				<select id="<?php esc_attr_e( self::generate_random_id( $element . '_font_size_unit_control_' . $device_type . '_' ) ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][styles][<?php esc_attr_e( $device_type ) ?>][font_unit]" class="design-control-select form-select input-control-wd-10 get-unit set-font-size-unit" data-element="<?php echo esc_attr( $element ); ?>" data-device="<?php esc_attr_e( $device_type ) ?>">
					<?php foreach ( self::selector_units() as $unit => $unit_title ) : ?>
						<option value="<?php esc_attr_e( $unit ) ?>" class="font-size-unit <?php esc_attr_e( $unit ) ?>" <?php echo ( $styles['font_unit'] === $unit ) ? 'selected=selected' : ''; ?>><?php echo $unit_title ?></option>
					<?php endforeach; ?>
				</select>
				</div>
			</div>
		<?php endif;
	}


	/**
	 * Render font weight editor control
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function font_weight_control( $element, $device_type, $styles ) {
		if ( isset( $styles ) ) : ?>
			<div class="font-color-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Espessura do texto:', 'woo-custom-installments' ) ?></span>

				<select id="<?php esc_attr_e( self::generate_random_id( $element . '_font_weight_control_' . $device_type . '_' ) ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][styles][<?php esc_attr_e( $device_type ) ?>][font_weight]" class="design-control-select form-select get-unit set-font-weight" data-device="<?php esc_attr_e( $device_type ) ?>" data-property="font-weight" data-element="<?php echo esc_attr( $element ); ?>">
				<?php foreach ( self::font_weight_units() as $unit => $unit_title ) : ?>
					<option value="<?php esc_attr_e( $unit ) ?>" class="font-weight-unit <?php esc_attr_e( $unit ) ?>" <?php echo ( (int) $styles['font_weight'] === (int) $unit ) ? 'selected=selected' : ''; ?>><?php echo $unit_title ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		<?php endif;
	}


	/**
	 * Render border radius editor control
	 * 
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $styles | Styles array
	 * @return void
	 */
	public static function border_radius_control( $element, $device_type, $styles ) {
		if ( isset( $styles['border_radius'] ) ) :
			$positions = array(
				'top' => esc_html__( 'Superior', 'woo-custom-installments' ),
				'right' => esc_html__( 'Direita', 'woo-custom-installments' ),
				'bottom' => esc_html__( 'Inferior', 'woo-custom-installments' ),
				'left' => esc_html__( 'Esquerda', 'woo-custom-installments' ),
			); ?>

			<div class="border-radius-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Raio da borda:', 'woo-custom-installments' ); ?></span>
	
				<ul class="design-control-group">
				<?php foreach ( $positions as $position => $label ) :
						if ( isset( $styles['border_radius'][ $position ] ) ) :
							$input_id = self::generate_random_id( "{$element}_border_radius_control_{$position}_{$device_type}_" ); ?>
							
							<li class="design-control-item <?php echo esc_attr( $position ); ?>">
							<input id="<?php echo esc_attr( $input_id ); ?>" 
										type="text" 
										name="elements_design[<?php echo esc_attr( $element ); ?>][styles][<?php echo esc_attr( $device_type ); ?>][border_radius][<?php echo esc_attr( $position ); ?>]" 
										class="design-control-input form-control set-border-radius" 
										data-device="<?php echo esc_attr( $device_type ); ?>" 
										data-box-position="<?php echo esc_attr( $position ); ?>"
										data-element="<?php echo esc_attr( $element ); ?>"
										data-property="border-radius"
										value="<?php echo esc_attr( $styles['border_radius'][ $position ] ); ?>">
							<label for="<?php echo esc_attr( $input_id ); ?>" class="design-control-label"><?php echo esc_html( $label ); ?></label>
							</li>
						<?php endif;
				endforeach;

				if ( isset( $styles['border_radius']['unit'] ) ) :
						$unit_id = self::generate_random_id( "{$element}_border_radius_control_unit_{$device_type}_" ); ?>
						
						<li class="design-control-item unit">
							<select id="<?php echo esc_attr( $unit_id ); ?>" 
									name="elements_design[<?php echo esc_attr( $element ); ?>][styles][<?php echo esc_attr( $device_type ); ?>][border_radius][unit]" 
									class="design-control-select form-select get-unit set-border-radius-unit" data-element="<?php echo esc_attr( $element ); ?>" data-device="<?php echo esc_attr( $device_type ); ?>">
							<?php foreach ( self::selector_units() as $unit => $unit_title ) : ?>
									<option value="<?php echo esc_attr( $unit ); ?>" 
										class="border-radius-unit <?php echo esc_attr( $unit ); ?>" 
										<?php selected( $styles['border_radius']['unit'], $unit ); ?>>
										<?php echo esc_html( $unit_title ); ?>
									</option>
							<?php endforeach; ?>
							</select>
						</li>
				<?php endif; ?>
				</ul>
			</div>
		<?php endif;
	}


	/**
	 * Render icon editor control
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param array $settings | Settings array
	 * @return void
	 */
	public static function icon_control( $element, $settings ) {
		if ( isset( $settings ) ) : ?>
			<div class="icon-editor-control mb-5">
				<span class="fs-base fw-semibold mb-2 d-block text-start ps-2"><?php esc_html_e( 'Ícone:', 'woo-custom-installments' ) ?></span>

				<div class="input-group icon-class-container d-none">
				<span class="input-group-text">
					<i class="icon-preview fs-4 <?php echo esc_attr( $settings['icon']['class'] ) ?>"></i>
				</span>

				<input type="text" id="<?php esc_attr_e( self::generate_random_id( $element . '_icon_class_control_' ) ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][icon][class]" class="form-control set-icon-class" value="<?php esc_attr_e( $settings['icon']['class'] ) ?>"/>
				</div>

				<div class="input-group icon-image-container d-none">
				<span class="input-group-text p-0">
					<img class="icon-preview" src="<?php echo esc_url( $settings['icon']['image'] ) ?>"/>
				</span>
				
				<input type="text" id="<?php esc_attr_e( self::generate_random_id( $element . '_icon_image_control_' ) ) ?>" name="elements_design[<?php esc_attr_e( $element ) ?>][icon][image]" class="form-control set-icon-image" value="<?php esc_attr_e( $settings['icon']['image'] ) ?>"/>

				<button class="btn btn-outline-secondary get-icon-image"><?php esc_html_e( 'Procurar', 'woo-custom-installments' ) ?></button>
				</div>

				<a class="fancy-link mt-3 d-flex w-fit fs-sm ms-2" href="https://fontawesome.com/search?m=free&o=r" target="_blank"><?php esc_html_e( 'Acessar Font Awesome', 'woo-custom-installments' ) ?></a>
			</div>
		<?php endif;
	}


	/**
	 * Display element preview
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param string $device_type | Device type (mobile or desktop)
	 * @param array $settings | Settings array
	 * @return void
	 */
	public static function element_preview( $element, $device_type, $settings ) {
		if ( isset( $settings ) ) : ?>
			<div class="d-flex flex-column align-items-center justify-content-center mb-5">
				<span class="mb-2 d-block fw-semibold"><?php esc_html_e( 'Pré-visualizar:', 'woo-custom-installments' ) ?></span>
				
				<div class="preview <?php esc_attr_e( $element ) ?> float-none" data-device="<?php esc_attr_e( $device_type ) ?>">
				<?php if ( isset( $settings['icon'] ) ) {
					if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) : ?>
						<i class="me-1 <?php esc_attr_e( $settings['icon']['class'] ) ?>"></i>
					<?php else : ?>
						<img class="me-1" src="<?php esc_attr_e( $settings['icon']['image'] ) ?>"/>
					<?php endif;
				}

				echo $settings['preview'] ?>
				</div>
			</div>
		<?php endif;
	}


	/**
	 * Devices wrapper selector and controllers
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param array $settings | Settings array
	 * @return void
	 */
	public static function devices_wrapper( $element, $settings ) {
		if ( isset( $settings ) ) :
			$desktop_controller_id = self::generate_random_id( $element . '_desktop_design_controller_' );
			$mobile_controller_id = self::generate_random_id( $element . '_mobile_design_controller_' ); ?>

			<div class="design-device-wrapper">
				<?php if ( isset( $settings['styles']['desktop'] ) ) : ?>
				<div id="<?php esc_attr_e( $desktop_controller_id ) ?>" class="nav-tab nav-tab-active" data-device="desktop">
					<svg class="woo-custom-installments-tab-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 3H4c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2h7v2H8v2h8v-2h-3v-2h7c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 14V5h16l.002 9H4z"></path></svg>
					<?php echo esc_html__( 'Computador', 'woo-custom-installments' ) ?></a>
				</div>
				<?php endif; ?>

				<?php if ( isset( $settings['styles']['mobile'] ) ) : ?>
				<div id="<?php esc_attr_e( $mobile_controller_id ) ?>" class="nav-tab" data-device="mobile">
					<svg class="woo-custom-installments-tab-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16.75 2h-10c-1.103 0-2 .897-2 2v16c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zm-10 18V4h10l.002 16H6.75z"></path><circle cx="11.75" cy="18" r="1"></circle></svg>
					<?php echo esc_html__( 'Celular', 'woo-custom-installments' ) ?></a>
				</div>
				<?php endif; ?>
			</div>

			<div class="design-device-content">
				<div class="nav-content show" data-device="desktop" data-target="<?php esc_attr_e( $desktop_controller_id ) ?>">
				<?php $desktop_styles = $settings['styles']['desktop'];

				self::element_preview( $element, 'desktop', $settings );
				self::get_element_styles( $element, 'desktop', $desktop_styles );
				self::font_size_control( $element, 'desktop', $desktop_styles );
				self::font_weight_control( $element, 'desktop', $desktop_styles );
				self::font_color_control( $element, 'desktop', $desktop_styles );
				self::background_color_control( $element, 'desktop', $desktop_styles );
				self::margin_control( $element, 'desktop', $desktop_styles );
				self::padding_control( $element, 'desktop', $desktop_styles );
				self::border_radius_control( $element, 'desktop', $desktop_styles ); ?>
				</div>

				<div class="nav-content" data-device="mobile" data-target="<?php esc_attr_e( $mobile_controller_id ) ?>">
				<?php $mobile_styles = $settings['styles']['mobile'];

				self::element_preview( $element, 'mobile', $settings );
				self::get_element_styles( $element, 'mobile', $mobile_styles );
				self::font_size_control( $element, 'mobile', $mobile_styles );
				self::font_weight_control( $element, 'mobile', $mobile_styles );
				self::font_color_control( $element, 'mobile', $mobile_styles );
				self::background_color_control( $element, 'mobile', $mobile_styles );
				self::margin_control( $element, 'mobile', $mobile_styles );
				self::padding_control( $element, 'mobile', $mobile_styles );
				self::border_radius_control( $element, 'mobile', $mobile_styles ); ?>
				</div>
			</div>

			<div class="container-separator"></div>

			<div class="common-device-controls">
				<?php self::icon_control( $element, $settings ); ?>
			</div>
		<?php endif;
	}


	/**
	 * Load element styles for preview
	 *
	 * @since 5.2.5
	 * @param string $element | Element name
	 * @param array $settings | Settings array
	 * @return void
	 */
	public static function get_element_styles( $element, $device_type, $styles ) {
		$css = '';

		if ( isset( $styles ) ) {
			$properties = array(
				'font-size' => isset( $styles['font_size'] ) ? $styles['font_size'] . ( $styles['font_unit'] ?? 'px' ) : '',
				'font-weight' => $styles['font_weight'] ?? '',
				'color' => $styles['font_color'] ?? '',
				'background-color' => $styles['background_color'] ?? '',
				'margin' => isset( $styles['margin'] ) ? self::format_box_property( $styles['margin'], $styles['margin']['unit'] ?? 'px' ) : '',
				'padding' => isset( $styles['padding'] ) ? self::format_box_property( $styles['padding'], $styles['padding']['unit'] ?? 'px' ) : '',
				'border-radius' => isset( $styles['border_radius'] ) ? self::format_box_property( $styles['border_radius'], $styles['border_radius']['unit'] ?? 'px' ) : '',
			);

			foreach ( $properties as $key => $value ) {
				if ( ! empty( $value ) ) {
				$css .= sprintf( '%s: %s;', $key, $value );
				}
			}

			$target = '.preview.' . $element. '[data-device="'. $device_type .'"]';

			printf( '<style>%s{%s}</style>', $target, $css );
		}
	}


	/**
	 * Format a box property (e.g., margin, padding, border-radius) for shorthand.
	 *
	 * @since 5.2.5
	 * @param array $box The box property array with top, right, bottom, left values.
	 * @param string $unit The unit to append to each value.
	 * @return string The formatted shorthand property.
	 */
	public static function format_box_property( $box, $unit ) {
		// Ensure $box is a valid array to avoid index errors
		$box = is_array( $box ) ? $box : array();
	
		// Set default values ​​to '0' if missing or empty
		$top = ! empty( $box['top'] ) ? $box['top'] : '0';
		$right = ! empty( $box['right'] ) ? $box['right'] : '0';
		$bottom = ! empty( $box['bottom'] ) ? $box['bottom'] : '0';
		$left = ! empty( $box['left'] ) ? $box['left'] : '0';
	
		// Return formatted values
		return sprintf( '%s%s %s%s %s%s %s%s', $top, $unit, $right, $unit, $bottom, $unit, $left, $unit );
	}


	/**
	 * Render Pix flag
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string
	 */
	public static function render_pix_flag( $product ) {
		$price = wc_get_price_to_display( $product );
		$economy_pix_active = Admin_Options::get_setting('enable_economy_pix_badge') === 'yes';
		$pix_flag = '';
		
		if ( Admin_Options::get_setting('enable_pix_method_payment_form') === 'yes' ) {
			$pix_flag .= '<div class="woo-custom-installments-pix-section">';
				$pix_flag .= '<h4 class="pix-method-title">'. Admin_Options::get_setting('text_pix_container') .'</h4>';
				$pix_flag .= '<div class="pix-method-container">';
				$pix_flag .= '<span class="pix-method-name">'. sprintf( esc_html__( 'Pix: %s', 'woo-custom-installments' ), wc_price( Calculate_Values::get_discounted_price( $product, 'main' ) ) ) .'</span>';
				
				if ( Admin_Options::get_setting('enable_instant_approval_badge') === 'yes' ) {
					$pix_flag .= '<span class="instant-approval-badge">'. esc_html__( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
				}

				$pix_flag .= '</div>';

				$get_pix_economy_value = Calculate_Values::get_pix_economy( $product );

				if ( $economy_pix_active && $get_pix_economy_value > 0 ) {
					$pix_flag .= '<div class="container-badge-icon pix-flag pix-info instant-approval-badge">';
				} else {
					$pix_flag .= '<div class="container-badge-icon pix-flag pix-info">';
				}

				if ( $get_pix_economy_value ) {
					$pix_flag .= '<i class="pix-icon-badge fa-brands fa-pix"></i>';
					
					if ( $economy_pix_active ) {
						$pix_flag .= '<div class="economy-pix-info">';
						$pix_flag .= $this->economy_pix_badge( $product );
						$pix_flag .= '</div>';
					}
				}

				$pix_flag .= '</div>';
			$pix_flag .= '</div>';
		}

		return $pix_flag;
	}


	/**
	 * Ticket flag
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string
	 */
	public static function render_ticket_flag( $product ) {
		$ticket_flag = '';
		
		if ( Admin_Options::get_setting('enable_ticket_method_payment_form') === 'yes' ) {
			$ticket_flag .= '<div class="woo-custom-installments-ticket-section">';
				$ticket_flag .= '<h4 class="ticket-method-title">'. esc_html__( 'Cobranças:', 'woo-custom-installments' ) .'</h4>';

				$ticket_flag .= '<span class="ticket-method-name">'. sprintf( __( '%s %s' ), Admin_Options::get_setting('text_ticket_container'), wc_price( Calculate_Values::get_discounted_price( $product, 'ticket' ) ) ) .'</span>';

				$ticket_flag .= '<div class="ticket-method-container">';
				$ticket_flag .= '<span class="ticket-instructions">'. Admin_Options::get_setting('text_instructions_ticket_container') .'</span>';
				$ticket_flag .= '</div>';

				$ticket_flag .= '<div class="container-badge-icon ticket-flag">';
				$ticket_flag .= '<img class="size-badge-icon" src="'. $this->assets_url . 'front/img/boleto-badge.svg"/>';
				$ticket_flag .= '</div>';

			$ticket_flag .= '</div>';
		}

		return $ticket_flag;
	}


	/**
	 * Get card flags
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param string $card_type | credit-card or debit-card
	 * @param string $type | credit or debit
	 * @return string
	 */
	public function get_card_flags( $card_type, $type ) {
		$default_flags = array(
			'mastercard' => $this->assets_url . 'front/img/mastercard-badge.svg',
			'visa' => $this->assets_url . 'front/img/visa-badge.svg',
			'elo' => $this->assets_url . 'front/img/elo-badge.svg',
			'hipercard' => $this->assets_url . 'front/img/hipercard-badge.svg',
			'diners_club' => $this->assets_url . 'front/img/diners-club-badge.svg',
			'discover' => $this->assets_url . 'front/img/discover-badge.svg',
			'american_express' => $this->assets_url . 'front/img/american-express-badge.svg',
			'paypal' => $this->assets_url . 'front/img/paypal-badge.svg',
			'stripe' => $this->assets_url . 'front/img/stripe-badge.svg',
			'mercado_pago' => $this->assets_url . 'front/img/mercado-pago-badge.svg',
			'pagseguro' => $this->assets_url . 'front/img/pagseguro-badge.svg',
			'pagarme' => $this->assets_url . 'front/img/pagarme-badge.svg',
			'cielo' => $this->assets_url . 'front/img/cielo-badge.svg',
		);

		/**
		 * Filter the card flags
		 * 
		 * @since 2.0.0
		 * @version 5.4.0
		 * @param array $default_flags | Default flags
		 * @param string $card_type | credit-card or debit-card
		 * @param string $type | credit or debit
		 */
		$flags = apply_filters( 'Woo_Custom_Installments/Elements/Card_Flags', $default_flags, $card_type, $type );

		$card_flags = '';
		$options = get_option('woo-custom-installments-setting');

		foreach( $flags as $key => $flag_url ) {
			if ( isset( $options['enable_' . $key . '_flag_' . $type] ) && $options['enable_' . $key . '_flag_' . $type] === 'yes' ) {
				$card_flags .= '<div class="container-badge-icon ' . esc_attr( $card_type ) . ' ' . esc_attr( $key ) . '-flag">';
				$card_flags .= '<img class="size-badge-icon" src="' . esc_url( $flag_url ) . '"/>';
				$card_flags .= '</div>';
			}
		}

		return $card_flags;
	}


	/**
	 * Credit card flags
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @return string
	 */
	public static function render_credit_card_flags() {
		$credit_card_flag = '';

		if ( Admin_Options::get_setting('enable_credit_card_method_payment_form') === 'yes' ) {
			$credit_card_flag .= '<div class="woo-custom-installments-credit-card-section">';
				$credit_card_flag .= '<h4 class="credit-card-method-title">' . Admin_Options::get_setting('text_credit_card_container') . '</h4>';
				
				if ( Admin_Options::get_setting('enable_instant_approval_badge') === 'yes' ) {
					$credit_card_flag .= '<div class="credit-card-method-container">';
						$credit_card_flag .= '<span class="instant-approval-badge">' . esc_html__('Aprovação imediata', 'woo-custom-installments') . '</span>';
					$credit_card_flag .= '</div>';
				}

				$credit_card_flag .= '<div class="credit-card-container-badges">';
					$credit_card_flag .= $this->get_card_flags( 'credit-card', 'credit' );
				$credit_card_flag .= '</div>';
			$credit_card_flag .= '</div>';
		}

		return $credit_card_flag;
	}


	/**
	 * Debit card flags
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @return string
	 */
	public static function render_debit_card_flags() {
		$html = '';

		if ( Admin_Options::get_setting('enable_debit_card_method_payment_form') === 'yes') {
			$html .= '<div class="woo-custom-installments-debit-card-section">';
			$html .= '<h4 class="debit-card-method-title">' . Admin_Options::get_setting('text_debit_card_container') . '</h4>';
			
			if ( Admin_Options::get_setting('enable_instant_approval_badge') === 'yes' ) {
				$html .= '<div class="debit-card-method-container">';
				$html .= '<span class="instant-approval-badge">' . esc_html__('Aprovação imediata', 'woo-custom-installments') . '</span>';
				$html .= '</div>';
			}

			$html .= '<div class="debit-card-container-badges">';
				$html .= $this->get_card_flags( 'debit-card', 'debit' );
			$debit_carhtmld_flag .= '</div>';

			$html .= '</div>';
		}

		return $html;
	}


	/**
	 * Generate table of installments
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string
	 */
	public static function render_installments_table( $product ) {
		if ( ! $product ) {
			return;
		}

		if ( $product && $product->is_type( 'variable', 'variation' ) && ! Helpers::variations_has_same_price( $product ) ) {
			$args = array();
			$args['price'] = $product->get_variation_price('max');
			$price = wc_get_price_to_display( $product, $args );
		} else {
			$price = wc_get_price_to_display( $product );
		}
		
		$all_installments = Calculate_Installments::set_values( 'all', $price, $product, false );

		if ( ! $all_installments ) {
			return;
		}

		// Installments table
		$table = '<h4 class="installments-title">'. Admin_Options::get_setting('text_table_installments') .'</h4>';
		$table .= '<div id="table-installments">';
		$table .= '<table class="table table-hover woo-custom-installments-table">';
			$table .= '<tbody data-default-text="'. Admin_Options::get_setting('text_display_installments_payment_forms') .'">';
			foreach ( $all_installments as $installment ) {
				$find = array_keys( Helpers::strings_to_replace( $installment ) );
				$replace = array_values( Helpers::strings_to_replace( $installment ) );
				$final_text = str_replace( $find, $replace, Admin_Options::get_setting('text_display_installments_payment_forms') );

				$table .= '<tr class="'. $installment['class'] .'">';
				$table .= '<th class="final-text">'. $final_text .'</th>';
				$table .= '<th class="final-price">'. wc_price( $installment['final_price'] ) .'</th>';
				$table .= '</tr>';
			}
			$table .= '</tbody>';
		$table .= '</table>';
		$table .= '</div>';

		return $table;
	}


	/**
	 * Display best installments
	 * 
	 * @since 2.1.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string
	 */
	public function display_best_installments( $product ) {
		if ( $product === false ) {
			global $product;
		}

		// check if option __disable_installments in product is true
		$disable_installments_in_product = get_post_meta( $product->get_id(), '__disable_installments', true ) === 'yes';
	
		// check if product is variation e get the id of parent product
		if ( $product && $product->is_type( 'variation', 'variable' ) ) {
			$parent_id = $product->get_parent_id();
			$disable_installments_in_parent = get_post_meta( $parent_id, '__disable_installments', true ) === 'yes';
		} else {
			$disable_installments_in_parent = false;
		}
	
		// check if '__disable_installments' is true for the simple or variation products
		if ( $disable_installments_in_product || $disable_installments_in_parent || ! $product->is_purchasable() ) {
			return;
		}

		// Get the correct price based on the product type
		if ( $product->is_type('variation') ) {
			$price = $product->get_sale_price() ?: $product->get_regular_price();
		} elseif ( $product->is_type( 'variable' ) ) {
			// For variable products, get the lowest price with discount
			$price = $product->get_variation_sale_price( 'min', true ) ?: $product->get_variation_regular_price( 'min', true );
		} else {
			$price = $product->get_sale_price() ?: $product->get_regular_price();
		}

		$installments = Calculate_Installments::set_values( array(), $price, $product, false );
		$best_installments = '';
	
		if ( Admin_Options::get_setting('get_type_best_installments') === 'best_installment_without_fee' ) {
			$best_installments = Calculate_Installments::best_without_interest( $installments, $product );
		} elseif ( Admin_Options::get_setting('get_type_best_installments') === 'best_installment_with_fee' ) {
			$best_installments = Calculate_Installments::best_with_interest( $installments, $product );
		} elseif ( Admin_Options::get_setting('get_type_best_installments') === 'both' ) {
			$best_installments = Calculate_Installments::best_without_interest( $installments, $product );
			$best_installments .= Calculate_Installments::best_with_interest( $installments, $product );
		}
	
		$html = ' <span class="woo-custom-installments-card-container">';
		$html .= $best_installments;
		$html .= ' </span>';

		// Check display conditions
		if ( Admin_Options::get_setting('hook_display_best_installments') === 'display_loop_and_single_product'
			|| ( Admin_Options::get_setting('hook_display_best_installments') === 'only_single_product' && is_product() )
			|| ( Admin_Options::get_setting('hook_display_best_installments') === 'only_loop_products' && is_archive() ) ) {
			return $html;
		}
	}


	/**
	 * Discount product main price
	 * 
	 * @since 3.6.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string $html
	 */
	public function discount_main_price_single( $product ) {
		if ( $product === false ) {
			global $product;
		}

		if ( Admin_Options::get_setting('display_discount_price_hook') === 'hide' || Admin_Options::get_setting('enable_all_discount_options') !== 'yes' ) {
			return;
		}

		$html = '<span class="woo-custom-installments-offer">';

		$pix_icon_base = Admin_Options::get_setting('elements_design')['discount_pix']['icon'];

		if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
			if ( isset( $pix_icon_base['class'] ) ) {
				$html .= sprintf( __( '<i class="wci-icon-main-price icon-class %s"></i>' ), esc_attr( $pix_icon_base['class'] ) );
			}
		} else {
			if ( isset( $pix_icon_base['image'] ) ) {
				$html .= sprintf( __( '<img class="wci-icon-main-price icon-image" src="%s"/>' ), esc_url( $pix_icon_base['image'] ) );
			}
		}

		// check if exists text before price for display
		if ( ! empty( Admin_Options::get_setting('text_before_price') ) ) {
			$html .= '<span class="discount-before-price">'. Admin_Options::get_setting('text_before_price') .'</span>';
		}

		$html .= '<span class="discounted-price">'. wc_price( Calculate_Values::get_discounted_price( $product, 'main' ) ) .'</span>';

		// check if exists text after price for display
		if ( ! empty( Admin_Options::get_setting('text_after_price') ) ) {
			$html .= '<span class="discount-after-price">'. Admin_Options::get_setting('text_after_price') .'</span>';
		}

		$html .= '</span>';

		// Check display conditions
		if ( Admin_Options::get_setting('display_discount_price_hook') === 'display_loop_and_single_product'
			|| ( Admin_Options::get_setting('display_discount_price_hook') === 'only_single_product' && is_product() )
			|| ( Admin_Options::get_setting('display_discount_price_hook') === 'only_loop_products' && is_archive() ) ) {
			if ( $product->get_price() > 0 ) {
				return $html;
			}
		}
	}


	/**
	 * Create a ticket discount badge
	 * 
	 * @since 2.8.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string $html
	 */
	public function discount_ticket_badge( $product ) {
		if ( $product === false ) {
			global $product;
		}

		$html = '<span class="woo-custom-installments-ticket-discount">';

		$ticket_icon_base = Admin_Options::get_setting('elements_design')['discount_slip_bank']['icon'];

		if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
			if ( isset( $ticket_icon_base['class'] ) ) {
				$html .= sprintf( __( '<i class="wci-icon-ticket-discount icon-class %s"></i>' ), esc_attr( $ticket_icon_base['class'] ) );
			}
		} else {
			if ( isset( $ticket_icon_base['image'] ) ) {
				$html .= sprintf( __( '<img class="wci-icon-ticket-discount icon-image" src="%s"/>' ), esc_url( $ticket_icon_base['image'] ) );
			}
		}

		// check if exists text before price for display
		if ( ! empty( Admin_Options::get_setting('text_before_discount_ticket') ) ) {
			$html .= '<span class="discount-before-discount-ticket">'. Admin_Options::get_setting('text_before_discount_ticket') .'</span>';
		}

		$html .= '<span class="discounted-price">'. wc_price( Calculate_Values::get_discounted_price( $product, 'ticket' ) ) .'</span>';

		// check if exists text after price for display
		if ( ! empty( Admin_Options::get_setting('text_after_discount_ticket') ) ) {
			$html .= '<span class="discount-after-discount-ticket">'. Admin_Options::get_setting('text_after_discount_ticket') .'</span>';
		}

		$html .= '</span>';

		if ( Admin_Options::get_setting('enable_ticket_method_payment_form') === 'yes' && Admin_Options::get_setting('enable_ticket_discount_main_price') === 'yes' ) {
			// Check display conditions
			if ( Admin_Options::get_setting('display_discount_ticket_hook') === 'global'
			|| ( Admin_Options::get_setting('display_discount_ticket_hook') === 'only_single_product' && is_product() )
			|| ( Admin_Options::get_setting('display_discount_ticket_hook') === 'only_loop_products' && is_archive() ) ) {
				return $html;
			}
		}
	}


	/**
	 * Create a economy Pix badge
	 * 
	 * @since 3.6.0
	 * @version 5.4.0
	 * @param WC_Product $product | Product object
	 * @return string
	 */
	public function economy_pix_badge( $product ) {
		if ( $product === false || ! isset( $product ) ) {
			global $product;
		}
		
		if ( Admin_Options::get_setting('enable_economy_pix_badge') !== 'yes' || Admin_Options::get_setting('enable_all_discount_options') !== 'yes' ) {
			return;
		}

		$economy_value = Calculate_Values::get_pix_economy( $product );

		if ( $economy_value <= 0 ) {
			return '';
		}

		// Check if exists text before price for display
		$text_economy_pix_badge = Admin_Options::get_setting('text_economy_pix_badge');

		if ( ! empty( $text_economy_pix_badge ) ) {
			// Checks if string contains %s
			if ( strpos( $text_economy_pix_badge, '%s' ) !== false ) {
				$formatted_text = sprintf( $text_economy_pix_badge, wc_price( $economy_value ) );
			} else {
				// If %s is missing, use the original text
				$formatted_text = $text_economy_pix_badge;
			}

			$html = '<span class="woo-custom-installments-economy-pix-badge">';

			$economy_pix_icon_base = Admin_Options::get_setting('elements_design')['pix_economy']['icon'];

			if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
				if ( isset( $economy_pix_icon_base['class'] ) ) {
					$html .= sprintf( __( '<i class="wci-icon-economy-pix icon-class %s"></i>' ), esc_attr( $economy_pix_icon_base['class'] ) );
				}
			} else {
				if ( isset( $economy_pix_icon_base['image'] ) ) {
					$html .= sprintf( __( '<img class="wci-icon-economy-pix icon-image" src="%s"/>' ), esc_url( $economy_pix_icon_base['image'] ) );
				}
			}
			
			$html .= '<span class="discount-before-economy-pix">' . $formatted_text . '</span>';
			$html .= '</span>';

			// Check display conditions
			if ( Admin_Options::get_setting('display_economy_pix_hook') === 'global'
				|| Admin_Options::get_setting('display_economy_pix_hook') === 'only_single_product' && is_product()
				|| Admin_Options::get_setting('display_economy_pix_hook') === 'only_loop_products' && is_archive() ) {
				return $html;
			}
		}
	}
}