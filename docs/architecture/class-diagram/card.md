# Diagrama de Classes UML para ICard

## Card

Classe que representa um cartão (Card) no sistema.

```sh
+----------------------------------------------------+
|                        Card                        |
+----------------------------------------------------+
| - audio: File                                      |
| - midia: File                                      |
| - deck_origin: {                                   |
|     -_id: string                                   |
|     - deck_name: string                            |
| }                                                  |
| - english_message: string                          |
| - fonetics_message: string                         |
| - portuguese_message: string                       |
| - _id?: string                                     |
+----------------------------------------------------+
|                                                    |
| - constructor(                                     |
|     audio: File,                                   |
|     midia: File,                                   |
|     deck_origin: {                                 |
|         _id: string,                               |
|         deck_name: string,                         |
|     },                                             |
|     english_message: string,                       |
|     fonetics_message: string,                      |
|     portuguese_message: string,                    |
|     _id?: string                                   |
|   )                                                |
|                                                    |
+----------------------------------------------------+
```

## CardRequest

Classe que representa uma requisição de criação ou atualização de um cartão.

```sh
+----------------------------------------------------+
|                   CardRequest                      |
+----------------------------------------------------+
| - audio: File                                      |
| - midia: File                                      |
| - deck_origin: string                              |
| - english_message: string                          |
| - fonetics_message: string                         |
| - portuguese_message: string                       |
| - _id?: string                                     |
+----------------------------------------------------+
|                                                    |
| - constructor(                                     |
|     audio: File,                                   |
|     midia: File,                                   |
|     deck_origin: string,                           |
|     english_message: string,                       |
|     fonetics_message: string,                      |
|     portuguese_message: string,                    |
|     _id?: string                                   |
|   )                                                |
|                                                    |
+----------------------------------------------------+
```

## CardResponse

Classe que representa uma resposta contendo detalhes de um cartão.

```sh
+----------------------------------------------------+
|                  CardResponse                      |
+----------------------------------------------------+
| - audio: {                                         |
|     - file_name: string                            |
|     - file_url: string                             |
|     - key: string                                  |
|     - type: string                                 |
| }                                                  |
| - midia: {                                         |
|     - file_name: string                            |
|     - file_url: string                             |
|     - key: string                                  |
|     - type: string                                 |
| }                                                  |
| - deck_origin: {                                   |
|     -_id: string                                   |
|     - deck_name: string                            |
| }                                                  |
| - english_message: string                          |
| - fonetics_message: string                         |
| - portuguese_message: string                       |
| - _id?: string                                     |
+----------------------------------------------------+
|                                                    |
| - constructor(                                     |
|     audio: {                                       |
|         file_name: string,                         |
|         file_url: string,                          |
|         key: string,                               |
|         type: string,                              |
|     },                                             |
|     midia: {                                       |
|         file_name: string,                         |
|         file_url: string,                          |
|         key: string,                               |
|         type: string,                              |
|     },                                             |
|     deck_origin: {                                 |
|         _id: string,                               |
|         deck_name: string,                         |
|     },                                             |
|     english_message: string,                       |
|     fonetics_message: string,                      |
|     portuguese_message: string,                    |
|     _id?: string                                   |
|   )                                                |
|                                                    |
+----------------------------------------------------+
```

Voltar para [Diagrama de Classes UML](index.md)
