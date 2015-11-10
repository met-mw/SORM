<?php
namespace SORM\Tools;


abstract class Builder {

    const OPERAND_TYPE_F = 'field';
    const OPERAND_TYPE_V = 'value';

    abstract public function table($table);

    abstract public function build();

} 