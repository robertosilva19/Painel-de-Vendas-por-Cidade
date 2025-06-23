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
// ?>

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
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-8">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">Viagens e Passageiros por Mês</div>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-sm-3">
                        <label>Ano:</label>
                        <select id="anoSelect" class="form-control">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>&nbsp;</label>
                        <button type="button" id="filtrarViagensBtn" class="btn btn-primary form-control">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
                
                <div id="viagensChart" style="width: 100%; height: 400px;"></div>
                
                <div id="noDataViagensMessage" style="display: none; text-align: center; padding: 50px;">
                    <i class="fa fa-info-circle fa-3x text-muted"></i>
                    <p class="text-muted">Nenhuma viagem encontrada para o ano selecionado.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-4">
        <div class="panel panel-secondary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">Vendas por Cidade</div>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-sm-6">
                        <label>Data Inicial:</label>
                        <input type="date" id="dataInicial" class="form-control form-control-sm"/>
                    </div>
                    <div class="col-sm-6">
                        <label>Data Final:</label>
                        <input type="date" id="dataFinal" class="form-control form-control-sm" />
                    </div>
                    <div class="col-sm-12" style="margin-top: 10px;">
                        <button type="button" id="filtrarBtn" class="btn btn-primary btn-sm btn-block">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
                
                <div id="piechart" style="width: 100%; height: 250px;"></div>
                
                <div id="noDataMessage" style="display: none; text-align: center; padding: 30px;">
                    <i class="fa fa-info-circle fa-2x text-muted"></i>
                    <p class="text-muted">Nenhuma venda encontrada.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
    <div class="col-sm-12">
        <div id="estatisticasViagens" class="alert alert-info" style="display: none;">
            <div class="row">
                <div class="col-sm-3">
                    <strong>Total de Viagens:</strong>
                    <span id="totalViagensAno" class="badge badge-primary">0</span>
                </div>
                <div class="col-sm-3">
                    <strong>Total de Passageiros:</strong>
                    <span id="totalPassageirosAno" class="badge badge-success">0</span>
                </div>
                <div class="col-sm-3">
                    <strong>Média Passageiros/Viagem:</strong>
                    <span id="mediaPassageiros" class="badge badge-info">0</span>
                </div>
                <div class="col-sm-3">
                    <strong>Mês com Mais Viagens:</strong>
                    <span id="melhorMes" class="badge badge-warning">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Novo gráfico de vendas por cidade -->
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-6">
        <?php include __DIR__ . '../../../global/modulos/visao_geral/visao_geral_acesso_rapido.php'; ?>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
let currentCidadeData = [];
let currentViagensData = [];

google.charts.load('current', {'packages':['corechart', 'bar']});
google.charts.setOnLoadCallback(function() {
    loadChartData(); // Carrega gráfico de cidades
    loadViagensChart(); // Carrega gráfico de viagens
});

// Função para carregar gráfico de viagens mensais
async function loadViagensChart(ano) {
 
    try {
        const anoSelecionado = ano || document.getElementById('anoSelect').value;
        const url = `api/vendas-cidades.php?action=viagens-mensais&ano=${anoSelecionado}`;
        
        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();

        if (!result.success) throw new Error(result.error || 'Erro desconhecido');
        const data = result.data;
        currentViagensData = data;

        if (data.length === 0) {
            document.getElementById('viagensChart').style.display = 'none';
            document.getElementById('noDataViagensMessage').style.display = 'block';
            document.getElementById('estatisticasViagens').style.display = 'none';
            return;
        }

        document.getElementById('viagensChart').style.display = 'block';
        document.getElementById('noDataViagensMessage').style.display = 'none';

        // Preparar dados para o gráfico de barras
        var chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Mês');
        chartData.addColumn('number', 'Viagens');
        chartData.addColumn('number', 'Passageiros');

        data.forEach(item => {
            chartData.addRow([
                item.nome_mes, 
                parseInt(item.total_viagens) || 0, 
                parseInt(item.total_passageiros) || 0
            ]);
        });

        var options = {
            title: `Viagens e Passageiros por Mês - ${anoSelecionado}`,
            titleTextStyle: {
                fontSize: 16,
                bold: true
            },
            chartArea: {left: 80, top: 60, width: '75%', height: '70%'},
            hAxis: {
                title: 'Meses',
                titleTextStyle: {fontSize: 12}
            },
            vAxes: {
                0: {
                    title: 'Quantidade de Viagens',
                    titleTextStyle: {color: '#1f77b4', fontSize: 12}
                },
                1: {
                    title: 'Número de Passageiros',
                    titleTextStyle: {color: '#ff7f0e', fontSize: 12}
                }
            },
            series: {
                0: {
                    type: 'columns',
                    targetAxisIndex: 0,
                    color: '#1f77b4'
                },
                1: {
                    type: 'columns', 
                    targetAxisIndex: 1,
                    color: '#ff7f0e'
                }
            },
            legend: {
                position: 'top',
                alignment: 'center'
            }
        };

        var chart = new google.visualization.ComboChart(document.getElementById('viagensChart'));
        chart.draw(chartData, options);

        // Atualizar estatísticas
        updateViagensStats(data, anoSelecionado);

    } catch (error) {
        document.getElementById('viagensChart').style.display = 'none';
        document.getElementById('noDataViagensMessage').style.display = 'block';
        document.getElementById('noDataViagensMessage').innerHTML = `
            <i class="fa fa-exclamation-triangle fa-3x text-warning"></i>
            <p class="text-warning">Erro ao carregar dados: ${error.message}</p>
        `;
    }
}

// Função para atualizar estatísticas das viagens
function updateViagensStats(data, ano) {
    const totalViagens = data.reduce((sum, item) => sum + parseInt(item.total_viagens || 0), 0);
    const totalPassageiros = data.reduce((sum, item) => sum + parseInt(item.total_passageiros || 0), 0);
    const mediaPassageiros = totalViagens > 0 ? (totalPassageiros / totalViagens).toFixed(1) : 0;
    
    // Encontrar mês com mais viagens
    const melhorMesData = data.reduce((prev, current) => 
        (parseInt(current.total_viagens) > parseInt(prev.total_viagens)) ? current : prev, data[0] || {});
    const melhorMes = melhorMesData.nome_mes || '-';

    document.getElementById('totalViagensAno').textContent = totalViagens;
    document.getElementById('totalPassageirosAno').textContent = totalPassageiros;
    document.getElementById('mediaPassageiros').textContent = mediaPassageiros;
    document.getElementById('melhorMes').textContent = melhorMes;
    document.getElementById('estatisticasViagens').style.display = 'block';
}

// Função original para gráfico de cidades (mantida)
async function loadChartData(dataInicial, dataFinal) {
    try {
        let url = 'api/vendas-cidades.php';
        if (dataInicial && dataFinal) {
            url += `?data_inicial=${dataInicial}&data_final=${dataFinal}`;
        }

        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();

        if (!result.success) throw new Error(result.error || 'Erro desconhecido');
        const data = result.data;
        currentCidadeData = data;

        if (data.length === 0) {
            document.getElementById('piechart').style.display = 'none';
            document.getElementById('noDataMessage').style.display = 'block';
            return;
        }

        document.getElementById('piechart').style.display = 'block';
        document.getElementById('noDataMessage').style.display = 'none';

        var chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Cidade');
        chartData.addColumn('number', 'Total');

        data.slice(0, 6).forEach(item => {
            chartData.addRow([item.cidade, parseInt(item.total)]);
        });

        var options = {
            title: 'Top Cidades',
            pieHole: 0.3,
            legend: {position: 'bottom'},
            chartArea: {left: 10, top: 30, width: '90%', height: '60%'},
            titleTextStyle: {fontSize: 14}
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(chartData, options);

    } catch (error) {
        document.getElementById('piechart').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'block';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Filtro do gráfico de viagens
    document.getElementById('filtrarViagensBtn').addEventListener('click', function() {
        const ano = document.getElementById('anoSelect').value;
        loadViagensChart(ano);
    });

    // Filtro original do gráfico de cidades
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
});
</script>