<?php

interface IInput { }

abstract class Input implements IInput { }

class CliInput extends Input { }

class WebInput extends Input { }



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



$presentation = new Presentation();

$presentation->output->addFields(["title" => "LotoQuery"]);
$presentation->output->header();

$data = [
    "chave1" => "valor1",
    "chave2" => "valor2",
];

$presentation->output->body($data);
$presentation->output->footer();
