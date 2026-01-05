
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

const adminBtn = document.getElementById("admin-btn");
const adminForm = document.querySelector(".admin-form");

adminBtn.addEventListener("click", (event) => {
    event.stopPropagation();
    adminForm.style.display = adminForm.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", (event) => {
if (adminForm.style.display !== "block") return;
if (event.target.closest(".admin-area")) return;
adminForm.style.display = "none";
});
