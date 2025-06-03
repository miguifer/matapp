
import { RUTA_IMG_ACADEMIAS } from "./variables.js";


const searchInput = document.getElementById("searchInput");
const resultadosContainer = document.getElementById("resultados");


// Mostrar lista de gimnasios por coincidencia en la búsqueda
function mostrarResultados() {
    const query = searchInput.value.toLowerCase();
    resultadosContainer.innerHTML = "";

    const coincidencias = gimnasios.filter(gimnasio =>
        gimnasio.nombreAcademia.toLowerCase().includes(query)
    );

    if (query === "") {
        return;
    }

    if (coincidencias.length > 0) {
        coincidencias.forEach(gimnasio => {
            const div = document.createElement("div");
            div.classList.add("resultado", "p-2");
            const img = document.createElement("img");
            img.classList.add("me-2");
            img.src = (gimnasio.path_imagen && gimnasio.path_imagen.trim() !== "" && gimnasio.path_imagen !== "undefined" && gimnasio.path_imagen !== null)
                ? RUTA_IMG_ACADEMIAS + gimnasio.path_imagen
                : defecto;
            img.alt = gimnasio.nombreAcademia;
            img.style.width = "30px";
            img.style.height = "30px";
            img.classList.add("rounded-5");

            div.appendChild(img);

            div.title = "Ir a " + gimnasio.nombreAcademia;

            const span = document.createElement("span");
            span.textContent = gimnasio.nombreAcademia;
            div.appendChild(span);

            div.style.cursor = "pointer";
            div.style.transition = "background-color 150ms";

            div.addEventListener("mouseover", () => {
                div.style.backgroundColor = "#505050";
            });

            div.addEventListener("mouseout", () => {
                div.style.backgroundColor = "";
            });

            div.addEventListener("click", () => {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = `${RUTA_ACADEMIA}` + "?academia=" + encodeURIComponent(JSON.stringify(gimnasio));

                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "academia_id";
                input.value = gimnasio.idAcademia;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            });

            resultadosContainer.appendChild(div);
        });
    } else {
        resultadosContainer.innerHTML =
            "<p class=\"text-danger small\">No se encontraron resultados</p>";
    }
}

searchInput.addEventListener("input", mostrarResultados);

//Mostrar popup de toastr si existe un mensaje
if (typeof toastrMsg !== "undefined" && toastrMsg) {
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": "10000",
        "progressBar": true,
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    toastr.info(toastrMsg);
}