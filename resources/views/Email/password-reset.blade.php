<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
</head>

<body>
    <p>Olá, {{ $user->name }}!</p>
    <p>Você está recebendo este email porque recebemos uma solicitação de redefinição de senha para sua conta.</p>
    <p>Utilize o Token abaixo para redefinir sua senha:</p>
    <p>Token: {{ $token }}</p>
    <p>Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.</p>
    <p>Obrigado!</p>
</body>

</html>
