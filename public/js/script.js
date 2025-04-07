document.addEventListener("DOMContentLoaded", function () {
    const newElement = document.createElement("div");
    newElement.className = "rojo";
    newElement.textContent = "Este es un elemento rojo!";

    const main = document.getElementById("main");
    if (main) {
        main.appendChild(newElement);
    }
});
