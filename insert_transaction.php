<?php
session_start();
include('../config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('../config/logger.php');

$page_title = "Registrar Nova Transação";
$breadcrumb_active = "Registrar Transação";

// Verificação de segurança: Apenas operadores (usuario) podem acessar
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'usuario') {
    header('Location: ../login.php');
    exit;
}
$id_usuario_logado = $_SESSION['user_id'];
$message = "";

// Obtém o percentual de comissão do usuário para exibição e cálculo (se for necessário no frontend)
$stmt_comissao = $pdo->prepare("SELECT percentual_comissao FROM usuarios WHERE id = ?");
$stmt_comissao->execute([$id_usuario_logado]);
$user_comissao_rate = $stmt_comissao->fetchColumn() ?? 0.00; // Valor padrão 0 se não encontrar

// Inclui o cabeçalho
include('../header.php'); 
?>

<h2 class="mb-4">Registrar Nova Transação</h2>
<p class="text-muted">Preencha os valores da operação. A sua comissão será calculada automaticamente em **<?php echo number_format($user_comissao_rate, 2, ',', '.'); ?>%**.</p>

<div class="card shadow-sm">
    <div class="card-body">
        <form id="transactionForm" method="POST" action="../process_transaction.php">
            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_logado; ?>">
            <input type="hidden" id="user_comissao_rate" value="<?php echo $user_comissao_rate; ?>">

            <!-- Campos de Data e Hora -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="data_transacao" class="form-label">Data da Transação</label>
                    <input type="date" class="form-control" id="data_transacao" name="data_transacao" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="hora_transacao" class="form-label">Hora da Transação</label>
                    <input type="time" class="form-control" id="hora_transacao" name="hora_transacao" value="<?php echo date('H:i'); ?>" required>
                </div>
            </div>

            <!-- Campos de Valores (Moeda) -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="valor_deposito" class="form-label">Valor de Depósito</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control currency-mask" id="valor_deposito" name="valor_deposito" value="0,00" required>
                    </div>
                    <div class="form-text">Valor que entrou na plataforma.</div>
                </div>
                <div class="col-md-4">
                    <label for="valor_saque" class="form-label">Valor de Saque</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control currency-mask" id="valor_saque" name="valor_saque" value="0,00" required>
                    </div>
                    <div class="form-text">Valor que saiu da plataforma.</div>
                </div>
                <div class="col-md-4">
                    <label for="valor_bau" class="form-label">Valor de Baú</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control currency-mask" id="valor_bau" name="valor_bau" value="0,00" required>
                    </div>
                    <div class="form-text">Valor depositado no Baú.</div>
                </div>
            </div>

            <!-- Resumo e Comissão Calculada -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="alert alert-primary">
                        **Lucro Bruto Estimado: R$ <span id="lucro_bruto_display">0,00</span>**
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        **Sua Comissão Estimada (<?php echo number_format($user_comissao_rate, 2, ',', '.'); ?>%): R$ <span id="comissao_display">0,00</span>**
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle me-2"></i> Confirmar Registro
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Carrega jQuery Mask Plugin (assumindo que está disponível via CDN no footer ou já incluído)
    // Se não estiver, o usuário deve incluir o arquivo `jquery.mask.min.js`
    
    $(document).ready(function(){
        // Aplica a máscara de moeda
        $('.currency-mask').mask('0#.#00,00', {reverse: true});
        
        // Função de cálculo
        function calcularValores() {
            // Função para converter string de moeda BRL para float
            function parseBRL(value) {
                // Remove separadores de milhar (ponto) e substitui a vírgula decimal por ponto
                return parseFloat(value.replace(/\./g, '').replace(',', '.'));
            }

            const depositoStr = $('#valor_deposito').val() || '0,00';
            const saqueStr = $('#valor_saque').val() || '0,00';
            const bauStr = $('#valor_bau').val() || '0,00';
            const comissaoRate = parseFloat($('#user_comissao_rate').val()) / 100;

            const deposito = parseBRL(depositoStr);
            const saque = parseBRL(saqueStr);
            const bau = parseBRL(bauStr);

            // Lucro Bruto = (Depósito + Baú) - Saque
            const lucroBruto = (deposito + bau) - saque;

            // Comissão = Lucro Bruto * Taxa de Comissão (apenas se o lucro for positivo)
            let comissao = 0;
            if (lucroBruto > 0) {
                 comissao = lucroBruto * comissaoRate;
            } else {
                 comissao = 0; // Se o lucro for negativo, a comissão é 0.
            }
            
            // Formatação para exibição
            function formatBRL(value) {
                return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            $('#lucro_bruto_display').text(formatBRL(lucroBruto));
            $('#comissao_display').text(formatBRL(comissao));

            // Atualiza o estado da cor do lucro
            if (lucroBruto >= 0) {
                $('#lucro_bruto_display').parent().removeClass('alert-danger').addClass('alert-primary');
            } else {
                $('#lucro_bruto_display').parent().removeClass('alert-primary').addClass('alert-danger');
            }
            
             // Atualiza a cor da comissão
            if (comissao > 0) {
                $('#comissao_display').parent().removeClass('alert-danger').addClass('alert-success');
            } else {
                $('#comissao_display').parent().removeClass('alert-success').addClass('alert-danger');
            }
        }

        // Liga o evento de input nos campos para recalcular
        $('#valor_deposito, #valor_saque, #valor_bau').on('keyup change', calcularValores);
        
        // Cálculo inicial ao carregar a página
        calcularValores();

        // Submissão do formulário
        $('#transactionForm').on('submit', function(e) {
            // Remove as máscaras dos campos antes do envio
            $('.currency-mask').each(function() {
                const unmaskedValue = $(this).val().replace(/\./g, '').replace(',', '.');
                $(this).val(unmaskedValue);
            });

            // Adiciona campos de lucro e comissão calculados (essenciais para o PHP)
            const lucroBrutoFinal = parseBRL($('#lucro_bruto_display').text() || '0,00');
            const comissaoFinal = parseBRL($('#comissao_display').text() || '0,00');

            // Adiciona campos ocultos
            $(this).append('<input type="hidden" name="lucro_bruto_calculado" value="' + lucroBrutoFinal.toFixed(2) + '">');
            $(this).append('<input type="hidden" name="comissao_usuario_calculada" value="' + comissaoFinal.toFixed(2) + '">');

             // A taxa de comissão do ADMIN/SUB-ADMIN (fixa em 10%) e SUPER_ADM (fixa em 50% do lucro bruto)
             // Estes valores serão recalculados no servidor para maior segurança
             
            return true; 
        });
    });
</script>

<?php 
include('../footer.php');
?>