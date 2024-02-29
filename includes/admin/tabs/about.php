<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div id="about-settings" class="nav-content ">
	<table class="form-table">
		<tr>
			<td class="d-grid">
				<h3 class="mb-4"><?php esc_html_e( 'Informações sobre a licença:', 'woo-custom-installments' ); ?></h3>
				<span class="mb-2"><?php echo esc_html__( 'Status da licença:', 'woo-custom-installments' ) ?>
					<?php if ( $this->responseObj->is_valid ) : ?>
						<span class="badge bg-translucent-success rounded-pill"><?php _e(  'Válida', 'woo-custom-installments' );?></span>
					<?php else : ?>
						<span class="badge bg-translucent-danger rounded-pill"><?php _e(  'Inválida', 'woo-custom-installments' );?></span>
					<?php endif; ?>
				</span>
				<span class="mb-2"><?php echo esc_html__( 'Recursos:', 'woo-custom-installments' ) ?>
					<?php if ( $this->responseObj->is_valid ) : ?>
						<span class="badge bg-translucent-primary rounded-pill"><?php _e(  'Pro', 'woo-custom-installments' );?></span>
					<?php else : ?>
						<span class="badge bg-translucent-warning rounded-pill"><?php _e(  'Grátis', 'woo-custom-installments' );?></span>
					<?php endif; ?>
				</span>
				<a class="btn btn-primary my-4 pulsating-button purchase-button <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci#buy-pro" target="_blank">
					<svg class="me-2" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M7 17a5.007 5.007 0 0 0 4.898-4H14v2h2v-2h2v3h2v-3h1v-2h-9.102A5.007 5.007 0 0 0 7 7c-2.757 0-5 2.243-5 5s2.243 5 5 5zm0-8c1.654 0 3 1.346 3 3s-1.346 3-3 3-3-1.346-3-3 1.346-3 3-3z"></path></svg>
					<span><?php _e(  'Comprar licença', 'woo-custom-installments' );?></span>
				</a>
				<span class="mb-2 <?php if ( ! $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Tipo da licença:', 'woo-custom-installments' ) ?>
					<span><?php if (isset( $this->responseObj->license_title ) ) { echo $this->responseObj->license_title; } ?></span>
				</span>
				<span class="mb-2 <?php if ( ! $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Licença expira em:', 'flexify-checkout-for-woocommerce' ) ?>
					<span id="expire-date">
						<?php
							if ( isset( $this->responseObj->expire_date ) ) {
								$expiryText = $this->responseObj->expire_date;
						
								if ($expiryText === "No expiry") {
									echo esc_html__('Nunca expira', 'flexify-checkout-for-woocommerce');
								} else {
									echo date('d/m/Y', strtotime($expiryText));

									if ( strtotime( $expiryText ) < time() ) {
										update_option( 'woo_custom_installments_license_key', '' );
										update_option( 'woo_custom_installments_license_status', 'invalid' );
									}
								}
							}
						?>
					</span>
				</span>
				<span class="mb-2 <?php if ( ! $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Sua chave de licença:', 'woo-custom-installments' ) ?>
					<span>
						<?php 
						if (isset( $this->responseObj->license_key ) ) {
							echo esc_attr( substr( $this->responseObj->license_key, 0, 9). "XXXXXXXX-XXXXXXXX".substr( $this->responseObj->license_key,-9) );
						} else {
							echo __(  'Chave da licença não disponível', 'woo-custom-installments' );
						}
						?>
					</span>
				</span>
			</td>
		</tr>
		
		<tr class="<?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>">
		<td class="w-75">
			<span id="insert-license-info" class="bg-translucent-danger rounded-2 p-2 mb-4 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Informe sua licença abaixo para desbloquear todos os recursos.', 'woo-custom-installments' ) ?></span>
			<span class="form-label d-block mt-2"><?php echo esc_html__( 'Código da licença', 'woo-custom-installments' ) ?></span>
			<div class="input-group" style="width: 550px;">
				<input class="form-control" type="text" placeholder="XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX" id="woo_custom_installments_license_key" name="woo_custom_installments_license_key" size="50" value="<?php echo get_option( 'woo_custom_installments_license_key' ) ?>" />
				<button id="woo_custom_installments_active_license" name="woo_custom_installments_active_license" class="btn btn-primary button-loading <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>" type="submit">
					<span class="span-inside-button-loader"><?php esc_attr_e( 'Ativar licença', 'woo-custom-installments' ); ?></span>
				</button>
			</div>
		</td>
		</tr>
		<tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'd-none';} ?>">
			<td>
				<button id="woo_custom_installments_deactive_license" name="woo_custom_installments_deactive_license" class="btn btn-sm btn-primary button-loading" type="submit">
					<span class="span-inside-button-loader"><?php esc_attr_e( 'Desativar licença', 'woo-custom-installments' ); ?></span>
				</button>
			</td>
		</tr>

		<tr class="w-75 mt-5">
			<td>
				<h3 class="mt-0"><?php esc_html_e( 'Status do sistema:', 'woo-custom-installments' ); ?></h3>
				<h4><?php esc_html_e( 'WordPress', 'woo-custom-installments' ); ?></h4>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Versão do WordPress:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2"><?php echo esc_html( get_bloginfo( 'version' ) ); ?></span>
				</div>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'WordPress Multisite:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2"><?php echo is_multisite() ? esc_html__( 'Sim', 'woo-custom-installments' ) : esc_html__( 'Não', 'woo-custom-installments' ); ?></span>
				</div>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Modo de depuração do WordPress:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2"><?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? esc_html__( 'Ativo', 'woo-custom-installments' ) : esc_html__( 'Desativado', 'woo-custom-installments' ); ?></span>
				</div>

				<h4><?php esc_html_e( 'WooCommerce', 'woo-custom-installments' ); ?></h4>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Versão do WooCommerce:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<?php if( version_compare( WC_VERSION, '6.0', '<' ) ) : ?>
							<span class="badge bg-translucent-danger">
								<span>
									<?php echo esc_html( WC_VERSION ); ?>
								</span>
								<span>
									<?php esc_html_e( 'A versão mínima exigida do WooCommerce é 6.0', 'woo-custom-installments' ); ?>
								</span>
							</span>
						<?php else : ?>
							<span class="badge bg-translucent-success">
								<?php echo esc_html( WC_VERSION ); ?>
							</span>
						<?php endif; ?>
					</span>
				</div>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Versão do Parcelas Customizadas para WooCommerce:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<span class="badge bg-translucent-success">
							<?php echo esc_html( WOO_CUSTOM_INSTALLMENTS_VERSION ); ?>
						</span>
					</span>
				</div>

				<h4><?php esc_html_e( 'Servidor', 'woo-custom-installments' ); ?></h4>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Versão do PHP:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<?php if ( version_compare( PHP_VERSION, '7.2', '<' ) ) : ?>
							<span class="badge bg-translucent-danger">
								<span>
									<?php echo esc_html( PHP_VERSION ); ?>
								</span>
								<span>
									<?php esc_html_e( 'A versão mínima exigida do PHP é 7.2', 'woo-custom-installments' ); ?>
								</span>
							</span>
						<?php else : ?>
							<span class="badge bg-translucent-success">
								<?php echo esc_html( PHP_VERSION ); ?>
							</span>
						<?php endif; ?>
					</span>
				</div>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'DOMDocument:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<span>
							<?php if ( ! class_exists( 'DOMDocument' ) ) : ?>
								<span class="badge bg-translucent-danger">
									<?php esc_html_e( 'Não', 'woo-custom-installments' ); ?>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php esc_html_e( 'Sim', 'woo-custom-installments' ); ?>
								</span>
							<?php endif; ?>
						</span>
					</span>
				</div>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Extensão cURL:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<span>
							<?php if ( !extension_loaded('curl') ) : ?>
								<span class="badge bg-translucent-danger">
									<?php esc_html_e( 'Não', 'woo-custom-installments' ); ?>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php esc_html_e( 'Sim', 'woo-custom-installments' ); ?>
								</span>
								<span>
									<?php echo sprintf( __( 'Versão %s', 'woo-custom-installments' ), curl_version()['version'] ) ?>
								</span>
							<?php endif; ?>
						</span>
					</span>
				</div>
				<div class="d-flex mb-2">
					<span><?php esc_html_e( 'Extensão OpenSSL:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<span>
							<?php if ( !extension_loaded('openssl') ) : ?>
								<span class="badge bg-translucent-danger">
									<?php esc_html_e( 'Não', 'woo-custom-installments' ); ?>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php esc_html_e( 'Sim', 'woo-custom-installments' ); ?>
								</span>
								<span>
									<?php echo OPENSSL_VERSION_TEXT ?>
								</span>
							<?php endif; ?>
						</span>
					</span>
				</div>
				<?php if ( function_exists( 'ini_get' ) ) : ?>
					<div class="d-flex mb-2">
						<span>
							<?php $post_max_size = ini_get( 'post_max_size' ); ?>

							<?php esc_html_e( 'Tamanho máximo da postagem do PHP:', 'woo-custom-installments' ); ?>
						</span>
						<span class="ms-2">
							<?php if ( wp_convert_hr_to_bytes( $post_max_size ) < 64000000 ) : ?>
								<span>
									<span class="badge bg-translucent-danger">
										<?php echo esc_html( $post_max_size ); ?>
									</span>
									<span>
										<?php esc_html_e( 'Valor mínimo recomendado é 64M', 'woo-custom-installments' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php echo esc_html( $post_max_size ); ?>
								</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="d-flex mb-2">
						<span>
							<?php $max_execution_time = ini_get( 'max_execution_time' ); ?>
							<?php esc_html_e( 'Limite de tempo do PHP:', 'woo-custom-installments' ); ?>
						</span>
						<span class="ms-2">
							<?php if ( $max_execution_time < 180 ) : ?>
								<span>
									<span class="badge bg-translucent-danger">
										<?php echo esc_html( $max_execution_time ); ?>
									</span>
									<span>
										<?php esc_html_e( 'Valor mínimo recomendado é 180', 'woo-custom-installments' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php echo esc_html( $max_execution_time ); ?>
								</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="d-flex mb-2">
						<span>
							<?php $max_input_vars = ini_get( 'max_input_vars' ); ?>
							<?php esc_html_e( 'Variáveis máximas de entrada do PHP:', 'woo-custom-installments' ); ?>
						</span>
						<span class="ms-2">
							<?php if ( $max_input_vars < 10000 ) : ?>
								<span>
									<span class="badge bg-translucent-danger">
										<?php echo esc_html( $max_input_vars ); ?>
									</span>
									<span>
										<?php esc_html_e( 'Valor mínimo recomendado é 10000', 'woo-custom-installments' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php echo esc_html( $max_input_vars ); ?>
								</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="d-flex mb-2">
						<span>
							<?php $memory_limit = ini_get( 'memory_limit' ); ?>
							<?php esc_html_e( 'Limite de memória do PHP:', 'woo-custom-installments' ); ?>
						</span>
						<span class="ms-2">
							<?php if ( wp_convert_hr_to_bytes( $memory_limit ) < 128000000 ) : ?>
								<span>
									<span class="badge bg-translucent-danger">
										<?php echo esc_html( $memory_limit ); ?>
									</span>
									<span>
										<?php esc_html_e( 'Valor mínimo recomendado é 128M', 'woo-custom-installments' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php echo esc_html( $memory_limit ); ?>
								</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="d-flex mb-2">
						<span>
							<?php $upload_max_filesize = ini_get( 'upload_max_filesize' ); ?>
							<?php esc_html_e( 'Tamanho máximo de envio do PHP:', 'woo-custom-installments' ); ?>
						</span>
						<span class="ms-2">
							<?php if ( wp_convert_hr_to_bytes( $upload_max_filesize ) < 64000000 ) : ?>
								<span>
									<span class="badge bg-translucent-danger">
										<?php echo esc_html( $upload_max_filesize ); ?>
									</span>
									<span>
										<?php esc_html_e( 'Valor mínimo recomendado é 64M', 'woo-custom-installments' ); ?>
									</span>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php echo esc_html( $upload_max_filesize ); ?>
								</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="d-flex mb-2">
						<span><?php esc_html_e( 'Função PHP "file_get_content":', 'woo-custom-installments' ); ?></span>
						<span class="ms-2">
							<?php if ( ! ini_get( 'allow_url_fopen' ) ) : ?>
								<span class="badge bg-translucent-danger">
									<?php esc_html_e( 'Desligado', 'woo-custom-installments' ); ?>
								</span>
							<?php else : ?>
								<span class="badge bg-translucent-success">
									<?php esc_html_e( 'Ligado', 'woo-custom-installments' ); ?>
								</span>
							<?php endif; ?>
						</span>
					</div>
				<?php endif; ?>
			</td>
			<tr>
				<td class="d-flex">
					<a class="btn btn-sm btn-outline-danger" target="_blank" href="https://meumouse.com/reportar-problemas/"><?php esc_html_e( 'Reportar problemas', 'woo-custom-installments' ); ?></a>
					<button class="btn btn-sm btn-outline-primary ms-2 button-loading" name="woo_custom_installments_clear_activation_cache"><?php esc_html_e( 'Limpar cache de ativação', 'woo-custom-installments' ); ?></button>
				</td>
			</tr>
		</tr>
	</table>
</div>