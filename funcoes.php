<?php
/**
 * funcoes.php
 * Conexão, sessão, autenticação, proteção CSRF e funções auxiliares
 * compartilhadas por todas as páginas.
 */
require_once __DIR__ . '/conexao.php';

// ---------- Sessão (cookie com httponly e SameSite) ----------
if (session_status() === PHP_SESSION_NONE) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!isset($_SESSION['filtro'])) {
    $_SESSION['filtro'] = 'todas'; // todas | concluidas | pendentes
}

// ---------- Utilitários ----------

/** Escapa texto para exibição segura em HTML (previne XSS). */
function h(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Redireciona e encerra o script. */
function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function voltarParaIndex(): void
{
    redirecionar('index.php');
}

/** Guarda uma mensagem para ser exibida na próxima página. */
function definirFlash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

/** Lê e apaga a mensagem guardada (ou null). */
function pegarFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// ---------- Autenticação ----------

/** Retorna o usuário logado (bean) ou null. */
function usuarioLogado()
{
    static $usuario = false;
    if ($usuario !== false) {
        return $usuario;
    }
    $id = (int) ($_SESSION['usuario_id'] ?? 0);
    $usuario = null;
    if ($id > 0) {
        $bean = R::load('usuario', $id);
        if ($bean->id) {
            $usuario = $bean;
        } else {
            unset($_SESSION['usuario_id']); // usuário não existe mais
        }
    }
    return $usuario;
}

/** Inicia a sessão autenticada (renova o ID para evitar session fixation). */
function iniciarSessao(int $usuarioId): void
{
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuarioId;
    $_SESSION['filtro'] = 'todas';
}

/** Barra o acesso de quem não está logado: redireciona para login.php. */
function exigirLogin(): void
{
    if (usuarioLogado() === null) {
        redirecionar('login.php');
    }
}

/** Busca uma tarefa SOMENTE se pertencer ao usuário logado. */
function tarefaDoUsuario(int $id)
{
    $usuario = usuarioLogado();
    if ($id <= 0 || $usuario === null) {
        return null;
    }
    return R::findOne('tarefa', 'id = ? AND usuario_id = ?', [$id, (int) $usuario->id]);
}

// ---------- CSRF ----------

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo hidden para colocar dentro de todo <form method="post">. */
function csrfCampo(): string
{
    return '<input type="hidden" name="csrf" value="' . h(csrfToken()) . '">';
}

/** Encerra com erro 403 se o token do POST não for válido. */
function csrfValidar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!is_string($enviado) || !hash_equals(csrfToken(), $enviado)) {
        redirecionar('erro.php?tipo=csrf');
    }
}

/** Exige método POST; qualquer outro método volta para a lista. */
function exigirPost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        voltarParaIndex();
    }
}
