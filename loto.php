<?php

interface IInput { }

abstract class Input implements IInput { }

class CliInput extends Input { }

class WebInput extends Input { }



interface IOutput { }

abstract class Output implements IOutput { }

class CliOutput extends Output { }

class WebOutput extends Output { }



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