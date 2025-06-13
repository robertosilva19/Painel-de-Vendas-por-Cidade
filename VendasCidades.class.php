<?php
// filepath: app/classes/VendasCidades.class.php
require_once __DIR__ . "/config.php";

class VendasCidades {
    private $pdo;

    public function __construct() {
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

    // public function getVendasPorCidade($dataInicial = null, $dataFinal = null) {
    //     $where = "bus_ponto_inicial IS NOT NULL AND bus_ponto_inicial != '' AND bus_ponto_final IS NOT NULL AND bus_ponto_final != ''";
    //     $params = [];
    //     if ($dataInicial && $dataFinal) {
    //         $where .= " AND hora_embarque BETWEEN :dataInicial AND :dataFinal";
    //         $params[':dataInicial'] = $dataInicial;
    //         $params[':dataFinal'] = $dataFinal;
    //     }
    //     $sql = "SELECT 
    //                 bus_ponto_inicial AS cidade_origem,
    //                 bus_ponto_final AS cidade_destino,
    //                 COUNT(*) AS total_viagens
    //             FROM passageiros
    //             WHERE $where
    //             GROUP BY bus_ponto_inicial, bus_ponto_final
    //             ORDER BY total_viagens DESC
    //             LIMIT 10";

    //            // die($sql);
    //     $stmt = $this->pdo->prepare($sql);
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

        public function getVendasPorCidade($dataInicial = null, $dataFinal = null) {
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

               // die($sql);
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPorCidadeOrigem() {
        $sql = "SELECT bus_ponto_inicial AS cidade, COUNT(*) AS total
                FROM passageiros
                GROUP BY cidade
                ORDER BY total DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>