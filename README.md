## Laravel API REST
Criação de uma API REST feita em Laravel usando MySQL como Banco de Dados.

Esta API permite que os usuários criem uma conta, façam login, criem postagens (com recursos de edição e exclusão), comentem em postagens (com recursos de edição e exclusão) e recebam notificações quando outros usuários comentarem em suas postagens.

<hr>

### Endpoints

#### Autenticação
Criar conta ``` POST api/authenticate/register ```

Login ``` POST api/authenticate/login ```

Logout ``` POST api/authenticate/logout ```

<hr>

#### Redefinição de senha
Enviar link de redefinição para o e-mail do usuário ``` POST api/password/reset-link ```

Redefinir senha do usuário ``` POST api/password/reset ```

#### User
Informações do usuário logado ``` GET api/user/me ```

Informações do usuário pelo ID ``` GET api/user/{user} ```

Atualiza informações do usuário ``` PUT api/profile/update ```

Upload de imagem de perfil do usuário ``` POST apiprofile/upload-image ```

Deleta imagem de perfil do usuário ``` DELETE api/profile/delete-image ```

<hr>

#### Posts
Retorna todos os posts ``` GET api/post ```

Cria novo post ``` POST api/post ```

Retorna um post pelo ID ``` GET api/post/{id} ```

Atualiza um post pelo ID ``` PUT api/post/{id} ```

Deleta um post pelo ID ``` DELETE api/post/{id} ```

Pesquisa posts pela sua descrição ``` GET api/post/search/{query} ```

Cria um novo comentário em um post ``` POST api/post/{post}/comments ```

Atualiza um comentário ``` PUT api/post/comments/{comments} ```

Deleta um comentário ``` DELETE api/post/comments/{comments} ```

<hr>

#### Notificações
Retorna as notificações do usuário ``` GET api/notifications ```

Salva as notificações do usuário ``` POST api/notifications ```

Deleta as notificações do usuário ``` DELETE api/notifications ```

<hr>

#### Documentação Swagger (OpenApi)
``` GET api/documentation ```

<hr>

### Pré-requisitos
* PHP 8.3
* Composer
* Docker

<hr>

### Instalação
1. Clone o repositório:
```
git clone https://github.com/matheusmrqs4/posts-rest-api-laravel
```

2. Entre no diretório:
 
```
cd your-repo
```

3. Instale as dependências:
```
composer install
```

4. Crie um arquivo .env e preencha os dados:
```
cp .env.example .env
```

5. Gere uma nova chave da aplicação:
```
php artisan key:generate
```

6. Rode os Containers Docker:
```
docker-compose up -d --build
```

7. Acesse em:
```
http://127.0.0.1:8069/
```
