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

    public function bindParameter($types, array $parameters);

    public function execute();

    public function getResult();

    public function getColumnTypeClass($type);

    public function detectFieldKey($key);

    public function detectFieldNull($null);



} 