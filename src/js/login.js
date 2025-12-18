const container = document.querySelector('.container');
const regbtn = document.querySelector('.reg-btn');
const loginbtn = document.querySelector('.login-btn');

regbtn.addEventListener('click', function(){
    container.classList.add('active');
} )

loginbtn.addEventListener('click', function(){
    container.classList.remove('active');
} )