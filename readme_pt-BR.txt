=== QR Code PicPay para WooCommerce ===
Contributors: dadeke
Donate link: https://app.picpay.com/user/deividsondamasio
Tags: woocommerce, picpay, payment
Requires at least: 4.9
Tested up to: 5.3
Stable tag: 1.1.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adicione o PicPay E-Commerce como método de pagamento em sua loja WooCommerce.

== Descrição ==

### Adiciona o gateway PicPay E-Commerce ao WooCommerce ###

Este plugin adiciona o gateway PicPay E-Commerce ao WooCommerce.

Note que o WooCommerce deve estar instalado e ativo.

[PicPay E-Commerce](https://ecommerce.picpay.com/) é um método de pagamento brasileiro desenvolvido pela PicPay Serviços S.A.

Este plugin foi desenvolvido a partir da [documentação oficial do PicPay E-Commerce](https://ecommerce.picpay.com/doc/) e utiliza a versão mais recente da API de pagamentos.

Este plugin foi desenvolvido sem nenhum incentivo da PicPay Serviços S.A. Nenhum dos desenvolvedores deste plugin possuem vínculos com a empresa.

Este software é gratuito e não está associado ao PicPay. PicPay é uma marca registrada da PicPay Serviços S.A. Este plugin não está afiliado a PicPay Serviços S.A e, portanto, não é um produto oficial da PicPay.

= Como testar =

A API do PicPay não possui um ambiente de homologação. Então todos os testes de vendas devem ser feitos usando o ambiente de produção.
Os valores podem ser estornados pelo [Painel do Lojista - PicPay](https://lojista.picpay.com/login) ou pelo WooCommerce alterando o Status do pedido para "Cancelado".
Depois que o Status do pedido for alterado para "Cancelado" no WooCommerce, automaticamente (depois de alguns segundos) o plugin deverá alterar o Status do pedido para "Reembolsado".

= Contribuição =

Você pode contribuir para o código fonte no [GitHub](https://github.com/dadeke/woo-picpay).

= Compatibilidade =

Compatível com versões atuais do WooCommerce.

O uso do plugin [Brazilian Market on WooCommerce](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) é obrigatório, pois desta forma é possível enviar os campos de "CPF" ou "CNPJ" para processar o pagamento.

== FAQ ==

= Qual é a licença do plugin? =

Este plugin está licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce mais recente.
* Possuir uma conta no [PicPay E-commerce](https://ecommerce.picpay.com/ "PicPay E-commerce").
* Gerar o PicPay Token e o Seller Token no [Painel do PicPay](https://lojista.picpay.com/login "Painel do PicPay").
* Ter o plugin [Brazilian Market on WooCommerce](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) instalado e configurado.

= PicPay recebe pagamentos de quais países? =

No momento o PicPay recebe pagamentos apenas do Brasil.

O plugin foi configurado para receber pagamentos apenas de usuários que selecionarem o Brasil nas informações de pagamento durante o checkout.

== Screenshots ==

1. Configurações do plugin.
2. Método de pagamento na página de finalizar o pedido.
3. Exemplo da página de pagamento do PicPay.

== Changelog ==

= 1.1.5 - 03/01/2020 =

* Remoção de código não utilizado.
* WC testado até 3.8.1

= 1.1.4 - 25/09/2019 =

* Definido SVG PicPay com largura fixa.

= 1.1.3 - 24/09/2019 =

* Atualizado o nome do plugin de "WooCommerce PicPay" para "QR Code PicPay para WooCommerce".

= 1.1.2 - 30/07/2019 =

* Corrigido bug no cancelamento do pedido.

= 1.1.1 - 10/04/2019 =

* Alterado para salvar como chave única os metadados no pedido.

= 1.1.0 - 09/04/2019 =

* Adicionado o prefixo "PicPay_" em todos os metadados que são salvos no pedido.

= 1.0.0 - 03/03/2019 =

* Publicado a primeira versão.

== Aviso de atualização  ==

= 1.1.5 =

* Remoção de código não utilizado.

= 1.1.4 =

* Definido SVG PicPay com largura fixa.

= 1.1.3 =

* Atualizado o nome do plugin de "WooCommerce PicPay" para "QR Code PicPay para WooCommerce".

= 1.1.2 =

* Correção de bug.

= 1.1.1 =

* Salva os metadados do PicPay como chave única.

= 1.1.0 =

* Salva os metadados com um prefixo que identifica os dados do PicPay.

"Porque Deus tanto amou o mundo que deu o seu Filho Unigênito, para que todo o que nele crer não pereça, mas tenha a vida eterna." João 3:16