<?php
/**
 * login.php - Autenticação por e-mail e senha.
 */
require_once __DIR__ . '/funcoes.php';

if (usuarioLogado() !== null) {
    voltarParaIndex();
}

$erro  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    $email = is_string($_POST['email'] ?? null) ? mb_strtolower(trim($_POST['email'])) : '';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

    try {
        $usuario = $email !== '' ? R::findOne('usuario', 'email = ?', [$email]) : null;
    } catch (Throwable $e) {
        redirecionar('erro.php?tipo=banco');
    }

    if ($usuario && password_verify($senha, $usuario->senha_hash)) {
        iniciarSessao((int) $usuario->id);
        voltarParaIndex();
    }
    $erro = 'E-mail ou senha incorretos.';
}

$titulo = 'Entrar';
require __DIR__ . '/topo.php';
?>
    <h1>(TODO LIST) Entrar</h1>

    <?php if ($erro): ?>
        <p class="alerta erro" role="alert"><?= h($erro) ?></p>
    <?php endif; ?>

    <form class="form-auth" action="login.php" method="post">
        <?= csrfCampo() ?>
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= h($email) ?>" required autofocus>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit" class="btn">Entrar</button>
    </form>

    <p class="rodape-auth">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
<?php require __DIR__ . '/rodape.php'; ?>
