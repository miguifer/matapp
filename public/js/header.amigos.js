import './variables.js';
import { RUTA_URL } from './variables.js';

$(function() {
    // Llama a cargarSolicitudes al cargar la página para actualizar la badge del botón
    cargarSolicitudes();

    // Buscar usuarios
    $('#buscarUsuarioInput').on('input', function() {
        let query = $(this).val();
        if (query.length < 2) {
            $('#resultadosBusqueda').empty();
            return;
        }
        $.get(`${RUTA_URL}` + '/amigos/buscar', {
            q: query
        }, function(res) {
            let html = '';
            res.forEach(u => {
                let btn;
                if (u.estado === 'aceptada') {
                    btn = `<button class="btn btn-sm btn-secondary" disabled>Ya es tu amigo</button>`;
                } else if (u.estado === 'pendiente') {
                    btn = `<button class="btn btn-sm btn-warning" disabled>Solicitud pendiente</button>`;
                } else {
                    btn = `<button class="btn btn-sm btn-primary solicitar-amistad" data-id="${u.idUsuario}">Solicitar amistad</button>`;
                }
                // Imagen de perfil
                let imgSrc = u.imagen ? `data:image/jpeg;base64,${u.imagen}` :
                    `${RUTA_URL}` + '/public/img/default_profile.png';
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center">
                        <img src="${imgSrc}" alt="Perfil" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                        ${u.login}
                    </span>
                    ${btn}
                </li>`;
            });
            $('#resultadosBusqueda').html(html);
        }, 'json');
    });

    // Cargar amigos
    function cargarAmigos() {
        $.get(`${RUTA_URL}` + '/amigos/lista', function(res) {
            let html = '';
            if (res.length === 0) html = '<li class="list-group-item text-muted">Sin amigos</li>';
            res.forEach(a => {
                let imgSrc = a.imagen ? `data:image/jpeg;base64,${a.imagen}` :
                    `${RUTA_URL}` + '/public/img/default_profile.png';
                let onlineBadge = a.online == 1 ?
                    '<span class="badge bg-success ms-2">Online</span>' :
                    '<span class="badge bg-secondary ms-2">Offline</span>';
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center">
                        <img src="${imgSrc}" alt="Perfil" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                        ${a.login} ${onlineBadge}
                    </span>
                    <span>
                        <button class="btn btn-info btn-sm ver-perfil-amigo me-2" data-id="${a.idUsuario}">Ver perfil</button>
                        <button class="btn btn-danger btn-sm eliminar-amigo" data-id="${a.idUsuario}">Eliminar</button>
                    </span>
                </li>`;
            });
            $('#listaAmigos').html(html);
        }, 'json');
    }

    // Cargar solicitudes
    function cargarSolicitudes() {
        $.get(`${RUTA_URL}` + '/amigos/solicitudes', function(res) {
            let html = '';
            if (res.length === 0) {
                html = '<li class="list-group-item text-muted">Sin solicitudes</li>';
                $('#badgeSolicitudes').hide();
                $('#badgeSolicitudesBtn').hide();
            } else {
                $('#badgeSolicitudes').text(res.length).show();
                $('#badgeSolicitudesBtn').text(res.length).show();
            }
            res.forEach(s => {
                let imgSrc = s.imagen ? `data:image/jpeg;base64,${s.imagen}` :
                    `${RUTA_URL}` + '/public/img/default_profile.png';
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center">
                        <img src="${imgSrc}" alt="Perfil" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                        ${s.login}
                    </span>
                    <span>
                        <button class="btn btn-success btn-sm aceptar-solicitud" data-id="${s.id}">Aceptar</button>
                        <button class="btn btn-danger btn-sm rechazar-solicitud" data-id="${s.id}">Rechazar</button>
                    </span>
                </li>`;
            });
            $('#listaSolicitudes').html(html);
        }, 'json');
    }

    // Aceptar/rechazar solicitud
    $('#listaSolicitudes').on('click', '.aceptar-solicitud', function() {
        let id = $(this).data('id');
        $.post(`${RUTA_URL}` + '/amigos/aceptar', {
            id: id
        }, function(res) {
            toastr.success(res.message || 'Solicitud aceptada');
            cargarSolicitudes();
            cargarAmigos();
        }, 'json');
    });
    $('#listaSolicitudes').on('click', '.rechazar-solicitud', function() {
        let id = $(this).data('id');
        $.post(`${RUTA_URL}` + '/amigos/rechazar', {
            id: id
        }, function(res) {
            toastr.info(res.message || 'Solicitud rechazada');
            cargarSolicitudes();
        }, 'json');
    });

    // Eliminar amigo
    $('#listaAmigos').on('click', '.eliminar-amigo', function() {
        let id = $(this).data('id');
        $.post(`${RUTA_URL}` + '/amigos/eliminar', {
            id: id
        }, function(res) {
            toastr.info(res.message || 'Amistad eliminada');
            cargarAmigos();
        }, 'json');
    });

    // Solicitar amistad
    $('#resultadosBusqueda').on('click', '.solicitar-amistad', function() {
        let idUsuario2 = $(this).data('id');
        $.post(`${RUTA_URL}` + '/amigos/solicitar', {
            idUsuario2: idUsuario2
        }, function(res) {
            toastr.success(res.message || 'Solicitud enviada');
            $('#buscarUsuarioInput').trigger('input'); // Refresca la búsqueda
            cargarSolicitudes();
        }, 'json');
    });

    // Cargar listas al abrir el offcanvas
    $('#offcanvasAmigos').on('shown.bs.offcanvas', function() {
        cargarAmigos();
        cargarSolicitudes();
    });

    $('#listaAmigos').on('click', '.ver-perfil-amigo', function() {
        let id = $(this).data('id');
        $.get(`${RUTA_URL}` + '/amigos/perfil', {
            id: id
        }, function(res) {
            let imgSrc = res.imagen ? `data:image/jpeg;base64,${res.imagen}` :
                `${RUTA_URL}` + '/public/img/default_profile.png';
            let html = `
                <div class="text-center mb-3">
                    <img src="${imgSrc}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                </div>
                <p><strong>Usuario:</strong> ${res.login}</p>
                <p><strong>Nombre:</strong> ${res.nombreUsuario || ''}</p>
                <p><strong>Email:</strong> ${res.emailUsuario || ''}</p>
                <!-- Agrega más campos si lo deseas -->
            `;
            $('#perfilAmigoContenido').html(html);
            $('#modalPerfilAmigo').modal('show');
        }, 'json');
    });
});