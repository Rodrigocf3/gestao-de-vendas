<?php
/**
 * cadastro.php - Autocadastro de usuário (sem confirmação por e-mail).
 * Após cadastrar, o usuário já entra logado.
 */
require_once __DIR__ . '/funcoes.php';

if (usuarioLogado() !== null) {
    voltarParaIndex();
}

$erros = [];
$nome  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    $nome      = is_string($_POST['nome'] ?? null) ? trim($_POST['nome']) : '';
    $email     = is_string($_POST['email'] ?? null) ? mb_strtolower(trim($_POST['email'])) : '';
    $senha     = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
    $confirmar = is_string($_POST['confirmar'] ?? null) ? $_POST['confirmar'] : '';

    if (mb_strlen($nome) < 2 || mb_strlen($nome) > 100) {
        $erros[] = 'Informe seu nome (2 a 100 caracteres).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (strlen($senha) < 6) {
        $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirmar) {
        $erros[] = 'A confirmação da senha não confere.';
    }

    if (!$erros) {
        try {
            if (R::findOne('usuario', 'email = ?', [$email])) {
                $erros[] = 'Já existe um usuário cadastrado com este e-mail.';
            } else {
                $usuario = R::dispense('usuario');
                $usuario->nome       = $nome;
                $usuario->email      = $email;
                $usuario->senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $usuario->criado_em  = date('Y-m-d H:i:s');
                $id = (int) R::store($usuario);

                iniciarSessao($id);   // acesso imediato, sem e-mail
                voltarParaIndex();
            }
        } catch (Throwable $erro) {
            redirecionar('erro.php?tipo=salvar');
        }
    }
}

$titulo = 'Cadastro';
require __DIR__ . '/topo.php';
?>
    <h1>Criar conta</h1>

    <?php foreach ($erros as $e): ?>
        <p class="alerta erro" role="alert"><?= h($e) ?></p>
    <?php endforeach; ?>

    <form class="form-auth" action="cadastro.php" method="post">
        <?= csrfCampo() ?>
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" maxlength="100" value="<?= h($nome) ?>" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" maxlength="150" value="<?= h($email) ?>" required>

        <label for="senha">Senha (mínimo 6 caracteres)</label>
        <input type="password" id="senha" name="senha" minlength="6" required>

        <label for="confirmar">Confirmar senha</label>
        <input type="password" id="confirmar" name="confirmar" minlength="6" required>

        <button type="submit" class="btn">Cadastrar</button>
    </form>

    <p class="rodape-auth">Já tem conta? <a href="login.php">Entrar</a></p>
<?php require __DIR__ . '/rodape.php'; ?>
