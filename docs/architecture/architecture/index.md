# Arquitetura de Software

Bem-vindo à seção de Arquitetura de Software da documentação do projeto Memflash. Esta seção descreve a arquitetura geral do sistema, incluindo a organização de pastas e arquivos, bem como o uso do padrão MVC (Model-View-Controller) em ambos, o Back-end e o Front-end.

## Visão Geral

O projeto Memflash adota uma arquitetura MVC para garantir uma separação clara de responsabilidades e facilitar a manutenção e escalabilidade do sistema. A arquitetura é dividida em duas partes principais: o Back-end e o Front-end.

## Back-end (API)

### Estrutura de Pastas e Arquivos

A estrutura de pastas e arquivos do Back-end segue o padrão MVC:

- **controllers**: Contém os controladores que lidam com a lógica de negócios da aplicação.
- **models**: Aqui estão os modelos que representam os dados do sistema e interagem com o banco de dados.
- **views**: Embora o Back-end não tenha uma camada de visualização no sentido tradicional, pode incluir modelos de dados e respostas JSON que serão consumidas pelo Front-end.
- **routes**: Define as rotas da API, especificando quais controladores e métodos devem ser acionados em resposta a diferentes solicitações HTTP.
- **middlewares**: Contém middlewares utilizados para funções comuns, como autenticação e autorização.
- **config**: Armazena configurações gerais da aplicação.

### Padrão MVC no Back-end

- **Model**: Os modelos são responsáveis por definir a estrutura dos dados e interagir com o banco de dados. Eles encapsulam a lógica de acesso aos dados.
- **View**: No Back-end, as views não são usadas para renderizar páginas da web, mas podem incluir modelos de dados retornados pela API.
- **Controller**: Os controladores processam solicitações HTTP, chamam os modelos apropriados para buscar ou manipular dados e retornam respostas JSON aos clientes.

## Front-end

### Estrutura de Pastas e Arquivos

A estrutura de pastas e arquivos do Front-end também segue o padrão MVC:

- **src**: Contém o código-fonte do Front-end.
  - **components**: Aqui estão os componentes reutilizáveis da interface do usuário.
  - **models**: Define modelos de dados do Front-end, geralmente correspondendo aos modelos do Back-end.
  - **views**: Contém as views da aplicação, que são responsáveis por renderizar a interface do usuário.
  - **controllers**: Os controladores do Front-end gerenciam a interação entre as views e os modelos, controlando o fluxo de dados.
- **public**: Armazena recursos estáticos, como imagens e arquivos CSS.
- **config**: Pode conter configurações específicas do Front-end.

### Padrão MVC no Front-end

- **Model**: Os modelos do Front-end representam os dados que serão exibidos na interface do usuário. Eles podem ser sincronizados com os modelos do Back-end.
- **View**: As views são responsáveis por renderizar a interface do usuário com base nos dados do modelo.
- **Controller**: Os controladores do Front-end gerenciam a lógica de interação do usuário, atualizando modelos e views conforme necessário.

## Contribuições e Revisões

Se você tiver sugestões, revisões ou comentários sobre a arquitetura de software do projeto, sinta-se à vontade para contribuir para a documentação ou entrar em contato com a equipe de desenvolvimento. Sua contribuição é fundamental para manter a estrutura organizada e clara.

Agradecemos por sua colaboração na documentação da Arquitetura de Software do projeto Memflash.

Voltar para [Arquitetura e Desenvolvimento Técnico](../index.md)
