# CodeIgniter 4 - Ecommerce

## Sobre o Projeto

Este projeto é um sistema de ecommerce completo desenvolvido com CodeIgniter 4. Ele possui todas as funcionalidades essenciais para um ecommerce, incluindo:

- Cadastro de usuários e administradores
- Gerenciamento de produtos, categorias e cupons de desconto
- Integração com a API do PagSeguro para pagamentos de pedidos
- Upload múltiplo de imagens para produtos
- Extração de relatórios de todos os CRUDs para arquivos XLSX

## Escopo do Projeto

O sistema é dividido em três áreas principais:

1. **Site**: Contém a listagem de produtos e carrinho de compras.
2. **Área do Administrador**: Onde é realizado todo o controle da loja virtual.
3. **Área do Cliente**: Para a seleção dos produtos, realização da compra e controle dos pedidos.

### Funcionalidades Principais

#### Site

- **Loja Virtual**: Disponível para visitantes, permitindo a seleção e adição de produtos ao carrinho.
- **Listagem de Produtos e Filtro**: Listagem paginada de produtos com filtros por nome, preço e categoria.

#### Painel do Administrador

- **Gerenciamento de Usuários**: Cadastro, edição, deleção e alteração de status de usuários.
- **Gerenciamento de Categorias**: Cadastro, edição, deleção e alteração de status de categorias.
- **Gerenciamento de Produtos**: Cadastro, edição, deleção, importação/exportação de produtos e gerenciamento de estoque.
- **Gerenciamento de Cupons**: Cadastro, edição, deleção e aplicação de cupons de desconto.
- **Gerenciamento de Pedidos**: Listagem, visualização e alteração de status de pedidos.

#### Painel do Cliente

- **Loja Virtual**: Visualização e seleção de produtos.
- **Carrinho de Compras**: Alteração de quantidade de produtos e finalização de compra.
- **Pagamento**: Integração com a API do PagSeguro para pagamentos via Cartão de Crédito, PIX e Boleto.
- **Pedidos**: Listagem e gerenciamento de pedidos, incluindo cancelamento e estorno de valores.

#### Geral 
- **Autenticação e recuperação de senha**: Implementado autenticação e recuperação de senhas para ambos usuários, clientes e administradores.
- **Validação**: Validações em todos formulários, garantindo os dados corretos.
- **Softdelete**: Utilização de softdelete para registros importantes como pagamentos.
- **AJAX**: Utilização de requisições AJAX para melhorar a experiência do usuário.
- **Modais**: Uso de modais para visualização de dados sem redirecionamento.

## Tecnologias Utilizadas

- **Backend**: CodeIgniter 4 em PHP
- **Banco de Dados**: MySQL
- **Pagamentos**: Integração com a API Charge do PagSeguro para pagamentos instantâneos com cartão de crédito, PIX e Boleto.
- **Relatórios**: PHPSpreadsheet
- **Templates ultilizados**: W3layouts para área pública e AdminLTE para área administrativa.
- **Sweet Alert**: Para modais e alertas.

## Iniciar

Siga os passos abaixo para configurar e iniciar o projeto:

1. Configure o arquivo `.env` com as credenciais do banco de dados.
2. **Adendo**: Certifique-se de que a base de dados `app_ecommerce` foi criada no seu servidor MySQL.
3. Execute as migrações:
    ```sh
    php spark migrate
    php spark migrate -n CodeIgniter\Settings
    php spark db:seed All
    ```
4. Inicie o servidor:
    ```sh
    php spark serve
    ```
