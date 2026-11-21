<?php
/**
 * Rodapé padrão — scripts e fechamento do HTML.
 */
?>
</div><!-- /container-fluid -->
</div><!-- /page-wrapper -->

<!-- ==================== FOOTER ========================= -->
<footer class="footer-bar mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
            <span class="text-muted small">
                &copy; <?= date('Y') ?> <strong>LogiSystem</strong> &mdash;
                APO &middot; Fundamentos de Programação para Internet &middot; UNIPAR EAD
            </span>
            <span class="text-muted small">
                <i class="bi bi-shield-lock-fill text-accent me-1"></i>
                Dados protegidos &middot; PDO + Prepared Statements
            </span>
        </div>
    </div>
</footer>
<!-- =================== /FOOTER ========================= -->

<!-- Bootstrap 5 Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= $navRoot ?>js/main.js"></script>

<script>
// Auto-hide flash toast after 4s
document.addEventListener('DOMContentLoaded', function () {
    const flashEl = document.getElementById('flashToast');
    if (flashEl) {
        setTimeout(() => {
            const toast = bootstrap.Toast.getOrCreateInstance(flashEl);
            toast.hide();
        }, 4000);
    }
});
</script>
</body>
</html>
