document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-toggle");
  const themeIcon = document.getElementById("theme-icon");
  const body = document.body;

  if (!toggleBtn || !themeIcon) return;

  const iconaSole = "img/sole.svg";
  const iconaLuna = "img/luna.svg";

  const apply = (mode) => {
    const isLight = mode === "light";
    body.classList.toggle("light-mode", isLight);
    localStorage.setItem("theme", isLight ? "light" : "dark");

    themeIcon.src = isLight ? iconaLuna : iconaSole;
    themeIcon.alt = isLight ? "Passa alla modalità scura" : "Passa alla modalità chiara";
  };

  const saved = localStorage.getItem("theme");
  apply(saved === "light" ? "light" : "dark");

  toggleBtn.addEventListener("click", () => {
    const isLightNow = body.classList.contains("light-mode");
    apply(isLightNow ? "dark" : "light");
  });
});
