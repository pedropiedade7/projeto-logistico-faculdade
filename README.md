# Gerenciador Logístico - APO

Projeto desenvolvido para a disciplina de Fundamentos de Programação para Internet (FPI).

## Sobre o Sistema

O sistema é uma aplicação web simples desenvolvida em PHP com banco de dados MySQL para controle de logística. Ele permite:

- Cadastrar e gerenciar **Produtos** (com peso, volume e valor)
- Cadastrar **Endereços** de origem e destino (com busca automática de CEP via ViaCEP)
- Cadastrar **Transportadores** com dados de veículos e contato
- Gerar **Fretes** associando 1 transportador a múltiplos produtos
- Visualizar os detalhes do frete gerado com cálculo de peso e valor total
- Exportar relatório dos fretes em formato CSV por período

## Tecnologias Utilizadas

- PHP 8
- MySQL
- HTML5 / CSS3 / JavaScript
- Bootstrap 5

## Como rodar o projeto no XAMPP

1. Copie a pasta `projeto-logistico-faculdade` para dentro de `C:\xampp\htdocs\`.
2. Inicie o Apache e o MySQL no painel do XAMPP.
3. Abra o phpMyAdmin (`http://localhost/phpmyadmin`).
4. Crie o banco de dados importando o arquivo `sql/schema.sql` ou execute o script SQL contido nele.
5. Abra o navegador e acesse:
   ```
   http://localhost/projeto-logistico-faculdade/
   ```
