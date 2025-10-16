// Validação do formulário de login
const loginForm = document.getElementById('login-form');

if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        const usuario = document.getElementById('usuario').value.trim();
        const senha = document.getElementById('senha').value.trim();
        const acesso = document.getElementById('acesso');

        // Verifica se algum dos campos está vazio
        if (!usuario || !senha) {
            e.preventDefault();

            if (acesso) {
                acesso.textContent = 'Por favor, preencha todos os campos.';
                acesso.style.color = 'red';
            }

            // Foca no primeiro campo vazio
            if (!usuario) {
                document.getElementById('usuario').focus();
            } else {
                document.getElementById('senha').focus();
            }
        }
    });

    // Remove a mensagem de erro quando o usuário começa a digitar
    const inputs = loginForm.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach((input) => {
        input.addEventListener('input', function () {
            const acesso = document.getElementById('acesso');
            if (acesso && acesso.textContent === 'Por favor, preencha todos os campos.') {
                acesso.textContent = '';
            }
        });
    });
}
