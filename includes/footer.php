<?php
/**
 * FOOTER - Pie de página común para todas las páginas
 * Cierra el container principal y carga los scripts de Bootstrap.
 */
?>
</div> <!-- Cierre del container-fluid del header -->

<!-- ========== FOOTER ========== -->
<footer class="bg-dark text-center text-white py-3 mt-5 border-top border-secondary">
    <small>
        <i class="bi bi-controller"></i> RLCS CRM &copy; <?= date('Y') ?> - Gestión de Rocket League Championship Series
    </small>
</footer>

<!-- Bootstrap 5 JS (Bundle incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Inicializar y mostrar todos los toasts pendientes
document.querySelectorAll('.toast').forEach(function(el) {
    new bootstrap.Toast(el).show();
});
// Skeleton loading en formularios de filtro
document.querySelectorAll('form.form-filter').forEach(function(form) {
    form.addEventListener('submit', function() {
        var tbody = document.querySelector('table tbody');
        if (!tbody) return;
        var cols = tbody.querySelector('tr') ? tbody.querySelector('tr').cells.length : 4;
        var html = '';
        for (var i = 0; i < 5; i++) {
            html += '<tr class="skeleton-row">';
            for (var c = 0; c < cols; c++) html += '<td>&nbsp;</td>';
            html += '</tr>';
        }
        tbody.innerHTML = html;
    });
});
</script>

</body>
</html>
