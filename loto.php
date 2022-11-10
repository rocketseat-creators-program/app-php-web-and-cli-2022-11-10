#! /usr/bin/php
<?php

interface IInput {
    function readString(string $title): string;
    function readInteger(string $title): int;
}

abstract class Input implements IInput {
    function readInteger(string $title): int {
        do {
            $text = $this->readString($title);
        } while (strlen($text) > 0 && (int)$text != $text);
        return strlen($text) == 0 ? 0 : (int)$text;
    }
}

class CliInput extends Input {
    function readString(string $title): string {
        echo $title;
        $data = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
        if ($data) {
            echo $data . PHP_EOL;
        } else {
            $data = trim(fgets(STDIN));
        }
        echo PHP_EOL;
        return $data;
    }
}

class WebInput extends Input {
    function readString(string $title): string {
        $id = substr(md5($title), 0, 4);
        $value = isset($_GET[$id]) ? $_GET[$id] : '';
        echo "<form>";
        echo "<label for='$id'>";
        echo "<div>$title</div>";
        echo "<input type='number' id='$id' name='$id' value='$value' />";
        echo "</label>";
        echo "<input type='submit' value='Consultar' />";
        echo "</form>";
        return $value;
    }
}



interface IOutput {
    function addFields(array $fields): void;
    function header(): void;
    function body(array $results): void;
    function footer(): void;
}

abstract class Output implements IOutput {
    protected array $fields = [];

    function addFields(array $fields): void {
        $this->fields = array_merge($this->fields, $fields);
    }
}

class CliOutput extends Output {
    function header(): void {
        echo "{$this->fields['title']}" . PHP_EOL;
        echo str_repeat('^', strlen($this->fields['title'])) . PHP_EOL;
        ECHO PHP_EOL;
    }

    function body(array $results): void {
        echo "RESULTADOS DA CONSULTA:" . PHP_EOL;
        echo PHP_EOL;
        foreach ($results as $label => $value) {
            echo " | " . $label . ": " . PHP_EOL;
            echo " +--> " . $value . PHP_EOL;
            echo PHP_EOL;
        }
    }

    function footer(): void {
        echo "Script execution finished." . PHP_EOL;
        echo PHP_EOL;
    }
}

class WebOutput extends Output {
    function header(): void {
        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<title>{$this->fields['title']}</title>";
        echo "<style>";
        echo "html { background-color: #3C3F41; color: #87939A; font-family: sans-serif; font-size: 1.1em; }";
        echo "input { margin: 0.5em 0.5em 0.5em 0; padding: 0.5em; font-weight: bold; font-size: 1em; }";
        echo ".result { display: flex; margin-bottom: 0.25em; }";
        echo ".result .label { margin-right: 1em; width: 400px; }";
        echo ".result .value { font-weight: bold; color: white; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<h1>{$this->fields['title']}</title></h1>";
    }

    function body(array $results): void {
        echo "<div class='results'>";
        echo "<h2>Resultado da consulta</h2>";
        foreach ($results as $label => $value) {
            echo "<div class='result'>";
            echo "<span class='label'>{$label}</span>";
            echo "<span class='value'>{$value}</span>";
            echo "</div>";
        }
        echo "</div>";
    }

    function footer(): void {
        echo "<script>";
        echo "document.querySelector('input').value = document.querySelector('.result .value').innerText;";
        echo "</script>";
        echo "</body>";
        echo "</html>";
    }
}



class Presentation {
    public IInput $input;
    public IOutput $output;

    function __construct() {
        $isCli = defined("STDOUT");
        if ($isCli) {
            $this->input = new CliInput();
            $this->output = new CliOutput();
        } else {
            $this->input = new WebInput();
            $this->output = new WebOutput();
        }
    }
}



class LotoQuery {
    private string $apiUrlPattern = "https://servicebus2.caixa.gov.br/portaldeloterias/api/megasena/{lotteryNumber}";

    function queryJson(string $url): array {
        $context = stream_context_create([
            "ssl"=> [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ]);
        $data = @file_get_contents($url, false, $context);
        $error = error_get_last();
        if (!$error) {
            $json = json_decode($data, true);
            return $json;
        } else {
            return ["error" => $error];
        }
    }

    function query(int $lotteryNumber = 0): array {
        $lotteryNumberAsText = $lotteryNumber > 0 ? "$lotteryNumber" : "";
        $apiUrl = str_replace("{lotteryNumber}", $lotteryNumberAsText, $this->apiUrlPattern);
        $json = $this->queryJson($apiUrl);
        if (!isset($json["error"])) {
            $data = [
                "Número do sorteio" => $json['numero'],
                "Local do sorteio" => $json['localSorteio'] . ", " . $json['nomeMunicipioUFSorteio'],
                "Data do sorteio" => $json['dataApuracao'],
                "Números sorteados" => implode(' / ', $json['dezenasSorteadasOrdemSorteio']),
                "Valor arrecadado" => "R$ " . number_format($json['valorArrecadado'], 2, ",", "."),
                "Ganhadores e Prêmios na 1ª faixa" => $json['listaRateioPremio'][0]["descricaoFaixa"] . ": " . $json['listaRateioPremio'][0]["numeroDeGanhadores"] . " pessoa(s) x R$ " . number_format($json['listaRateioPremio'][0]["valorPremio"], 2, ",", ".") . " = total de R$ " . number_format($json['listaRateioPremio'][0]["numeroDeGanhadores"] * $json['listaRateioPremio'][0]["valorPremio"], 2, ",", "."),
                "Ganhadores e Prêmios na 2ª faixa" => $json['listaRateioPremio'][1]["descricaoFaixa"] . ": " . $json['listaRateioPremio'][1]["numeroDeGanhadores"] . " pessoa(s) x R$ " . number_format($json['listaRateioPremio'][1]["valorPremio"], 2, ",", ".") . " = total de R$ " . number_format($json['listaRateioPremio'][1]["numeroDeGanhadores"] * $json['listaRateioPremio'][1]["valorPremio"], 2, ",", "."),
                "Ganhadores e Prêmios na 3ª faixa" => $json['listaRateioPremio'][2]["descricaoFaixa"] . ": " . $json['listaRateioPremio'][2]["numeroDeGanhadores"] . " pessoa(s) x R$ " . number_format($json['listaRateioPremio'][2]["valorPremio"], 2, ",", ".") . " = total de R$ " . number_format($json['listaRateioPremio'][2]["numeroDeGanhadores"] * $json['listaRateioPremio'][2]["valorPremio"], 2, ",", "."),
                "Valor acumulado para o próximo sorteio" => "R$ " . number_format($json['valorAcumuladoProximoConcurso'], 2, ",", "."),
                "Valor estimado do próximo sorteio" => "R$ " . number_format($json['valorEstimadoProximoConcurso'], 2, ",", "."),
                "Data do próximo sorteio" => $json['dataProximoConcurso'],
            ];
        } else {
            $data = [ "Erro ao realizar consulta" => $json["error"]["message"] ];
        }
        return $data;
    }
}



$presentation = new Presentation();

$presentation->output->addFields(["title" => "LotoQuery"]);
$presentation->output->header();

$lotteryNumber = $presentation->input->readInteger("Número do sorteio: ");
$lotoQuery = new LotoQuery();
$data = $lotoQuery->query($lotteryNumber);

$presentation->output->body($data);
$presentation->output->footer();
