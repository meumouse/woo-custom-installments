<?php

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="about" class="nav-content">
	<table class="form-table">
		<tr>
			<th>
				<?php esc_html_e( 'Ativar atualizações automáticas', 'woo-custom-installments' ); ?>
				<span class="woo-custom-installments-description"><?php esc_html_e( 'Ative essa opção para que o plugin Parcelas Customizadas seja atualizado automaticamente sempre que possível.', 'woo-custom-installments' ); ?></span>
			</th>
			<td>
				<div class="form-check form-switch">
					<input type="checkbox" class="toggle-switch" id="enable_auto_updates" name="enable_auto_updates" value="yes" <?php checked( Admin_Options::get_setting('enable_auto_updates') === 'yes' ); ?> />
				</div>
			</td>
		</tr>

		<tr>
			<th>
				<?php esc_html_e( 'Mostrar notificação de atualização disponível', 'woo-custom-installments' ); ?>
				<span class="woo-custom-installments-description"><?php esc_html_e( 'Ative essa opção para que seja exibido uma notificação de atualização disponível.', 'woo-custom-installments' ); ?></span>
			</th>
			<td>
				<div class="form-check form-switch">
					<input type="checkbox" class="toggle-switch" id="enable_update_notices" name="enable_update_notices" value="yes" <?php checked( Admin_Options::get_setting('enable_update_notices') === 'yes' ); ?> />
				</div>
			</td>
		</tr>

		<tr class="container-separator"></tr>
		
		<tr>
			<td class="d-grid license-details-group">
				<h3 class="mb-4"><?php esc_html_e( 'Informações sobre a licença:', 'woo-custom-installments' ); ?></h3>

				<span class="mb-2 license-details-item"><?php esc_html_e( 'Status da licença:', 'woo-custom-installments' ) ?>
					<?php if ( License::is_valid() ) : ?>
						<span class="badge bg-translucent-success rounded-pill"><?php esc_html_e(  'Válida', 'woo-custom-installments' );?></span>
					<?php elseif ( empty( get_option('woo_custom_installments_license_key') ) ) : ?>
						<span class="fs-sm"><?php esc_html_e(  'Nenhuma licença informada', 'woo-custom-installments' );?></span>
					<?php else : ?>
						<span class="badge bg-translucent-danger rounded-pill"><?php esc_html_e(  'Inválida', 'woo-custom-installments' );?></span>
					<?php endif; ?>
				</span>

				<span class="mb-2 license-details-item"><?php esc_html_e( 'Recursos:', 'woo-custom-installments' ) ?>
					<?php if ( License::is_valid() ) : ?>
						<span class="badge bg-translucent-primary rounded-pill"><?php esc_html_e(  'Pro', 'woo-custom-installments' );?></span>
					<?php else : ?>
						<span class="badge bg-translucent-warning rounded-pill"><?php esc_html_e(  'Básicos', 'woo-custom-installments' );?></span>
					<?php endif; ?>
				</span>

				<?php if ( License::is_valid() ) :
					$license_key = get_option('woo_custom_installments_license_key');

					if ( strpos( $license_key, 'CM-' ) === 0 ) : ?>
						<span class="mb-2 license-details-item"><?php echo sprintf( esc_html__( 'Assinatura: Clube M - %s', 'woo-custom-installments' ), License::license_title() ) ?></span>
					<?php else : ?>
						<span class="mb-2 license-details-item"><?php echo sprintf( esc_html__( 'Tipo da licença: %s', 'woo-custom-installments' ), License::license_title() ) ?></span>
					<?php endif; ?>

					<span class="mb-2 license-details-item"><?php echo sprintf( esc_html__( 'Licença expira em: %s', 'woo-custom-installments' ), License::license_expire() ) ?></span>
					
					<span class="mb-2 license-details-item"><?php esc_html_e( 'Sua chave de licença:', 'woo-custom-installments' ) ?>
						<?php if ( ! empty( $license_key ) ) :
							echo esc_attr( substr( $license_key, 0, 9 ) . "XXXXXXXX-XXXXXXXX" . substr( $license_key, -9 ) );
						else :
							esc_html_e(  'Não disponível', 'woo-custom-installments' );
						endif; ?>
					</span>
				<?php endif; ?>
			</td>
		</tr>

		<?php if ( License::is_valid() ) : ?>
			<tr>
				<td class="d-flex align-items-center">
					<button type="submit" id="woo_custom_installments_deactive_license" class="btn btn-sm btn-primary" name="woo_custom_installments_deactive_license"><?php esc_html_e( 'Desativar licença', 'woo-custom-installments' ); ?></button>
					<button type="submit" id="woo_custom_installments_refresh_license" class="btn btn-sm btn-outline-primary ms-3 d-none" name="woo_custom_installments_refresh_license"><?php esc_html_e( 'Sincronizar licença', 'woo-custom-installments' ); ?></button>
				</td>
			</tr>
		<?php else :
			if ( get_option('woo_custom_installments_alternative_license_activation') === 'yes' ) : ?>
				<tr>
					<td>
						<span class="h4 d-block"><?php esc_attr_e( 'Notamos que teve problemas de conexão ao tentar ativar sua licença', 'woo-custom-installments' ); ?></span>
						<span class="d-block text-muted"><?php esc_attr_e( 'Você pode fazer upload do arquivo .key da licença para fazer sua ativação manual.', 'woo-custom-installments' ); ?></span>
						<a class="fancy-link mt-2 mb-3" href="https://meumouse.com/minha-conta/licenses/?domain=<?php echo urlencode( License::get_domain() ); ?>&license_key=<?php echo urlencode( get_option('woo_custom_installments_temp_license_key') ); ?>&app_version=<?php echo urlencode( WOO_CUSTOM_INSTALLMENTS_VERSION ); ?>&product_id=<?php echo ( strpos( get_option('woo_custom_installments_temp_license_key'), 'CM-' ) === 0 ) ? '7' : '1'; ?>&settings_page=<?php echo urlencode( License::get_domain() . '/wp-admin/admin.php?page=woo-custom-installments' ); ?>" target="_blank"><?php esc_html_e( 'Clique aqui para gerar seu arquivo de licença', 'woo-custom-installments' ) ?></a>

						<div class="drop-file-license-key">
							<div class="dropzone-license mt-4" id="license_key_zone">
								<div class="drag-text">
									<svg class="drag-and-drop-file-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19.937 8.68c-.011-.032-.02-.063-.033-.094a.997.997 0 0 0-.196-.293l-6-6a.997.997 0 0 0-.293-.196c-.03-.014-.062-.022-.094-.033a.991.991 0 0 0-.259-.051C13.04 2.011 13.021 2 13 2H6c-1.103 0-2 .897-2 2v16c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2V9c0-.021-.011-.04-.013-.062a.99.99 0 0 0-.05-.258zM16.586 8H14V5.414L16.586 8zM6 20V4h6v5a1 1 0 0 0 1 1h5l.002 10H6z"></path></svg>
									<?php echo esc_html( 'Arraste e solte o arquivo .key aqui', 'woo-custom-installments' ); ?>
								</div>

								<div class="file-list"></div>
								
								<form enctype="multipart/form-data" action="upload.php" class="upload-license-key" method="POST">
									<div class="drag-and-drop-file">
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="upload_license_key" name="upload_license_key" hidden>
											<label class="custom-file-label mb-4" for="upload_license_key"><?php echo esc_html( 'Ou clique para procurar seu arquivo', 'woo-custom-installments' ); ?></label>
										</div>
									</div>
								</form>
							</div>
						</div>
					</td>
				</tr>
			<?php else : ?>
				<tr>
					<td class="d-grid">
						<a class="btn btn-primary my-4 d-inline-flex w-fit" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugins-list&utm_campaign=wci" target="_blank">
							<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M13.5 16.5854C13.5 17.4138 12.8284 18.0854 12 18.0854C11.1716 18.0854 10.5 17.4138 10.5 16.5854C10.5 15.7569 11.1716 15.0854 12 15.0854C12.8284 15.0854 13.5 15.7569 13.5 16.5854Z"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6.33367 10C6.20971 9.64407 6.09518 9.27081 5.99836 8.88671C5.69532 7.68444 5.54485 6.29432 5.89748 4.97439C6.26228 3.60888 7.14664 2.39739 8.74323 1.59523C10.3398 0.793061 11.8397 0.806642 13.153 1.32902C14.4225 1.83396 15.448 2.78443 16.2317 3.7452C16.4302 3.98851 16.6166 4.23669 16.7907 4.48449C17.0806 4.89706 16.9784 5.45918 16.5823 5.7713C16.112 6.14195 15.4266 6.01135 15.0768 5.52533C14.9514 5.35112 14.8197 5.17831 14.6819 5.0094C14.0088 4.18414 13.2423 3.51693 12.4138 3.18741C11.6292 2.87533 10.7252 2.83767 9.64112 3.38234C8.55703 3.92702 8.04765 4.6748 7.82971 5.49059C7.5996 6.35195 7.6774 7.36518 7.93771 8.39788C8.07953 8.96054 8.26936 9.50489 8.47135 10H18C19.6569 10 21 11.3431 21 13V20C21 21.6569 19.6569 23 18 23H6C4.34315 23 3 21.6569 3 20V13C3 11.3431 4.34315 10 6 10H6.33367ZM19 13C19 12.4477 18.5523 12 18 12H6C5.44772 12 5 12.4477 5 13V20C5 20.5523 5.44772 21 6 21H18C18.5523 21 19 20.5523 19 20V13Z"></path></g></svg>	
							<span><?php esc_html_e(  'Comprar licença', 'woo-custom-installments' );?></span>	
						</a>

						<span class="bg-translucent-success fw-medium rounded-2 px-3 py-2 mb-4 d-flex align-items-center w-fit">
							<svg class="icon icon-success me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
							<?php esc_html_e( 'Informe sua licença abaixo para desbloquear todos os recursos.', 'woo-custom-installments' ) ?>
						</span>

						<span class="form-label d-block mt-2"><?php esc_html_e( 'Código da licença', 'woo-custom-installments' ) ?></span>
						
						<div class="input-group" style="max-width: 550px;">
							<input class="form-control" type="text" placeholder="XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX" id="woo_custom_installments_license_key" name="woo_custom_installments_license_key" size="50" value="<?php echo get_option( 'woo_custom_installments_license_key' ) ?>" />
							<button type="submit" id="woo_custom_installments_active_license" class="btn btn-primary" name="woo_custom_installments_active_license"><?php esc_html_e( 'Ativar licença', 'woo-custom-installments' ); ?></button>
						</div>
					</td>
				</tr>
			<?php endif;
		endif; ?>

		<tr class="container-separator"></tr>

		<tr class="w-75 mt-5">
			<td>
				<h3 class="h2 mt-0"><?php esc_html_e( 'Status do sistema:', 'woo-custom-installments' ); ?></h3>
				<h4 class="mt-4"><?php esc_html_e( 'WordPress', 'woo-custom-installments' ); ?></h4>
				<div class="d-flex align-items-center mb-2">
					<span><?php esc_html_e( 'Versão do WordPress:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2"><?php echo esc_html( get_bloginfo( 'version' ) ); ?></span>
				</div>
				<div class="d-flex align-items-center mb-2">
					<span><?php esc_html_e( 'WordPress Multisite:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2"><?php echo is_multisite() ? esc_html__( 'Sim', 'woo-custom-installments' ) : esc_html__( 'Não', 'woo-custom-installments' ); ?></span>
				</div>
				<div class="d-flex align-items-center mb-2">
					<span><?php esc_html_e( 'Modo de depuração do WordPress:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2"><?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? esc_html__( 'Ativo', 'woo-custom-installments' ) : esc_html__( 'Desativado', 'woo-custom-installments' ); ?></span>
				</div>

				<h4 class="mt-4"><?php esc_html_e( 'WooCommerce', 'woo-custom-installments' ); ?></h4>
				<div class="d-flex align-items-center mb-2">
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
				<div class="d-flex align-items-center mb-2">
					<span><?php esc_html_e( 'Versão do Parcelas Customizadas para WooCommerce:', 'woo-custom-installments' ); ?></span>
					<span class="ms-2">
						<span class="badge bg-translucent-success">
							<?php if ( License::is_valid() ) {
								$get_pro = esc_html__( 'Pro', 'woo-custom-installments' );
							} else {
								$get_pro = '';
							}

							echo sprintf( esc_html( WOO_CUSTOM_INSTALLMENTS_VERSION . ' %s' ), $get_pro ); ?>
						</span>
					</span>
				</div>

				<h4 class="mt-4"><?php esc_html_e( 'Servidor', 'woo-custom-installments' ); ?></h4>
				<div class="d-flex align-items-center mb-2">
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
				<div class="d-flex align-items-center mb-2">
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
				<div class="d-flex align-items-center mb-2">
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
				<div class="d-flex align-items-center mb-2">
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
					<div class="d-flex align-items-center mb-2">
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
					<div class="d-flex align-items-center mb-2">
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
					<div class="d-flex align-items-center mb-2">
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
					<div class="d-flex align-items-center mb-2">
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
					<div class="d-flex align-items-center mb-2">
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
					<div class="d-flex align-items-center mb-2">
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
		</tr>

		<tr class="container-separator"></tr>

		<tr>
			<td>
				<button id="wci_reset_settings_trigger" class="btn btn-sm btn-outline-warning d-flex align-items-center">
					<svg class="icon icon-lg icon-warning me-2" xmlns="http://www.w3.org/2000/svg"><path d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3-3 1.331-3 3 1.329 3 3 3z"></path><path d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219 9.053 9.053 0 0 0-2.43-2.43 8.95 8.95 0 0 0-3.219-1.355 9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053 7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725 7.11 7.11 0 0 1-.644 1.188 7.2 7.2 0 0 1-.858 1.039 7.028 7.028 0 0 1-3.536 1.907 7.13 7.13 0 0 1-2.822 0 6.961 6.961 0 0 1-2.503-1.054 7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034 9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183 9.014 9.014 0 0 0 3.218-1.355 8.886 8.886 0 0 0 1.331-1.099 9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"></path></svg>
					<?php esc_html_e( 'Redefinir configurações', 'woo-custom-installments' ); ?>
				</button>

				<div id="wci_reset_settings_container" class="popup-container">
					<div class="popup-content">
						<div class="popup-header border-bottom-0 justify-content-end">
							<button id="wci_close_reset" class="btn-close" aria-label="<?php esc_html( 'Fechar', 'woo-custom-installments' ); ?>"></button>
						</div>
						<div class="popup-body">
							<div class="d-flex flex-column align-items-center p-4">
								<div class="btn-icon rounded-circle p-2 mb-3 bg-translucent-danger">
									<svg class="icon icon-xg icon-danger" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M11.953 2C6.465 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.493 2 11.953 2zM12 20c-4.411 0-8-3.589-8-8s3.567-8 7.953-8C16.391 4 20 7.589 20 12s-3.589 8-8 8z"></path><path d="M11 7h2v7h-2zm0 8h2v2h-2z"></path></svg>
								</div>
								
								<h5 class="popup-title text-center my-2"><?php esc_html_e('Atenção! Você realmente deseja redefinir as configurações?', 'woo-custom-installments' ); ?></h5>
								<span class="title-hightlight bg-danger mt-2 mb-3"></span>
								<span class="text-muted fs-lg p-3"><?php esc_html_e( 'Ao redefinir as configurações do plugin, todas opções serão removidas, voltando ao estado original. Sua licença não será removida.', 'woo-custom-installments' ) ?></span>
							</div>
							
							<div class="my-4 p-3">
								<button id="confirm_reset_settings" class="btn btn-lg btn-outline-secondary"><?php esc_html_e( 'Sim, desejo redefinir', 'woo-custom-installments' ); ?></button>
							</div>
						</div>
					</div>
				</div>
			</td>

			<td class="d-flex">
				<a class="btn btn-sm btn-outline-danger d-flex align-items-center" target="_blank" href="https://meumouse.com/reportar-problemas/?wpf9053_2=<?php echo urlencode( WOO_CUSTOM_INSTALLMENTS_ADMIN_EMAIL ); ?>&wpf9053_9=<?php echo urlencode( License::is_valid() ? 'Sim' : 'Não' ) ?>&wpf9053_7=<?php echo urlencode( License::get_domain() ) ?>&wpf9053_6=<?php echo urlencode( wp_get_theme()->get('Name') ) ?>"><?php esc_html_e( 'Reportar problemas', 'woo-custom-installments' ); ?></a>
			</td>
		</tr>
	</table>
</div>