document.addEventListener("DOMContentLoaded", () => {
    const navBtn = document.querySelector("header .nav-button");
    const nav = document.querySelector("header nav");
    navBtn.addEventListener("click", ()=>{
        nav.classList.toggle("nav-visible");
    });
});
