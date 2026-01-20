// 
//  Utilities per pagina di login/registrazione
//

document.addEventListener("DOMContentLoaded", onLoad);

function onLoad() {
    const container = document.getElementById('container');
    const regbtn = document.getElementById('reg-btn');
    const loginbtn = document.getElementById('login-btn');
    function Tab_Index() {
        const isRegisterActive = container.classList.contains('active');
        const loginUsername = document.getElementById('login-username');
        const loginPassword = document.getElementById('login-password');
        const loginSubmit = document.getElementById('login-submit');

        const regEmail = document.getElementById('reg-email');
        const regUsername = document.getElementById('reg-username');
        const regPassword = document.getElementById('reg-password');
        const regSubmit = document.getElementById('reg-submit');
        if (isRegisterActive) {
            const elements = [
                { element: loginUsername, tabIndex: -1 },
                { element: loginPassword, tabIndex: -1 },
                { element: loginSubmit, tabIndex: -1 },
                { element: regEmail, tabIndex: 0 },
                { element: regUsername, tabIndex: 0 },
                { element: regPassword, tabIndex: 0 },
                { element: regSubmit, tabIndex: 0 },
                { element: regbtn, tabIndex: -1 }
            ];

            elements.forEach(({ element, tabIndex }) => {
                if (element) element.tabIndex = tabIndex;
            });

            if (loginbtn) {
                loginbtn.tabIndex = 0;
                loginbtn.disabled = false;
            }
        }
        else {
            const elements = [
                { element: regEmail, tabIndex: -1 },
                { element: regUsername, tabIndex: -1 },
                { element: regPassword, tabIndex: -1 },
                { element: regSubmit, tabIndex: -1 },
                { element: regEmail, tabIndex: 0 },
                { element: loginUsername, tabIndex: 0 },
                { element: loginPassword, tabIndex: 0 },
                { element: loginSubmit, tabIndex: 0 },
                { element: loginbtn, tabIndex: -1 }
            ];

            elements.forEach(({ element, tabIndex }) => {
                if (element) element.tabIndex = tabIndex;
            });

            if (regbtn) {
                regbtn.tabIndex = 0;
                regbtn.disabled = false;
            }
        }
    }
    Tab_Index();
    const errorMessages = document.getElementById('error-messages');
    if (errorMessages) {
        setTimeout(function () {
            if (container.classList.contains('active')) {
                const registerInput = document.getElementById('reg-email');
                if (registerInput) registerInput.focus();
            } else {
                const loginInput = document.getElementById('login-username');
                if (loginInput) loginInput.focus();
            }
        }, 100);
    }
    function messaggioScreenReader(message) {
        const ann = document.createElement('div');
        ann.setAttribute('aria-live', 'polite');
        ann.setAttribute('aria-atomic', 'true');
        ann.className = 'visually-hidden';
        ann.textContent = message;
        document.body.appendChild(ann);
        setTimeout(() => {
            document.body.removeChild(ann);
        }, 1000);
    }
    if (regbtn) {
        regbtn.addEventListener('click', function () {
            container.classList.add('active');
            Tab_Index();
            messaggioScreenReader('Ti trovi nel form per una nuova registrazione. Compila i campi per creare un account.');
            setTimeout(() => {
                const registerInput = document.getElementById('reg-email');
                if (registerInput) {
                    registerInput.focus();
                    registerInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 50);
        });
    }
    if (loginbtn) {
        loginbtn.addEventListener('click', function () {
            container.classList.remove('active');
            Tab_Index();
            messaggioScreenReader('Ti trovi nel form di accesso. Inserisci username e password per accedere.');
            setTimeout(() => {
                const loginInput = document.getElementById('login-username');
                if (loginInput) {
                    loginInput.focus();
                    loginInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 50);
        });
    }
    [regbtn, loginbtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab') return;
        const isRegisterActive = container.classList.contains('active');
        const activeElement = document.activeElement;
        if (isRegisterActive) {
            const focusableInRegister = [];
            const regEmail = document.getElementById('reg-email');
            const regUsername = document.getElementById('reg-username');
            const regPassword = document.getElementById('reg-password');
            const regSubmit = document.getElementById('reg-submit');
            if (regEmail && regEmail.tabIndex !== -1) focusableInRegister.push(regEmail);
            if (regUsername && regUsername.tabIndex !== -1) focusableInRegister.push(regUsername);
            if (regPassword && regPassword.tabIndex !== -1) focusableInRegister.push(regPassword);
            if (regSubmit && regSubmit.tabIndex !== -1) focusableInRegister.push(regSubmit);
            const loginCurtainBtn = document.getElementById('login-btn');
            const isLoginBtnFocusable = loginCurtainBtn && loginCurtainBtn.tabIndex !== -1;
            if (focusableInRegister.length > 0) {
                if (activeElement === focusableInRegister[focusableInRegister.length - 1] && !e.shiftKey) {
                    if (isLoginBtnFocusable) {
                        e.preventDefault();
                        loginCurtainBtn.focus();
                    }
                }
                if (activeElement === loginCurtainBtn && e.shiftKey && isLoginBtnFocusable) {
                    e.preventDefault();
                    focusableInRegister[focusableInRegister.length - 1].focus();
                }
            }
        } else {
            const focusableInLogin = [];
            const loginUsername = document.getElementById('login-username');
            const loginPassword = document.getElementById('login-password');
            const loginSubmit = document.getElementById('login-submit');
            if (loginUsername && loginUsername.tabIndex !== -1) focusableInLogin.push(loginUsername);
            if (loginPassword && loginPassword.tabIndex !== -1) focusableInLogin.push(loginPassword);
            if (loginSubmit && loginSubmit.tabIndex !== -1) focusableInLogin.push(loginSubmit);
            const registerCurtainBtn = document.getElementById('reg-btn');
            const isRegBtnFocusable = registerCurtainBtn && registerCurtainBtn.tabIndex !== -1;
            if (focusableInLogin.length > 0) {
                if (activeElement === focusableInLogin[focusableInLogin.length - 1] && !e.shiftKey) {
                    if (isRegBtnFocusable) {
                        e.preventDefault();
                        registerCurtainBtn.focus();
                    }
                }
                if (activeElement === registerCurtainBtn && e.shiftKey && isRegBtnFocusable) {
                    e.preventDefault();
                    focusableInLogin[focusableInLogin.length - 1].focus();
                }
            }
        }
    });
}

// 
// Utilities per form nuovo drink
// 
function addIngredient() {
    var list = document.getElementById('ingredients-list');
    var fieldId = list.childElementCount + 1;
    var item = document.createElement('li');
    item.innerHTML = '<div class="form-row">' +
        '<div class="form-group input-quantity">' +
        `<label for="ingredient-quanity-${fieldId}">Quantità</label>` +
        `<input type="text" class="ingredient-quantity" id="ingredient-quanity-${fieldId}" ` +
        ' name="ingredient-quantities[]" placeholder="es. 12oz" required>' +
        '</div>' +
        '<div class="form-group">' +
        `<label for="ingredient-name-${fieldId}">Nome</label>` +
        `<input type="text" class="ingredient-name" id="ingredient-name-${fieldId}" ` +
        ' name="ingredient-names[]" placeholder="es. Vodka" required>' +
        '</div>' +
        `<button type="button" class="btn-remove" onclick="removeIngredient(${fieldId})">` +
        `<img src="img/trash.svg" alt="Rimuovi ingrediente ${fieldId}">` +
        '</button>' +
        '</div>'
    list.appendChild(item);
}

function addStep() {
    var list = document.getElementById('steps-list');
    var fieldId = list.childElementCount + 1;
    var item = document.createElement('li');
    item.innerHTML = '<div class="row">' +
        '<div class="form-group">' +
        `<label for="preparation-${fieldId}">Procedimento</label>` +
        `<textarea class="preparation-step" id="preparation-${fieldId}" name="steps[]" ` +
        ` placeholder="Procedimento" required></textarea>` +
        '</div>' +
        `<button type="button" class="btn-remove" onclick="removeStep(${fieldId})">` +
        `<img src="img/trash.svg" alt="Rimuovi passo di preparazione ${fieldId}">` +
        '</button>' +
        '</div>'
    list.appendChild(item)
}

function removeIngredient(id) {
    var list = document.getElementById('ingredients-list');
    var el = document.getElementById('ingredient-name-' + id).parentNode.parentNode.parentNode;
    list.removeChild(el);
}

function removeStep(id) {
    var list = document.getElementById('steps-list');
    var el = document.getElementById('preparation-' + id).parentNode.parentNode.parentNode;
    list.removeChild(el);
}