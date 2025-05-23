@include('includes.header')

<link rel="stylesheet" href="<?= RUTA_URL ?>/public/css/home.css" type="text/css" />
<link rel="stylesheet/less" type="text/css" href="<?= RUTA_URL ?>/public/css/styles/matapp-inicio.less" />

<div class="search-container" style="margin-top: 25vh">
    <h4 class="">Encuentra tu academia</h4>
    <div class="search-bar d-flex">
        <input class="w-100 p-2" type="text" title="Busca por nombre de academia" id="searchInput"
            placeholder="Buscar..." autocomplete="off" />
        <img src="<?= RUTA_URL ?>/public/img/home/lupita.svg" alt="lupa de buscar" />
    </div>
    <span class="input-subtitle">Empieza a escribir para encontrar tu academia</span><br />
    <div id="resultados"></div>
    <a href="<?= RUTA_URL ?>/crearAcademia" class="small text-decoration-none" id="link-crear-academia">¿No puedes
        encontrar tu
        academia? ¡Crea una!</a>
</div>
</div>

<footer class="footer-home text-center py-2 mt-5" style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 100;">
    <div class="container">
        <span class="text-muted">&copy; {{ date('Y') }} {{ NOMBRE_SITIO }}. Todos los derechos reservados.</span>
    </div>
</footer>

<?php if (isset($_GET['toastrErr'])): ?>
<script>
    window.toastrMsg = '<?= $_GET['toastrErr'] ?>';
</script>
<?php endif; ?>

<script>
    window.RUTA_LOGO_DEFECTO = "<?= RUTA_URL ?>/public/img/home/logo.png";
    window.RUTA_ACADEMIA = "<?= RUTA_URL ?>/academia";
    window.GIMNASIOS_DATA = [
        <?php foreach ($academias as $academia): ?>
        {
            <?php if ($academia->idAcademia !== null): ?>
            idAcademia: <?= $academia->idAcademia ?>,
            <?php endif; ?>
            <?php if ($academia->nombreAcademia !== null): ?>
            nombreAcademia: "<?= $academia->nombreAcademia ?>",
            <?php endif; ?>
            <?php if ($academia->ubicacionAcademia !== null): ?>
            ubicacionAcademia: "<?= $academia->ubicacionAcademia ?>",
            <?php endif; ?>
            <?php if ($academia->tipoAcademia !== null): ?>
            tipoAcademia: "<?= $academia->tipoAcademia ?>",
            <?php endif; ?>
            <?php if ($academia->idGerente !== null): ?>
            idGerente: <?= $academia->idGerente ?>,
            <?php endif; ?>
            <?php if ($academia->path_imagen !== null): ?>
            path_imagen: "<?= RUTA_IMG_ACADEMIAS . $academia->path_imagen ?>",
            <?php endif; ?>
            <?php if ($academia->latitud !== null): ?>
            latitud: <?= $academia->latitud ?>,
            <?php endif; ?>
            <?php if ($academia->longitud !== null): ?>
            longitud: <?= $academia->longitud ?>,
            <?php endif; ?>
        },
        <?php endforeach; ?>
    ];
</script>

<script src="<?= RUTA_URL ?>/public/js/home.js"></script>


@include('includes.footer')
