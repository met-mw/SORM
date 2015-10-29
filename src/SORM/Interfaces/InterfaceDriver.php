<?php
namespace SORM\Interfaces;


interface InterfaceDriver {

    public function query($query);

    public function fetchAssoc();

    public function fetchRow();

    public function fetchFields();

    public function fetchAll();

    public function lastInsertId();

    public function prepare($query);

    public function bindParameter($types, array $attributes);

    public function execute();

    public function getResult();


} 