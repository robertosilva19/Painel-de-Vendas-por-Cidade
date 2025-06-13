<?php 
require_once "classes/Viagens.class.php";
require_once "classes/Passageiros.class.php";
require_once "classes/Encomendas.class.php";

$totalViagensMesAtual = new Viagens;
$totaViagMesAtu = $totalViagensMesAtual->findAllListaMesAtual();

$totalEncomendasMesAtual = new Encomendas;
$totaEcomendaMesAtu = $totalEncomendasMesAtual->findAllListaEncomendasMesAtual();

$totalPassageirosMesAtual = new Passageiros;
$totaPassageiroMesAtu = $totalPassageirosMesAtual->findAllListaPassageirosMesAtual();
?>

<div class="row">
    <div class="col-sm-4 col-xs-6">
        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-users"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $totaViagMesAtu['totalViagens']; ?>" data-postfix="" data-duration="1500" data-delay="0">0</div>
            <h3>Registro de viagens</h3>
            <p>Total viagens registradas neste mês.</p>
        </div>
    </div>
    <div class="col-sm-4 col-xs-6">
        <div class="tile-stats tile-green">
            <div class="icon"><i class="entypo-chart-bar"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $totaPassageiroMesAtu['totalPassageiros']; ?>" data-postfix="" data-duration="1500" data-delay="600">0</div>
            <h3>Passagens</h3>
            <p>Total passagens registradas neste mês.</p>
        </div>
    </div>
    <div class="clear visible-xs"></div>
    <div class="col-sm-4 col-xs-6">
        <div class="tile-stats tile-aqua">
            <div class="icon"><i class="entypo-mail"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $totaEcomendaMesAtu['totalEncomendas']; ?>" data-postfix="" data-duration="1500" data-delay="1200">0</div>
            <h3>Encomendas</h3>
            <p>Total encomendas neste mês</p>
        </div>
    </div>
</div>

<!-- Novo gráfico de vendas por cidade -->
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-8">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">Vendas de Passagens por Cidade</div>
            </div>
            <div class="panel-body">
                <!-- Filtro de datas -->
                <!-- <div class="row" style="margin-bottom: 15px;">
                    <div class="col-sm-3">
                        <label>Data Inicial:</label>
                        <input type="date" id="dataInicial" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <label>Data Final:</label>
                        <input type="date" id="dataFinal" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <label>&nbsp;</label>
                        <button type="button" id="filtrarBtn" class="btn btn-primary form-control">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-sm-3">
                        <label>&nbsp;</label>
                        <button type="button" id="limparFiltroBtn" class="btn btn-default form-control">
                            <i class="fa fa-refresh"></i> Semana Atual
                        </button>
                    </div>
                </div> -->
                
                <div id="piechart" style="width: 100%; height: 400px;"></div>
                
                <div id="noDataMessage" style="display: none; text-align: center; padding: 50px;">
                    <i class="fa fa-info-circle fa-3x text-muted"></i>
                    <p class="text-muted">Nenhuma venda encontrada para o período selecionado.</p>
                </div>
                
                <div id="debugInfo" style="margin-top: 10px; font-size: 12px; color: #666;"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-secondary">
            <div class="panel-heading">
                <div class="panel-title">Informações</div>
            </div>
            <div class="panel-body">
                <p>Clique em uma fatia do gráfico para ver detalhes da cidade.</p>
                <div id="cityStats">
                    <div id="periodInfo" class="alert alert-info">
                        <strong>Período:</strong> <span id="currentPeriod">Semana atual</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalhes da cidade -->
<div class="modal fade" id="cityDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalhes de Vendas - <span id="modalCityName"></span></h4>
            </div>
            <div class="modal-body">
                <div id="cityDetailsContent">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Carregando dados...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
let currentData = [];
let currentDateRange = {
    inicial: null,
    final: null
};

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(loadChartData);

async function loadChartData(dataInicial = null, dataFinal = null) {
    try {
        let url = 'api/vendas-cidades.php?action=chart-data';
        if (dataInicial && dataFinal) {
            url += `&data_inicial=${dataInicial}&data_final=${dataFinal}`;
            currentDateRange.inicial = dataInicial;
            currentDateRange.final = dataFinal;
            document.getElementById('currentPeriod').textContent = 
                `${new Date(dataInicial).toLocaleDateString('pt-BR')} até ${new Date(dataFinal).toLocaleDateString('pt-BR')}`;
        } else {
            currentDateRange.inicial = null;
            currentDateRange.final = null;
            document.getElementById('currentPeriod').textContent = 'Semana atual';
        }

        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();

        if (!result.success) throw new Error(result.error || 'Erro desconhecido');
        const data = result.data;
        currentData = data;

        if (data.length === 0) {
            document.getElementById('piechart').style.display = 'none';
            document.getElementById('noDataMessage').style.display = 'block';
            document.getElementById('noDataMessage').innerHTML = `
                <i class="fa fa-info-circle fa-3x text-muted"></i>
                <p class="text-muted">Nenhuma venda encontrada para o período selecionado.</p>
                <p class="text-muted">Tente selecionar um período diferente ou aguarde novos dados.</p>
            `;
            return;
        }

        document.getElementById('piechart').style.display = 'block';
        document.getElementById('noDataMessage').style.display = 'none';

        // Ajuste para o novo SQL: cidade (origem) e total
        var chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Cidade');
        chartData.addColumn('number', 'Total');

        data.forEach(item => {
            chartData.addRow([item.cidade, parseInt(item.total)]);
        });

        var options = {
            title: 'Passagens por Cidade de Origem',
            pieHole: 0.3,
            legend: {position: 'right'},
            chartArea: {left: 20, top: 50, width: '70%', height: '85%'}
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(chartData, options);

        // Atualizar estatísticas do período
        updatePeriodStats(data);

    } catch (error) {
        document.getElementById('piechart').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'block';
        document.getElementById('noDataMessage').innerHTML = `
            <i class="fa fa-exclamation-triangle fa-3x text-warning"></i>
            <p class="text-warning">Erro ao carregar dados: ${error.message}</p>
            <p class="text-muted">Verifique a conexão com o banco de dados.</p>
        `;
    }
}

// Atualizar estatísticas do período
function updatePeriodStats(data) {
    const totalPassagens = data.reduce((sum, item) => sum + parseInt(item.total), 0);
    const totalCidades = data.length;
    document.getElementById('cityStats').innerHTML = `
        <div id="periodInfo" class="alert alert-info">
            <strong>Período:</strong> <span id="currentPeriod">${document.getElementById('currentPeriod').textContent}</span>
        </div>
        <div class="well well-sm">
            <h5><i class="fa fa-bar-chart"></i> Resumo do Período</h5>
            <ul class="list-unstyled">
                <li><strong>Total de Passagens:</strong> ${totalPassagens}</li>
                <li><strong>Cidades Atendidas:</strong> ${totalCidades}</li>
            </ul>
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('filtrarBtn').addEventListener('click', function() {
        const dataInicial = document.getElementById('dataInicial').value;
        const dataFinal = document.getElementById('dataFinal').value;
        if (!dataInicial || !dataFinal) {
            alert('Por favor, selecione ambas as datas.');
            return;
        }
        if (new Date(dataInicial) > new Date(dataFinal)) {
            alert('A data inicial deve ser menor que a data final.');
            return;
        }
        loadChartData(dataInicial, dataFinal);
    });

    document.getElementById('limparFiltroBtn').addEventListener('click', function() {
        document.getElementById('dataInicial').value = '';
        document.getElementById('dataFinal').value = '';
        loadChartData();
    });
});
</script>

<?php include __DIR__ . '../../../global/modulos/visao_geral/visao_geral_acesso_rapido.php'; ?>