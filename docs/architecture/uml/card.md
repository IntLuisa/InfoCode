# UML do Banco de Dados - Entidade Card

O diagrama UML a seguir representa a estrutura da entidade "Card" no banco de dados do projeto Memflash.

## Descrição da Entidade Card

- **fonetics_message**: Mensagem fonética do cartão.
- **english_message**: Mensagem em inglês do cartão.
- **portuguese_message**: Mensagem em português do cartão.
- **deck_origin**: Referência ao Deck de origem do cartão (opcional).
- **order**: Ordem única do cartão.
- **audio**: Detalhes do áudio associado ao cartão.
  - **file_name**: Nome do arquivo de áudio.
  - **file_url**: URL do arquivo de áudio.
  - **key**: Chave associada ao áudio.
  - **type**: Tipo do áudio.
- **midia**: Detalhes da mídia associada ao cartão.
  - **file_name**: Nome do arquivo de mídia.
  - **file_url**: URL do arquivo de mídia.
  - **key**: Chave associada à mídia.
  - **type**: Tipo da mídia.
- **created_at**: Data de criação do cartão.
- **updated_at**: Data de última atualização do cartão.

## Diagrama UML

![Diagrama UML da Entidade Card](uml/card_uml.png)

Voltar para [UML do Banco de Dados](index.md)
