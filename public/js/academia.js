// Cambiar rol de Alumno a Entrenador o Gerente

function toggleRoleSwitch() {
    if (currentRole === "Alumno") {
        currentRole = "Entrenador";
        document.getElementById("labelEntrenador").innerText = "Entrenador";
        document.getElementById("switchEntrenador").checked = true;
    } else {
        currentRole = "Alumno";
        document.getElementById("labelEntrenador").innerText = "Alumno";
        document.getElementById("switchEntrenador").checked = false;
    }
}

function toggleRoleSwitch2() {
    if (currentRole === "Alumno") {
        currentRole = "Gerente";
        document.getElementById("labelGerente").innerText = "Gerente";
        document.getElementById("switchGerente").checked = true;
    } else {
        currentRole = "Alumno";
        document.getElementById("labelGerente").innerText = "Alumno";
        document.getElementById("switchGerente").checked = false;
    }
}