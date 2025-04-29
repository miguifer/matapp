// Respuestas predefinidas (funcionalidad del primer chatbot)
const responses = {
    hola: "¡Hola! Soy el asistente de MatApp. ¿En qué puedo ayudarte hoy?",
    matapp: "MatApp es una plataforma diseñada para ayudarte a mejorar tus habilidades en matemáticas, con herramientas interactivas y recursos educativos.",
    funciona: "MatApp funciona proporcionando ejercicios prácticos, explicaciones detalladas y recursos interactivos para aprender matemáticas de manera divertida y eficaz.",
    ayudar: "Puedo ayudarte a resolver problemas matemáticos, mejorar tus conocimientos en álgebra, geometría, cálculo y más. Además, te ayudo a prepararte para exámenes o tareas.",
    materias: "MatApp cubre una amplia gama de materias matemáticas, desde aritmética básica hasta cálculo avanzado, álgebra, geometría, trigonometría, y más.",
    cuenta: "Para crear una cuenta en MatApp, solo tienes que ir a la página de registro, llenar tus datos y listo. Así podrás guardar tu progreso y acceder a más recursos personalizados.",
    gratis: "MatApp ofrece una versión gratuita con acceso a muchos recursos, pero también tiene una opción premium con funciones adicionales y contenido exclusivo.",
    ejercicios: "Puedes hacer ejercicios de diferentes tipos: elección múltiple, verdadero o falso, problemas prácticos, y más, dependiendo de la materia y el nivel que elijas.",
    exámenes: "MatApp ofrece pruebas y exámenes para que puedas evaluar tu conocimiento en diferentes temas matemáticos y prepararte para exámenes reales.",
    progreso: "MatApp te permite seguir tu progreso a medida que completas ejercicios y superas pruebas. Puedes ver tus logros y áreas de mejora.",
    idiomas: "Actualmente, MatApp está disponible en varios idiomas, como inglés, español y francés. Puedes cambiar el idioma desde la configuración.",
    soporte: "Si necesitas ayuda, puedes ponerte en contacto con nuestro soporte a través del formulario de contacto en la web o enviarnos un correo a soporte@matapp.com.",
    adiós: "¡Hasta luego! Si tienes más dudas sobre MatApp, no dudes en volver. ¡Sigue aprendiendo!",
    niveles: "MatApp ofrece ejercicios y recursos para todos los niveles: desde primaria hasta universidad.",
    certificados: "Al completar ciertos cursos o exámenes en MatApp, puedes obtener certificados digitales que acreditan tu aprendizaje.",
    personalización: "La plataforma adapta los ejercicios y recomendaciones según tu progreso y tus intereses.",
    comunidad: "Puedes participar en foros y grupos de estudio dentro de MatApp para aprender junto a otros usuarios.",
    appmovil: "MatApp está disponible como aplicación móvil para Android y iOS. ¡Aprende desde cualquier lugar!",
    privacidad: "Tus datos están protegidos y solo se usan para mejorar tu experiencia en MatApp.",
    recomendaciones: "Recibirás recomendaciones personalizadas de ejercicios y recursos según tus resultados.",
    historial: "Puedes revisar tu historial de ejercicios y ver en qué temas necesitas mejorar.",
    gamificación: "Gana puntos, medallas y recompensas a medida que avanzas en tu aprendizaje.",
    profesores: "MatApp cuenta con recursos y herramientas especiales para profesores y tutores.",
    integración: "Puedes integrar MatApp con otras plataformas educativas y exportar tus resultados.",
    dudas: "Si tienes dudas sobre algún tema, puedes preguntarme o consultar las explicaciones detalladas en cada sección.",
    recursos: "Además de ejercicios, MatApp ofrece videos, guías, y juegos interactivos para reforzar tu aprendizaje.",
    acceso: "Puedes acceder a MatApp desde cualquier dispositivo con conexión a internet.",
    recomendacioneslibros: "MatApp te sugiere libros y materiales adicionales para profundizar en los temas que te interesan.",
    actualizaciones: "La plataforma se actualiza constantemente con nuevos contenidos y funcionalidades."
};

// Función que busca la mejor respuesta según la entrada del usuario
function findBestResponse(userInput) {
    userInput = userInput.toLowerCase();
    for (let key in responses) {
        if (userInput.includes(key)) {
            return responses[key];
        }
    }
    return "Lo siento, no entiendo esa pregunta. Intenta otra.";
}

// Crea un elemento de chat (li) según el tipo: "user" para mensajes del usuario y "bot" para respuestas
const createChatLi = (message, type) => {
    const chatLi = document.createElement("li");
    chatLi.classList.add("chat", type === "user" ? "outgoing" : "incoming");
    if (type === "bot") {
        chatLi.innerHTML = '<span class="material-symbols-outlined">smart_toy</span><p>' + message + '</p>';
    } else {
        chatLi.innerHTML = "<p>" + message + "</p>";
    }
    return chatLi;
};

// Selección de elementos
const chatInput = document.getElementById("user-input");
const sendChatBtn = document.getElementById("send-btn");
const chatbox = document.getElementById("chatbox");
const chatbotToggler = document.querySelector(".chatbot-toggler");
const chatbotCloseBtn = document.querySelector(".close-btn");

let userMessage = "";
const inputInitHeight = chatInput.scrollHeight;

// Función para manejar el envío de mensajes
const handleChat = () => {
    userMessage = chatInput.value.trim();
    if (!userMessage) return;

    // Agrega el mensaje del usuario
    chatbox.appendChild(createChatLi(userMessage, "user"));
    chatbox.scrollTop = chatbox.scrollHeight;
    chatInput.value = "";
    // Reinicia la altura usando un valor fijo
    chatInput.style.height = "55px";

    // Agrega un mensaje de "Pensando..." y luego actualiza con la respuesta
    const incomingChatLi = createChatLi("Pensando...", "bot");
    chatbox.appendChild(incomingChatLi);
    chatbox.scrollTop = chatbox.scrollHeight;

    setTimeout(() => {
        const response = findBestResponse(userMessage);
        incomingChatLi.querySelector("p").textContent = response;
        chatbox.scrollTop = chatbox.scrollHeight;
    }, 500);
};


// Ajuste automático del textarea
chatInput.addEventListener("input", () => {
    chatInput.style.height = "auto"; // Reinicia la altura para recalcularla
    chatInput.style.height = chatInput.scrollHeight + "px";
});


// Enviar mensaje al presionar Enter (sin Shift)
chatInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        handleChat();
    }
});

// Enviar mensaje al hacer clic en el botón de enviar
sendChatBtn.addEventListener("click", handleChat);

// Mostrar/Ocultar el chatbot al hacer clic en el toggler
chatbotToggler.addEventListener("click", function () {
    const isShown = document.body.classList.toggle("show-chatbot");
    if (isShown) {
        chatbotToggler.classList.add("show-chatbot");
    } else {
        chatbotToggler.classList.remove("show-chatbot");
    }
});

// Cerrar el chatbot al hacer clic en el botón de cerrar
chatbotCloseBtn.addEventListener("click", () =>
    document.body.classList.remove("show-chatbot")
);
