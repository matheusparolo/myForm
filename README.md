# MyForm

## *NOTA: As informações abaixo estão parcialmente defasadas e precisam ser reescritas e mantidas em arquivos separados.*

## Instruções

Para usar esse sistema em um servidor interno siga os passos:

1. Clone esse repositório (Via Git Clone ou Download ZIP)
2. Insira o código SQL em um servidor MySQL (Código para download mais abaixo)
3. Na raiz do repositório clonado crie um arquivo chamado ".env" e coloque as informações de acordo (mais informações abaixo)
4. Na raiz use o composer para instalar as dependencias (Via composer install)
5. Inicie um servidor com interpretador PHP dentro do diretorio "public"


## Banco de dados
- SGBD - Mysql

- Arquivo mwb (Mysql Workbench):
https://drive.google.com/file/d/1M6CmerIwNB1fsNdNSaJczgcB8amkj3qw/view?usp=sharing

- Arquivo SQL:
https://drive.google.com/file/d/14BQrydmI-saFtOeYQY0uidjjrfcsWy6m/view?usp=sharing

## .env

### Definições
| Variavel | Tipo de Valor | Descrição |
| --- | --- | --- |
| appName | string | Nome do aplicativo, será usado por padrão no titulo das paginas
| db | string | Identifica o SGBD a ser utilizado
| dbHost | string | Endereço do banco de dados
| dbUser | string | Nome de usuário do banco de dados
| dbPass | string | Senha do usuario do banco de dados
| dbName | string | Identifica o schema a ser utilizado
| dbDebug | boolean | Define a ativação do debug do banco de dados
| slimDebug | boolean | Define a ativação do debug do Slim Framework

### Exemplo
```
appName=MyForm
db=mysql
dbHost=127.0.0.1
dbUser=root
dbPass=root
dbName=myForm

dbDebug=true
slimDebug=true
```

## Modelagem
- Web
  - https://docs.google.com/document/d/13cUJ-njdzZEvOrepqel8wlX8n-9GkkP0uWZMWh9ULaw/edit?usp=sharing

## Bibliotecas e Frameworks Utilizados

### Cliente-Side
- Bootstrap 4.1
- Jquery 3.3.1
- ChartJS

### Server-Side
- Slim Framework 3.0
- Pug 3.2

##Rotas

#### Principais
| Metodo |Formato | Rota |Descrição | Middlewares | Possiveis Redirecionamentos | Códigos de Resposta |
| ---  | --- | --- | --- | --- |--- |--- |
| GET  | HTML | / | Redireciona para a rota /pesquisa                            | | /pesquisa |


#### Autenticações
| Metodo |Formato | Rota |Descrição | Middlewares | Possiveis Redirecionamentos | Possiveis Códigos de Resposta |
| ---  | --- | --- | --- | --- |--- |--- |
| GET  | HTML | /usuario | Página de autenticação do usuario (Login ou Cadastro) | | /pesquisa |
| POST | JSON | /entrar  | Realiza o login do usuario | | | 000, 100, 200
| POST | JSON | /cadastrar  | Realiza o cadastro do usuario | |  | 000, 100, 200
| GET  | HTML | /sair | Realiza o logout | | /  |

#### Usuário
| Metodo |Formato | Rota |Descrição | Middlewares | Possiveis Redirecionamentos | Possiveis Códigos de Resposta |
| ---  | --- | --- | --- | --- |--- |--- |
| GET  | HTML | /usuario/editar | Página de edição de informações | Auth | /usuario, /erro
| POST | JSON | /editar | Realiza a edição das informações | Auth | | 000, 100, 200, 300
| POST | JSON | /editar_senha | Realiza a edição da senha | Auth | | 000, 100, 200, 300
| GET  | JSON | /buscar/{name} | Busca um usuario por um fragmento do seu nome | Auth | |  

#### Pesquisa
| Metodo |Formato | Rota |Descrição | Middlewares | Possiveis Redirecionamentos | Possiveis Códigos de Resposta |
| ---  | --- | --- | --- | --- |--- |--- |
| GET  | HTML | /pesquisa | Página que exibe todas as pesquisas das quais o usuario participa | Auth | /pesquisa/criar, /pesquisa/{id}/editar, /pesquisa/{id}/deletar, /pesquisa/{id}, /usuario,  /erro|
| GET  | JSON | /pesquisa/json | Retorna as pesquisas que o usuário participa em formato JSON | Auth | | 200, 300
| GET  | HTML | /pesquisa/criar | Página para a criação de uma nova pesquisa | Auth | /pesquisa, /usuario, /erro
| POST | JSON | /pesquisa/criar | Cria uma nova pesquisa | Auth | | 000, 200, 300
| GET  | HTML | /pesquisa/{id} | Exibe as informações da pesquisa | Auth, ResearchMember | /formulario/criar/{research-id}, /formulario/{id}/editar, /formulario/{id}/deletar, /formulario/{id}/resposta/enviar, /formulario/{id}/resultados, /usuario, /pesquisa, /erro
| GET  | JSON | /pesquisa/{id}/json | Retorna as informações da pesquisa em JSON | Auth, ResearchMember | | 200, 300, 400|
| GET  | HTML | /pesquisa/{id}/editar | Página para editar as informações da pesquisa | Auth, ResearchCreator | /pesquisa, /usuario, /pesquisa, /erro
| GET  | JSON | /pesquisa/{id}/editar/json | Retorna as informações da pesquisa para edição em JSON | Auth, ResearchCreator | | 200, 300, 400
| POST | JSON | /pesquisa/{id}/editar | Edita as informações da pesquisa | Auth, ResearchCreator | | 000, 200, 300, 400
| POST | JSON | /pesquisa/{id}/deletar | Deleta uma pesquisa | Auth, ResearchCreator | | 000, 200, 300, 400

#### Formulário
| Metodo |Formato | Rota |Descrição | Middlewares | Possiveis Redirecionamentos | Possiveis Códigos de Resposta |
| ---  | --- | --- | --- | --- |--- |--- |
| GET  | HTML | /formulario/criar/{research-id} | Página para criação de novo formulário | Auth, ResearchMember | /pesquisa/{id}, /usuario, /pesquisa, /erro
| POST | JSON | /formulario/criar | Cria um novo formulário | Auth, ResearchMember | | 000, 200, 300, 400
| GET  | JSON | /formulario/{id}/json | Retorna as informações do formulário em JSON | Auth, ResearchMember | | 200, 300, 400 |
| GET  | HTML | /formulario/{id}/editar | Página de edição do formulário | Auth, ResearchMember | /pesquisa/{id}, /usuario, /pesquisa, /erro | 
| GET  | HTML | /formulario/{id}/resposta/enviar | Página por onde as respostas são enviadas | Auth, ResearchMember | /usuario, /erro
| GET  | JSON | /formulario/{id}/resposta/{answerIndex} | Retorna as informações de uma resposta de acordo com seu indice | Auth, ResearchMember | |200, 300, 400
| GET  | HTML | /formulario/{id}/resultados | Página onde estão os resultados da formulário | Auth, ResearchMember | /usuario, /pesquisa, /erro
| GET  | JSON | /formulario/{id}/resultados/json | Retorna os resultados do formulario em JSON |  Auth, ResearchMember| |00, 300, 400
| POST | JSON | /formulario/editar | Edita o formulário | Auth, ResearchMember | | 000, 200, 300, 400
| POST | JSON | /formulario/deletar | Deleta o formulario | Auth, ResearchMembber | | 000, 200, 300, 400
| POST | JSON | /formulario|resposta/enviar | Envia uma resposta para o formulario | Auth, ResearchMember | | 000, 200, 300, 400

## Middlewares
| Nome | Descrição |
| --- | --- |
| Auth | Verifica se o cliente está autenticado
| ResearchCreator | Verifica se o cliente é o criador da pesquisa requisitada
| ResearchMember | Verifica se o cliente membro da pesquisa requisitada

## Códigos de Resposta
| Código | Significado |
| --- | --- |
| 000 | Sucesso|
| 100 | Erro esperado por dados inconsistentes
| 200 | Erro no banco de dados
| 300 | Usuário não autenticado
| 400 | Usuário sem acesso necessário
