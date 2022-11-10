<?php

interface IInput { }

abstract class Input implements IInput { }

class CliInput extends Input { }

class WebInput extends Input { }



interface IOutput { }

abstract class Output implements IOutput { }

class CliOutput extends Output { }

class WebOutput extends Output { }
