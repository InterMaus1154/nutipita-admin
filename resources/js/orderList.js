/*actions on order list*/
document.addEventListener("DOMContentLoaded", () => {
    // action buttons menu
    const openActionMenuButtons = document.querySelectorAll(".openActionMenuButton");
    if (openActionMenuButtons.length === 0) return;
    openActionMenuButtons.forEach(button => {
        button.addEventListener("click", () => {
            const orderId = button.getAttribute('data-order-id');
            const linkBox = document.querySelector(`div.action[data-order-id='${orderId}']`);
            console.log(linkBox);
            if (linkBox.classList.contains('hidden')) {
                linkBox.classList.remove("hidden");
                linkBox.classList.add("flex");
            } else {
                linkBox.classList.add('hidden');
                linkBox.classList.remove("flex");
            }
        });
    });

    document.addEventListener("click", event => {
        document.querySelectorAll("div.action.flex[data-order-id]").forEach(menu => {
            if (!menu.contains(event.target) && !event.target.classList.contains("openActionMenuButton")) {
                menu.classList.remove("flex");
                menu.classList.add("hidden");
            }
        });
    });

    const openExtraInfoButtons = document.querySelectorAll(".openExtraInfoButton");
    openExtraInfoButtons.forEach(button => {
        button.addEventListener("click", () => {
            const orderId = button.getAttribute('data-order-id');
            const infoBox = document.querySelector(`#extra-info-${orderId}`);
            if (infoBox.classList.contains('hidden')) {
                infoBox.classList.remove("hidden");
                infoBox.classList.add("flex");
            } else {
                infoBox.classList.add('hidden');
                infoBox.classList.remove("flex");
            }
        });
    })
})
