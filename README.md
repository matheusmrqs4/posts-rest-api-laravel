## Laravel API REST
Criação de uma API REST feita em Laravel usando MySQL como Banco de Dados.

Essa API permite que os usuários criem uma conta, façam login, criem postagens (com recursos de edição e exclusão) e comentem em postagens (com recursos de edição e exclusão).

<hr>

### Endpoints

#### Autenticação
Criar conta ``` POST api/authenticate/register ```

Login ``` POST api/authenticate/login ```

Logout ``` POST api/authenticate/logout ```

<hr>

#### Redefinição de senha
Enviar link de redefinição para o e-mail do usuário ``` POST /password/reset-link ```

Redefinir senha do usuário ``` POST /password/reset ```

#### User
Informações do usuário logado ``` GET /user/me ```

Informações do usuário pelo ID ``` GET /user/{user} ```

Atualiza informações do usuário ``` PUT /profile/update ```

Upload de imagem de perfil do usuário ``` POST /profile/upload-image ```

Deleta imagem de perfil do usuário ``` DELETE /profile/delete-image ```

<hr>

#### Posts
Retorna todos os posts ``` GET /post ```

Cria novo post ``` POST /post ```

Retorna um post pelo ID ``` GET /post/{id} ```

Atualiza um post pelo ID ``` PUT /post/{id} ```

Deleta um post pelo ID ``` DELETE /post/{id} ```

Pesquisa posts pela sua descrição ``` GET /post/search/{query} ```

Cria um novo comentário em um post``` POST /post/{post}/comments ```

Atualiza um comentário ``` PUT /post/comments/{comments} ```

Deleta um comentário ``` DELETE /post/comments/{comments} ```

<hr>

#### Notificações
Retorna as notificações do usuário ``` GET /notifications ```

Salva as notificações do usuário ``` POST /notifications ```

Deleta as notificações do usuário ``` DELETE /notifications ```

<hr>

#### Documentação Swagger (OpenApi)
``` GET /documentation ```

<hr>


### Como rodar o projeto
> Faça um git clone desse projeto: **git clone https://github.com/matheusmrqs4/rest-api-laravel.git**;
 
> Preencha os dados do **.env** (.env.example);

> Rode os Containers Docker: **docker-compose up -d --build**.
