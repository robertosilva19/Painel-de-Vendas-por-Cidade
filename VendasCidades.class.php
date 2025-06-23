<?php
// filepath: app/classes/VendasCidades.class.php
require_once("Generic.class.php");
//require_once __DIR__ . "/config.php";

class VendasCidades
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function getVendasPorCidade($dataInicial = null, $dataFinal = null)
    {
        $where = "bus_ponto_inicial IS NOT NULL AND bus_ponto_inicial != '' AND bus_ponto_final IS NOT NULL AND bus_ponto_final != ''";
        $params = [];
        if ($dataInicial && $dataFinal) {
            $where .= " AND hora_embarque BETWEEN :dataInicial AND :dataFinal";
            $params[':dataInicial'] = $dataInicial;
            $params[':dataFinal'] = $dataFinal;
        }
        $sql = "SELECT 
                    bus_ponto_inicial AS cidade_origem,
                    bus_ponto_final AS cidade_destino,
                    COUNT(*) AS total_viagens
                FROM passageiros
                WHERE $where
                GROUP BY bus_ponto_inicial, bus_ponto_final
                ORDER BY total_viagens DESC
                LIMIT 10";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPorCidadeOrigem($dt_inicial = null, $dt_final = null)
    {
        $where = "WHERE 1 = 1 ";
        if ($dt_inicial && $dt_final) {
            $where .= " AND bus_data_cadastro BETWEEN '$dt_inicial' AND '$dt_final' ";
        }
        $sql = "SELECT 
	                bus_ponto_inicial AS cidade,  COUNT(*) AS total
                FROM passageiros p
                join passagens_valores_viagens as pvv
                on p.bus_idPassageiros = pvv.bus_id_passageiro
                {$where}
                GROUP BY cidade ORDER BY total DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nova função para obter dados de viagens e passageiros por mês
    public function getViagensPassageirosPorMes($anoInicial = null, $anoFinal = null)
    {
        $where = "WHERE 1 = 1 ";
        $params = [];

        if ($anoInicial && $anoFinal) {
            $where .= " AND YEAR(v.data_viagem) BETWEEN :anoInicial AND :anoFinal ";
            $params[':anoInicial'] = $anoInicial;
            $params[':anoFinal'] = $anoFinal;
        } else if ($anoInicial) {
            $where .= " AND YEAR(v.data_viagem) = :anoInicial ";
            $params[':anoInicial'] = $anoInicial;
        } else {
            // Se não especificar ano, pega o ano atual
            $where .= " AND YEAR(v.data_viagem) = YEAR(CURDATE()) ";
        }

        $sql = "SELECT 
                    YEAR(v.data_viagem) as ano,
                    MONTH(v.data_viagem) as mes,
                    MONTHNAME(v.data_viagem) as nome_mes,
                    COUNT(DISTINCT v.id_viagem) as total_viagens,
                    COALESCE(SUM(p.total_passageiros), 0) as total_passageiros
                FROM viagens v
                LEFT JOIN (
                    SELECT 
                        pvv.bus_id_viagem,
                        COUNT(*) as total_passageiros
                    FROM passagens_valores_viagens pvv
                    INNER JOIN passageiros pass ON pvv.bus_id_passageiro = pass.bus_idPassageiros
                    GROUP BY pvv.bus_id_viagem
                ) p ON v.id_viagem = p.bus_id_viagem
                {$where}
                GROUP BY YEAR(v.data_viagem), MONTH(v.data_viagem)
                ORDER BY ano DESC, mes ASC";

        die($sql);

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Função alternativa caso a estrutura do banco seja diferente
    public function getViagensPassageirosPorMesSimplificado($ano = null)
    {
        $where = "WHERE 1 = 1 ";
        $params = [];

        if ($ano) {
            $where .= " AND YEAR(v.dt_partida) = :ano ";
            $params[':ano'] = $ano;
        } else {
            $where .= " AND YEAR(v.dt_partida) = YEAR(CURDATE()) ";
        }

        $sql = "SELECT 
                YEAR(v.dt_partida) as ano,
                MONTH(v.dt_partida) as mes,
                CASE MONTH(v.dt_partida)
                    WHEN 1 THEN 'Janeiro'
                    WHEN 2 THEN 'Fevereiro'
                    WHEN 3 THEN 'Março'
                    WHEN 4 THEN 'Abril'
                    WHEN 5 THEN 'Maio'
                    WHEN 6 THEN 'Junho'
                    WHEN 7 THEN 'Julho'
                    WHEN 8 THEN 'Agosto'
                    WHEN 9 THEN 'Setembro'
                    WHEN 10 THEN 'Outubro'
                    WHEN 11 THEN 'Novembro'
                    WHEN 12 THEN 'Dezembro'
                END as nome_mes,
                COUNT(DISTINCT v.bus_idViagem) as total_viagens,
                COUNT(p.bus_idPassageiros) as total_passageiros
            FROM 
                passageiros p
            INNER JOIN 
                passagens_valores_viagens pvv ON p.bus_idPassageiros = pvv.bus_id_passageiro
            INNER JOIN 
                bus_viagens v ON pvv.bus_id_viag = v.bus_idViagem 
            {$where}
            GROUP BY 
                YEAR(v.dt_partida), MONTH(v.dt_partida)
            ORDER BY 
                ano DESC, mes ASC";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
