</div> <!-- Fecha a div class="main-content" -->
    
    <footer class="text-center py-3 footer-main" style="background-color: hsl(var(--card)); border-top: 1px solid hsl(var(--border));"> 
        <p class="text-muted-foreground">© 2025 Sistema Dashboard | Todos os direitos reservados.</p>
    </footer>

</div> <!-- Fecha a div class="layout-wrapper" -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const body = document.body;
            const layoutWrapper = document.querySelector('.layout-wrapper');

            // 1. Abre/Fecha o menu ao clicar no hamburger
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    body.classList.toggle('sidebar-open');
                    layoutWrapper.classList.toggle('sidebar-open');
                });
            }

            // 2. Fecha o menu ao clicar fora (no overlay)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    body.classList.remove('sidebar-open');
                    layoutWrapper.classList.remove('sidebar-open');
                });
            }

            // 3. Ativa o link de navegação atual no sidebar
            const currentPage = window.location.pathname.split("/").pop();
            const navLinks = document.querySelectorAll(".sidebar-nav .nav-link");
            navLinks.forEach(link => {
                if (link.getAttribute("href") === currentPage) {
                    link.classList.add("active");
                    // Abre o dropdown pai, se existir
                    const parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        new bootstrap.Collapse(parentCollapse, {
                            toggle: true
                        });
                        // Ativa também o link "pai" do dropdown
                        const parentToggle = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
                        if (parentToggle) {
                            parentToggle.classList.add('active');
                        }
                    }
                }
            });
        });

        /**
         * NOVO: Função para os botões de filtro rápido em reports.php
         * Preenche os campos de data e submete o formulário.
         */
        function setDateFilter(range) {
            const startDateInput = document.getElementById('date_start');
            const endDateInput = document.getElementById('date_end');
            const form = document.getElementById('filterForm');

            // Verifica se os elementos existem na página atual (só vai rodar em reports.php)
            if (!startDateInput || !endDateInput || !form) {
                console.log("Elementos de filtro não encontrados. (Isso é normal se não estiver em reports.php)");
                return;
            }

            const today = new Date();
            let startDate, endDate;
            
            // Formata data para 'YYYY-MM-DD'
            const toISODate = (date) => date.toISOString().split('T')[0];

            endDate = toISODate(today); // Data final é sempre hoje

            if (range === '30days') {
                let d = new Date();
                d.setDate(d.getDate() - 29); // 30 dias atrás (incluindo hoje)
                startDate = toISODate(d);
            } else if (range === 'month') {
                startDate = toISODate(new Date(today.getFullYear(), today.getMonth(), 1));
            } else if (range === 'year') {
                startDate = toISODate(new Date(today.getFullYear(), 0, 1));
            }

            // Define os valores e submete o formulário
            startDateInput.value = startDate;
            endDateInput.value = endDate;
            form.submit();
        }
    </script>
    </body>
</html>