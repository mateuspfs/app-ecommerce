<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de senha</title>
</head>
<body>
    <h1>Olá, <?= $user->nome ?>.</h1>
    <h2>Recebemos seu pedido para redefinir a senha, segeu o link abaixo para continuar</h2>
    <p><a href="<?= url_to('auth.resetPassword') ?>?token=<?= urlencode($token) ?>&user_mail=<?= urlencode($user->email) ?>">Redefinir senha</a></p>

    <h3>Se não foi você, ignore este email.</h3>
</body>
</html>