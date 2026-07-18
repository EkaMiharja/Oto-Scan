<?php
/**
 * ============================================
 * Footer Admin Panel (Soft UI Dashboard)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */
?>
        <?php if (isAdminLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
            <footer class="footer pt-3  ">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                © <?= date('Y') ?> Sistem Manajemen Kendaraan Perumahan
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        <?php endif; ?>
        <?php if (isset($isVR) && $isVR): ?>
                </div> <!-- End container-fluid -->
            </div> <!-- End section -->
        </main>
    </div> <!-- End vr background div -->
        <?php else: ?>
        </div> <!-- End container-fluid -->
    </main>
        <?php endif; ?>
    
    <!-- Core JS Files -->
    <script src="<?= BASE_URL ?>/assets/soft-ui/js/core/popper.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/soft-ui/js/core/bootstrap.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/soft-ui/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/soft-ui/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/soft-ui/js/plugins/chartjs.min.js"></script>
    
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    
    <!-- Control Center for Soft Dashboard -->
    <script src="<?= BASE_URL ?>/assets/soft-ui/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
    
    <?php if (isset($useDataTables) && $useDataTables): ?>
    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.datatable-exportable').DataTable({
                dom: '<"row mb-3 align-items-center"<"col-md-6"B><"col-md-6 text-end"f>>t',
                paging: false,
                info: false,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn bg-gradient-success btn-sm mb-0 me-2',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        title: 'Data_' + ($('h6').first().text().trim().replace(/\s+/g, '_') || 'Export')
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn bg-gradient-danger btn-sm mb-0',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        title: 'Data_' + ($('h6').first().text().trim().replace(/\s+/g, '_') || 'Export'),
                        customize: function (doc) {
                            doc.styles.tableHeader.fillColor = '#3A416F';
                            doc.styles.tableHeader.color = 'white';
                        }
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    infoFiltered: "(disaring dari _MAX_ total entri)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
    <?php endif; ?>
    
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
