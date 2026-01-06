
const container = document.querySelector('.container');
const regbtn = document.querySelector('.reg-btn');
const loginbtn = document.querySelector('.login-btn');

regbtn.addEventListener('click', function(){
    container.classList.add('active');
    const RegisterInput = document.querySelector('.register input');
    if (RegisterInput) {
        RegisterInput.focus();
    }
} );

loginbtn.addEventListener('click', function(){
    container.classList.remove('active');
    const LoginInput = document.querySelector('.login input');
    if (LoginInput) {
        LoginInput.focus();
    }
} );
