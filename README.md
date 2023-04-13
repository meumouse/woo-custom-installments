=== Parcelas Customizadas para WooCommerce ===
Contributors: meumouse
Tags: parcelas, parcelamento de produtos, parcelas avançadas, parcelas customizadas, woocommerce
Requires at least: 5.0
Tested up to: 6.0.3
Stable tag: 6.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Extensão que permite exibir o parcelamento e preço com desconto para lojas WooCommerce.

== Description ==

O plugin Parcelas Customizadas para WooCommerce permite informar aos seus clientes em quantas vezes podem parcelar suas compras,
quantas parcelas sem juros, desconto no pagamento por Pix, Boleto, ou outra forma que você queira.

A função deste plugin é apenas visual e informativa, não modifica valores no backend, é necessário que utilize outro plugin oferecer descontos por método de pagamento.

Desenvolvido com o framework Bootstrap 5.2.2, oferece estilos nativamente ao plugin, mas caso seu tema ofereça esse recurso você poderá desabilitar o carregamento do CSS.

Funciona com qualquer tema do WordPress.

Integrado com o plugin PagSeguro (Cálculo de parcelas baseado em fator de multiplicação, veja nossa central de ajuda.)

== Installation ==

Instale o Parcelas Customizadas para WooCommerce através do repositório de plugins WordPress.org ou fazendo upload dos arquivos para o seu servidor.

Ative o plugin Parcelas Customizadas para WooCommerce.

Ao ativar o plugin clique em "Configurar" nos links do plugin para seguir até a seção de configurações do plugin.

Defina os valores de taxas e descontos e salve para alterar as configurações padrão.


== Changelog ==

=  2.0.0 =

* Recurso adicionado: Shortcode [woo_custom_installments_modal] - APENAS EM PRODUTOS
* Recurso adicionado: Shortcode [woo_custom_installments_card_info] - APENAS EM PRODUTOS
* Recurso adicionado: Shortcode [woo_custom_installments_discount_and_card] - APENAS EM PRODUTOS
* Recurso adicionado: Shortcode [woo_custom_installments_table_installments] - APENAS EM PRODUTOS
* Recurso adicionado: Shortcode [woo_custom_installments_pix_container] - GLOBAL
* Recurso adicionado: Shortcode [woo_custom_installments_ticket_container] - GLOBAL
* Recurso adicionado: Shortcode [woo_custom_installments_credit_card_container] - GLOBAL
* Recurso adicionado: Shortcode [woo_custom_installments_debit_card_container] - GLOBAL
* Recurso adicionado: Texto antes do preço com desconto
* Recurso adicionado: Texto inicial em produtos variáveis (A partir de)
* Recurso adicionado: Título do botão acionador de parcelas
* Recurso adicionado: Título do container de transferências
* Recurso adicionado: Título do container de boleto bancário
* Recurso adicionado: Texto de instruções de boleto bancário
* Recurso adicionado: Título do container de cartões de crédito
* Recurso adicionado: Título do container de cartões de débito
* Recurso adicionado: Título da tabela de parcelas
* Recurso adicionado: Texto informativo de parcelas com juros
* Recurso adicionado: Texto informativo de parcelas sem juros
* Recurso adicionado: Título do container das formas de pagamento
* Recurso adicionado: Método do desconto no preço principal (Percentual ou fixo)
* Recurso adicionado: Mostrar emblema de desconto na finalização da compra
* Recurso adicionado: Mostrar informação de desconto na revisão do pedido
* Recurso adicionado: Desconto por método de pagamento
* Recurso adicionado: Ativar forma de pagamento Pix
* Recurso adicionado: Ativar forma de pagamento boleto bancário
* Recurso adicionado: Ativar forma de pagamento cartão de crédito
* Recurso adicionado: Ativar forma de pagamento cartão de débito
* Recurso adicionado: Ativar bandeiras (Mastercard, Visa, Elo, Hipercard, Diners Club, Discover, American Express, PayPal, Stripe, Mercado Pago, Pagseguro, Pagar.me e Cielo)
* Recurso adicionado: Personalização (Cor do preço com desconto, Tamanho da fonte do preço com desconto, Margem superior do preço com desconto, Margem inferior do preço com desconto, Ícone do preço com desconto, Cor do botão do popup de parcelas, Tamanho do botão do popup de parcelas, Margem superior do popup/sanfona de parcelas, Margem inferior do popup/sanfona de parcelas, Cor de exibição das parcelas, Tamanho da fonte de exibição das parcelas, Margem superior da exibição das parcelas, Margem inferior da exibição das parcelas, Ícone da exibição das parcelas)
* Recurso adicionado: Exibir emblema de aprovação imediata para formas de pagamentos
* Recurso adicionado: Posição das formas de pagamento e parcelas na página de produto individual
* Recurso adicionado: Tipo de exibição das parcelas (Popup ou sanfona)
* Recursos alterados: De => Exibição na página de produto individual e Exibição nos arquivos de produtos. Para => Exibir melhor parcela
* Melhorias em design no painel administrativo
* Correção de bugs
* Otimizações

= 1.3.0 =

* Recurso adicionado: Shortcode [woo_custom_installments_modal]
* Recurso adicionado: Alterar texto padrão das parcelas no produto individual
* Recurso adicionado: Alterar texto padrão das parcelas nos arquivos de produtos
* Recurso adicionado: Alterar texto padrão no popup dos detalhes do parcelamento
* Recurso adicionado: Permitir mostrar ícones: Pix, Boleto bancário e cartão de crédito
* Melhorias em design no painel administrativo
* Correção de bugs
* Otimizações

= 1.1.2 =
* Correção de bugs

= 1.1.0 =
* Correção de bugs
* Remoção da opção "Sempre exibir o preço do boleto"

= 1.0.5 =
* Correção de bugs

= 1.0.0 =
* Initial version
