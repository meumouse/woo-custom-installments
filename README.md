# Parcelas Customizadas para WooCommerce®

Extensão que permite adicionar detalhes de parcelamento, desconto e formas de pagamento aceitas em lojas WooCommerce, tornando a experiência de compra mais flexível e conveniente.

O melhor plugin de parcelas e descontos para WooCommerce!

---

#### Propriedade intelectual:
O software Parcelas Customizadas para WooCommerce ® é uma propriedade registrada da MEUMOUSE.COM® – SOLUÇÕES DIGITAIS LTDA, em conformidade com o §2°, art. 2° da Lei 9.609, de 19 de Fevereiro de 1998.
É expressamente proibido a distribuição ou cópia ilegal deste software, sujeita a penalidades conforme as leis de direitos autorais vigentes.

---

### Instalação:

#### Instalação via painel de administração:

Você pode instalar um plugin WordPress de duas maneiras: via o painel de administração do WordPress ou via FTP. Aqui estão as etapas para ambos os métodos:

* Acesse o painel de administração do seu site WordPress.
* Vá para “Plugins” e clique em “Adicionar Novo”.
* Digite o nome do plugin que você deseja instalar na barra de pesquisa ou carregue o arquivo ZIP do plugin baixado.
* Clique em “Instalar Agora” e espere até que o plugin seja instalado.
* Clique em “Ativar Plugin”.

#### Instalação via FTP:

* Baixe o arquivo ZIP do plugin que você deseja instalar.
* Descompacte o arquivo ZIP em seu computador.
* Conecte-se ao seu servidor via FTP.
* Navegue até a pasta “wp-content/plugins”.
* Envie a pasta do plugin descompactada para a pasta “plugins” no seu servidor.
* Acesse o painel de administração do seu site WordPress.
* Vá para “Plugins” e clique em “Plugins Instalados”.
* Localize o plugin que você acabou de instalar e clique em “Ativar”.
* Após seguir essas etapas, o plugin deve estar instalado e funcionando corretamente em seu site WordPress.

---

### Registro de alterações (Changelogs):

Versão 5.5.1 (17/08/2025)
* Correção de bugs
    - Informação de economia no Pix não era exibido no loop de produtos
* Otimizações
    - Carregamento de classes dinâmica
* Recurso adicionado: Controles de estilos para widgets do Elementor para tema XStore
* Recurso adicionado: Controles de estilos para loop carrossel e loop grid do Elementor
* Recurso adicionado: Filtro para desenvolvedores alterarem a prioridade do componente de preço ('Woo_Custom_Installments/Price/Priority')

Versão 5.5.0 (07/08/2025)
* Correção de bugs
    - Preço da variação é alterado incorretamente na integração com Woodmart
    - Faixa de preço em produtos variáveis não é exibida
* Otimizações

Versão 5.4.11 (07/07/2025)
* Correção de bugs
    - Preço antigo não exibe em produtos variáveis
    - Estilos para computador sobrepõe estilos para celular

Versão 5.4.10 (04/07/2025)
* Correção de compatibilidade com tema Woodmart

Versão 5.4.9 (01/07/2025)
* Otimizações
    - Prioridade de estilos
* Recurso removido: Forçar prioridade dos estilos

Versão 5.4.8 (25/06/2025)
* Correção de bugs
    - Retornando preço 0,00 quando o preço não está informado
    - Unsupported operand type: string - float: /inc/Core/Calculate_Values.php:345
    - Cálculo de descontos quando há produtos com descontos diferentes no carrinho
* Otimizações
    - Ícone da sanfona de métodos de pagamento
* Recurso modificado: Emblema de desconto da forma de pagamento: Valor total de descontos

Versão 5.4.7 (17/06/2025)
* Correção de bugs:
    - Call to undefined method get_available_variations() in Render_Elements.php on line 326

Versão 5.4.6 (17/06/2025)
* Correção de bugs
    - Variável {{ total }} não atualiza o preço total de parcelas
    - Duplicidade de elementos de parcelamento e descontos
* Otimizações
* Recurso adicionado: Mostrar notificação de atualização disponível
* Compatibilidade com assinaturas variáveis

Versão 5.4.5 (12/06/2025)
* Correção de bugs
    - •	Uncaught DivisionByZeroError: Division by zero in /inc/Core/Calculate_Values.php:45
* Otimizações

Versão 5.4.4 (10/06/2025)
* Correção de bugs
    - Notificação de atualização disponível não some após plugin atualizado
    - Atualização de preços em produtos simples com Tiered Price Table

Versão 5.4.3 (06/06/2025)
* Correção de bugs
    - Parcelamento não é renderizado em produtos diferentes de simples ou variável
    - Texto de parcelamento não é exibido
    - Preço do produto não é atualizado ao alterar quantidade em produtos simples
* Otimizações

Versão 5.4.2 (05/06/2025)
* Correção de bugs
    - Verificação com tema Woodmart Child
    - Preço antigo não é atualizado com Tiered Price Table
* Otimizações
* Recurso adicionado: Compatibilidade com tema Shoptimizer

Versão 5.4.1 (05/06/2025)
* Correção de bugs
    - Uncaught Error: Class "Elementor\Plugin" not found

Versão 5.4.0 (04/06/2025)
* Correção de bugs
    - Removido a duplicidade de parcelas quando a exibição das melhores parcelas são com juros e sem juros
    - ID do produto incorreto no editor Elementor
    - Melhores parelas não aparecem de imediato ao editar com Elementor
    - Desconto individual só é aplicado para o primeiro produto quando há multiplos produtos com descontos individuais
* Otimizações
* Mudança de arquitetura para MACI (Modular Autoload Class Initialization)
* Recurso adicionado: Ativar atualizações automáticas
* Recurso removido: Compatibilidade com tema EpicJungle
* Recurso removido: Ativar atualização de valores em elementos em produtos variáveis
* Recurso removido: Método de atualização do preço -> Remover faixa de preço em produtos variáveis
* Recurso adicionado: Controlador de estilos para widgets do Elementor para emblema de desconto
* Recurso adicionado: Widget para Elementor: Emblema de desconto
* Recurso adicionado: Shortcode [woo_custom_installments_sale_badge]: Emblema de desconto
* Recurso adicionado: Atualizar preço do produto a partir da quantidade
* Recurso modificado: Local de exibição do preço com desconto no Pix
* Recurso modificado: Local de exibição da informação de economia no Pix
* Recurso modificado: Local de exibição das melhores parcelas
* Recurso modificado: Arquivo de tradução em ingês en_US atualizado
* Recurso modificado: Arquivo de tradução em espanhol es_ES atualizado

Versão 5.3.0 (29/01/2025)
* Correção de bugs
* Otimizações

Versão 5.2.7 (17/12/2024)
* Correção de bugs
* Otimizações

Versão 5.2.6 (12/12/2024)
* Correção de bugs
* Otimizações
* Recurso modificado: Centralizar grupo de preços na grade de produtos

Versão 5.2.5 (10/12/2024)
* Correção de bugs
* Otimizações
* Recurso adicionado: Formato de ícones
* Recurso adicionado: Ativar atualização de valores em elementos em produtos variáveis
* Recurso adicionado: Ativar emblema de percentual de desconto
* Recurso modificado: Ordem dos elementos
* Recurso moficicado: Texto de exibição das parcelas (Formas de pagamento)
* Recurso moficicado: Texto de exibição das parcelas (Arquivos de produtos)
* Recurso moficicado: Texto de exibição das parcelas (Produto individual)

Versão 5.2.3 (26/11/2024)
* Correção de bugs

Versão 5.2.2 (26/11/2024)
* Correção de bugs
* Otimizações

Versão 5.2.1 (23/09/2024)
* Correção de bugs
* Otimizações
* Compatibilidade com tema Ricky

Versão 5.2.0 (17/09/2024)
* Correção de bugs
* Otimizações
* Links da documentação atualizados
* Compatibilidade com Rank Math JSON LD - Exibir preço com desconto em dados estruturados (Schema.org)
* Recurso adicionado: Ativar widgets para Elementor
* Recurso adicionado: Empilhamento de preços em widgets
* Recurso adicionado: Forma de exibição da mensagem de desconto
* Recurso adicionado: Widget para Elementor: Mensagem de desconto por quantidade
* Recurso modificado: Personalizar preço do produto

Versão 5.1.2 (12/09/2024)
* Correção de bugs

Versão 5.1.0 (10/09/2024)
* Correção de bugs
* Otimizações

Versão 5.0.0 (06/09/2024)
* Correção de bugs
* Otimizações
* Recurso adicionado: Opção "Gancho personalizado" para Posição das formas de pagamento e parcelas na página de produto individual
* Recurso adicionado: Widget para Elementor: Preço do produto
* Recurso adicionado: Widget para Elementor: Popup - Formas de pagamento
* Recurso adicionado: Widget para Elementor: Sanfona - Formas de pagamento
* Recurso adicionado: Widget para Elementor: Bandeiras de cartão de crédito
* Recurso adicionado: Widget para Elementor: Bandeiras de cartão de débito
* Recurso adicionado: Widget para Elementor: Tabela de parcelamento
* Recurso adicionado: Widget para Elementor: Caixa de informação de preço
* Recurso modificado: Remover faixa de preço em produtos variáveis

Versão 4.5.3 (23/08/2024)
* Correção de bugs

Versão 4.5.2 (22/08/2024)
* Correção de bugs
* Otimizações
* Integração com plugin Tiered Pricing Table

Versão 4.5.1 (16/08/2024)
* Correção de bugs
* Otimizações

Versão 4.5.0 (10/08/2024)
* Correção de bugs
* Otimizações
* Nova arquitetura
* Opção removida: Desativar atualização dinâmica de parcelas em produtos variáveis
* Shortcode adicionado: [woo_custom_installments_get_price_on_pix] - Para recuperar o valor do produto no Pix
* Shortcode adicionado: [woo_custom_installments_get_price_on_ticket] - Para recuperar o valor do produto no Boleto bancário
* Shortcode adicionado: [woo_custom_installments_get_economy_pix_price] - Para recuperar o valor da economia no Pix
* Recurso modificado: Ordem dos elementos - Preço do produto adicionado para alteração da ordem de exibição
* Recurso adicionado: API de transientes para recuperação de opções do plugin

Versão 4.3.1 (20/04/2024)
* Correção de bugs

Versão 4.3.0 (17/04/2024)
* Correção de bugs
* Otimizações
* Recurso adicionado: Ativação alternativa de licenças
* Arquivo modelo de traduções atualizado
* Arquivo de tradução idioma en_US (Inglês americano) atualizado
* Arquivo de tradução idioma es_ES (Espanhol) atualizado

Versão 4.2.0 (06/04/2024)
* Correção de bugs
* Compatibilidade com Clube M

Versão 4.1.0 (28/03/2024)
* Correção de bugs
* Otimizações
* Recurso adicionado: Gancho "woo_custom_installments_before_installments_container"
* Recurso adicionado: Gancho "woo_custom_installments_after_installments_container"
* Recurso adicionado: Gancho "woo_custom_installments_popup_header"
* Recurso adicionado: Gancho "woo_custom_installments_popup_bottom"
* Recurso adicionado: Gancho "woo_custom_installments_accordion_header"
* Recurso adicionado: Gancho "woo_custom_installments_accordion_bottom"

Versão 4.0.0 (28/03/2024)
* Correção de bugs
* Otimizações
* Recurso adicionado: Ativar preço com desconto no Pix em Post Meta para Feed XML

Versão 3.8.5 (13/03/2024)
* Correção de bugs

Versão 3.8.1 (02/03/2024)
* Alteração de servidor de verificação de licenças

Versão 3.8.0 (29/02/2024)
* Correção de bugs
* Otimizações
* Opção removida: Ordem do desconto no boleto bancário
* Opção removida: Ordem da melhor parcela
* Opção removida: Ocultar informação de desconto e parcelas sem variação selecionada
* Opção removida: Texto informativo para seleção de variação
* Opção removida: Informar juros desde a primeira parcela
* Opção adicionada: Texto informativo para desconto por quantidade

Versão 3.6.7 (25/01/2024)
* Correção de bugs

Versão 3.6.5 (19/01/2024)
* Correção de bugs

Versão 3.6.2 (01/12/2023)
* Recurso adicionado: Opção "Ocultar" para Local de exibição do preço com desconto
* Recurso adicionado: Local de exibição da informação de economia no Pix
* Correção bugs
* Otimizações

Versão 3.6.0 (28/11/2023)
* Recurso removido: Desativar atualização de valores na finalização de compra
* Recurso adicionado: Bandeiras de cartão para forma de pagamento Cartão de crédito
* Recurso adicionado: Bandeiras de cartão para forma de pagamento Cartão de débito
* Recurso adicionado: Emblema de economia no Pix
* Recurso adicionado: Shortcode [woo_custom_installments_economy_pix_badge] - Apenas para produtos
* Recurso alterado: Shortcode [woo_custom_installments_discount_and_card] para -> [woo_custom_installments_group] - Apenas para produtos
* Recurso adicionado: Shortcode [woo_custom_installments_pix_info] - Apenas para produtos
* Correção bugs
* Otimizações

Versão 3.4.8 (22/11/2023)
* Correção de bugs

Versão 3.4.7 (21/11/2023)
* Correção de bugs
* Tradução para espanhol (Spanish)

Versão 3.4.6 (20/11/2023)
* Correção de bugs
* Otimizações

Versão 3.4.5 (17/11/2023)
* Correção no carregamento de estilos

Versão 3.4.3 (16/11/2023)
* Correção de problemas com verificação de licença
  
Versão 3.4.2 (16/11/2023)
* Correção de bugs
* Otimizações

Versão 3.2.5 (27/10/2023)
* Correção de bugs
* Otimizações

Versão 3.2.0 (24/10/2023)
* Compatibilidade com WooCommerce High-Performance Order Storage (HPOS)
* Recurso alterado: Remover faixa de preço em produtos variáveis
* Correção bugs
* Otimizações

Versão 3.0.0 (27/09/2023)
* Recurso adicionado: Novo estilo do botão de popup de parcelas
* Recurso adicionado: Informar desconto para cada produto individualmente
* Recurso alterado: Mostrar informação de preço no Pix, mesmo com juros zero
* Opção removida: Mostrar informação de desconto na revisão do pedido
* Opção removida: Mostrar informação de juros na revisão do pedido
* Correção de bugs
* Otimizações
* Melhorias no painel administrativo

Versão 2.9.2 (18/08/2023)
* Correção de bugs
* Otimizações

Versão 2.9.0 (24/07/2023)
* Correção de bugs
* Otimizações

Versão 2.8.0 (10/07/2023)
* Recurso adicionado: Emblema de desconto no boleto bancário
* Recurso adicionado: Shortcode [woo_custom_installments_ticket_discount_badge]
* Recurso adicionado: Adicionar texto personalizado após o preço do produto
* Recurso adicionado: Ativar mensagem nos produtos elegíveis para desconto por quantidade
* Correção de bugs
* Otimizações

Versão 2.7.2 (20/06/2023)
* Recurso adicionado: Desconto por quantidade mínima
* Recurso adicionado: Opção Ocultar no Tipo de exibição das parcelas
* Correção de bugs
* Otimizações

Versão 2.4.0 (29/05/2023)
* Correção na biblioteca de ícones Font Awesome
* Novo recurso adicionado: Habilitar funções de descontos
* Novo recurso adicionado: Habilitar funções de juros
* Novo recurso adicionado: Incluir valor de frete no desconto do pedido
* Novo recurso adicionado: Remover faixa de preço em produtos variáveis
* Correção de bugs
* Otimizações

Versão 2.3.5 (18/05/2023)
* Recurso adicionado: Desativar atualização de valores na finalização de compra
* Recurso adicionado: Adicionar juros por método de pagamento
* Correção de problema: Tabela de parcelas não atualiza valor quando seleciona variação de produto
* Correção no cálculo de juros das parcelas com taxa de juros padrão 
* Correção de bugs
* Otimizações

Versão 2.2.0 (05/05/2023)
* Recurso adicionado: Centralizar melhor parcela e desconto na grade de produtos
* Recurso adicionado: Centralizar melhor parcela e desconto no produto individual
* Correção no cálculo de desconto na finalização de compra
* Correção de bugs
* Otimizações

Versão 2.1.0 (24/04/2023)
* Recurso adicionado: Arredondamento do preço com desconto
* Recurso adicionado: Arredondamento do botão de parcelas
* Recurso adicionado: Posição da melhor parcela
* Recurso adicionado: Juros por parcela
* Correção de bugs
* Otimização de conexão com API

Versão 2.0.0 (13/04/2023)
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

Versão 1.3.0 (21/12/2022)
* Recurso adicionado: Shortcode [woo_custom_installments_modal]
* Recurso adicionado: Alterar texto padrão das parcelas no produto individual
* Recurso adicionado: Alterar texto padrão das parcelas nos arquivos de produtos
* Recurso adicionado: Alterar texto padrão no popup dos detalhes do parcelamento
* Recurso adicionado: Permitir mostrar ícones: Pix, Boleto bancário e cartão de crédito
* Melhorias em design no painel administrativo
* Correção de bugs
* Otimizações

Versão 1.2.0 (22/11/2022)
* Correção de bugs
* Otimizações

Versão 1.1.2 (31/10/2022)
* Correção de bugs

Versão 1.1.0 (31/10/2022)
* Correção de bugs
* Remoção da opção "Sempre exibir o preço do boleto"

Versão 1.0.5 (05/09/2022)
* Correção de bugs

Versão 1.0.0 (18/08/2022)
* Versão inicial