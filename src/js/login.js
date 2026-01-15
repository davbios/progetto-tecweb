const container = document.querySelector('.container');
const regbtn = document.querySelector('.reg-btn');
const loginbtn = document.querySelector('.login-btn');


function Tab_Index() {
    const isRegisterActive = container.classList.contains('active');
    if (isRegisterActive) {
        document.querySelectorAll('.login input, .login button').forEach(el => {
            el.setAttribute('tabindex', '-1');
        });
        document.querySelectorAll('.register input, .register button').forEach(el => {
            el.removeAttribute('tabindex');
        });
        regbtn.setAttribute('tabindex', '-1');
        loginbtn.removeAttribute('tabindex');
        loginbtn.removeAttribute('disabled');
    } 
    else {
        document.querySelectorAll('.register input, .register button').forEach(el => {
            el.setAttribute('tabindex', '-1');
        });
        document.querySelectorAll('.login input, .login button').forEach(el => {
            el.removeAttribute('tabindex');
        });
        loginbtn.setAttribute('tabindex', '-1');
        regbtn.removeAttribute('tabindex');
        regbtn.removeAttribute('disabled');
    }
}

window.addEventListener('DOMContentLoaded', function() {
    Tab_Index();

    const errorMessages = document.querySelector('.error-messages');
    if (errorMessages) {
        setTimeout(function() {
            if (container.classList.contains('active')) {
                const registerInput = document.querySelector('#reg-email');
                if (registerInput) registerInput.focus();
            } else {
                const loginInput = document.querySelector('#login-username');
                if (loginInput) loginInput.focus();
            }
        }, 100);
    }
});

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

regbtn.addEventListener('click', function(){
    container.classList.add('active');
    Tab_Index();
    messaggioScreenReader('Ti trovi nel form per una nuova registrazione. Compila i campi per creare un account.');
    setTimeout(() => {
        const registerInput = document.querySelector('#reg-email');
        if (registerInput) {
            registerInput.focus();
            registerInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 50);
});

loginbtn.addEventListener('click', function(){
    container.classList.remove('active');
    Tab_Index();
    messaggioScreenReader('Ti trovi nel form di accesso. Inserisci username e password per accedere.');
    setTimeout(() => {
        const loginInput = document.querySelector('#login-username');
        if (loginInput) {
            loginInput.focus();
            loginInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 50);
});

[regbtn, loginbtn].forEach(btn => {
    btn.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
});


document.addEventListener('keydown', function(e) {
    if (e.key !== 'Tab') return;
    
    const isRegisterActive = container.classList.contains('active');
    const activeElement = document.activeElement;
    
    if (isRegisterActive) {
        const focusableInRegister = Array.from(
            document.querySelectorAll('.register input, .register button[type="submit"]')
        ).filter(el => el.getAttribute('tabindex') !== '-1');
        const loginCurtainBtn = document.querySelector('.login-btn');
        const isLoginBtnFocusable = loginCurtainBtn && loginCurtainBtn.getAttribute('tabindex') !== '-1';
        if (activeElement === focusableInRegister[focusableInRegister.length - 1] && !e.shiftKey) {
            if (isLoginBtnFocusable) {
                e.preventDefault();
                loginCurtainBtn.focus();
            } else {
                e.preventDefault();
                focusableInRegister[0].focus();
            }
        }
        if (activeElement === loginCurtainBtn && e.shiftKey && isLoginBtnFocusable) {
            e.preventDefault();
            focusableInRegister[focusableInRegister.length - 1].focus();
        }
    } else {
        const focusableInLogin = Array.from(
            document.querySelectorAll('.login input, .login button[type="submit"]')
        ).filter(el => el.getAttribute('tabindex') !== '-1');
        const registerCurtainBtn = document.querySelector('.reg-btn');
        const isRegBtnFocusable = registerCurtainBtn && registerCurtainBtn.getAttribute('tabindex') !== '-1';
        if (activeElement === focusableInLogin[focusableInLogin.length - 1] && !e.shiftKey) {
            if (isRegBtnFocusable) {
                e.preventDefault();
                registerCurtainBtn.focus();
            } else {
                e.preventDefault();
                focusableInLogin[0].focus();
            }
        }
        if (activeElement === registerCurtainBtn && e.shiftKey && isRegBtnFocusable) {
            e.preventDefault();
            focusableInLogin[focusableInLogin.length - 1].focus();
        }
    }
});