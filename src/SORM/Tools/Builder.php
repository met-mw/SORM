<?php
namespace SORM\Tools;


abstract class Builder {

    const OPERAND_TYPE_F = 0;
    const OPERAND_TYPE_V = 1;
    const OPERAND_TYPE_O = 2;

    abstract public function table($table);

    abstract public function build();

} 