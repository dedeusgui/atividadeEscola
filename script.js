// Adiciona um listener ao formulário de login
document.getElementById('login-form').addEventListener('submit', function (e) {
    // Pega os valores dos campos de usuário e senha
    const usuario = document.getElementById('usuario').value.trim();
    const senha = document.getElementById('senha').value.trim();
    const acesso = document.getElementById('acesso');

    // Verifica se algum dos campos está vazio
    if (!usuario || !senha) {
        // Se estiverem vazios, impede o envio do formulário
        e.preventDefault();

        // Mostra uma mensagem de erro imediata para o usuário
        acesso.textContent = 'Por favor, preencha todos os campos.';
    }
    // Se os campos estiverem preenchidos, o formulário será enviado normalmente para o PHP.
});
