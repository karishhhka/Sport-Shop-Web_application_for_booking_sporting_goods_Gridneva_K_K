const openRegModal = document.getElementById('open-reg-modal');
const regModal = document.getElementById('reg-modal');
const openLoginModal = document.getElementById('open-login-modal');
const loginModal = document.getElementById('login-modal');
const closeMod = document.getElementById('close');
const closeModTwo = document.getElementById('closeTwo');
const openInreg = document.getElementById('open-inReg-modal');
const regForm = document.querySelector('.modal_form');
const errorContainer = document.getElementById('error-container');
const loginForm = document.getElementById('login-form');
const authErrorContainer = document.getElementById('auth-error-container');
const qrCodeSection = document.getElementById('qr-code-section');
const qrCodeImg = document.getElementById('qr-code');
const verify2faBtn = document.getElementById('verify-2fa-btn');
const codeInput = document.getElementById('2fa-code');

function openModal(modal) {
    modal.style.display = 'block';
}

function closeModal(modal) {
    modal.style.display = 'none';
}

openRegModal.addEventListener('click', () => openModal(regModal));
openLoginModal.addEventListener('click', () => {
    openModal(loginModal);
    closeModal(regModal);
});
openInreg.addEventListener('click', () => {
    openModal(regModal);
    closeModal(loginModal);
});

closeMod.addEventListener('click', () => {
    closeModal(regModal);
    errorContainer.innerHTML = '';
});

closeModTwo.addEventListener('click', () => {
    closeModal(loginModal);
    authErrorContainer.innerHTML = '';
    qrCodeSection.style.display = 'none';
    loginForm.style.display = 'block';
});

window.addEventListener('click', (event) => {
    if (event.target === regModal) {
        closeModal(regModal);
        errorContainer.innerHTML = '';
    }
    if (event.target === loginModal) {
        closeModal(loginModal);
        authErrorContainer.innerHTML = '';
        qrCodeSection.style.display = 'none';
        loginForm.style.display = 'block';
    }
});

// Обработчик регистрации
regForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    errorContainer.innerHTML = '';
    const formData = new FormData(regForm);

    try {
        const response = await fetch('/registration.php', {
            method: 'POST',
            body: formData
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Ожидался JSON, но получили:', text);
            throw new Error('Сервер вернул невалидный ответ');
        }

        const result = await response.json();
        
        if (result.success) {
            alert('Регистрация успешна!');
            closeModal(regModal);
        } else {
            errorContainer.innerHTML = result.errors?.map(error => `<p>${error}</p>`).join('') || '<p>Ошибка регистрации</p>';
        }
    } catch (error) {
        console.error('Ошибка:', error);
        errorContainer.innerHTML = '<p>Произошла ошибка при отправке данных. Проверьте консоль для подробностей.</p>';
    }
});


// Обработчик авторизации с 2FA
loginForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    authErrorContainer.innerHTML = '';
    
    const formData = new FormData(loginForm);

    try {
        const response = await fetch('/login.php', {
            method: 'POST',
            body: formData
        });

        const contentType = response.headers.get("content-type");

        if (!contentType || !contentType.includes("application/json")) {
            const text = await response.text();
            console.error("Получен не JSON:", text);
            throw new Error("Сервер вернул не JSON");
        }

        const result = await response.json();

        if (result.requires_2fa) {
            qrCodeImg.src = result.qr_code;
            qrCodeSection.style.display = 'block';
            loginForm.style.display = 'none';
        } else if (result.success) {
            window.location.href = result.redirect || '/persaccount/profile.php';
        } else {
            authErrorContainer.innerHTML = result.errors.map(error => `<p>${error}</p>`).join('');
        }
    } catch (error) {
        console.error("Ошибка авторизации:", error);
        authErrorContainer.innerHTML = `<p>Ошибка: ${error.message}</p>`;
    }
});

// Обработчик подтверждения 2FA кода
verify2faBtn?.addEventListener('click', async () => {
    const code = codeInput.value.trim();
    
    if (!code || code.length !== 6 || !/^\d+$/.test(code)) {
        authErrorContainer.innerHTML = '<p>Введите корректный 6-значный код</p>';
        return;
    }

    try {
        const response = await fetch('/verify_2fa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'code=' + encodeURIComponent(code)
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Ожидался JSON, но получили:', text);
            throw new Error('Сервер вернул невалидный ответ');
        }

        const result = await response.json();

        if (result.success && result.redirect) {
            window.location.href = result.redirect;
        } else {
            authErrorContainer.innerHTML = `<p>${result.error || 'Неверный код'}</p>`;
        }
    } catch (err) {
        console.error('Ошибка:', err);
        authErrorContainer.innerHTML = '<p>Произошла ошибка при проверке кода</p>';
    }
});