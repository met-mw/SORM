<?php
namespace SORM\Tools;


abstract class Builder {

    abstract public function table($table);

    abstract public function build();

} 