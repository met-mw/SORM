<?php
namespace SORM\Drivers;


use Exception;
use mysqli;
use mysqli_result;
use mysqli_stmt;
use SORM\Driver;
use SORM\Entity\Field;

/**
 * Class Mysql
 *
 * Драйвер для работы с MySQL и MariaDB
 */
class Mysql extends Driver {

    // Допустимые типы данных
    const TINYINT = 0;
    const BIT = 1;
    const SMALLINT = 2;
    const MEDIUMINT = 3;
    const INT = 4;
    const BIGINT = 5;
    const FLOAT = 6;
    const DOUBLE = 7;
    const DECIMAL = 8;
    const DATE = 9;
    const DATETIME = 10;
    const TIMESTAMP = 11;
    const TIME = 12;
    const YEAR = 13;
    const CHAR = 14;
    const VARCHAR = 15;
    const TINYBLOB = 16;
    const TINYTEXT = 17;
    const BLOB = 18;
    const TEXT = 19;
    const MEDIUMBLOB = 20;
    const MEDIUMTEXT = 21;
    const LONGBLOB = 22;
    const LONGTEXT = 23;

    public $typeTemplates = [
        self::TINYINT => '/^tinyint(\([0-9]*\))?$/i',
        self::BIT => '/^bit(\([0-9]*\))?$/i',
        self::SMALLINT => '/^smallint(\([0-9]*\))?$/i',
        self::MEDIUMINT => '/^mediumint(\([0-9]*\))?$/i',
        self::INT => '/^int(\([0-9]*\))?$/i',
        self::BIGINT => '/^bigint(\([0-9]*\))?$/i',
        self::FLOAT => '/^float$/i',
        self::DOUBLE => '/^double$/i',
        self::DECIMAL => '/^decimal(\([0-9]*,[0-9]*\))?$/i',
        self::DATE => '/^date$/i',
        self::DATETIME => '/^datetime$/i',
        self::TIMESTAMP => '/^timestamp$/i',
        self::TIME => '/^time$/i',
        self::YEAR => '/^year(4)(\([0-9]*\))?$/i',
        self::CHAR => '/^char(\([0-9]*\))?$/i',
        self::VARCHAR => '/^varchar(\([0-9]*\))?$/i',
        self::TINYBLOB => '/^tinyblob$/i',
        self::TINYTEXT => '/^tinytext$/i',
        self::BLOB => '/^blob$/i',
        self::TEXT => '/^text$/i',
        self::MEDIUMBLOB => '/^mediumblob$/i',
        self::MEDIUMTEXT => '/^mediumtext$/i',
        self::LONGBLOB => '/^longblob$/i',
        self::LONGTEXT => '/^longtext$/i'
    ];

    protected $typeClasses = [
        self::TINYINT => 'SORM\Entity\Field\Mysql\Tinyint',
        self::BIT => 'SORM\Entity\Field\Mysql\Bit',
        self::SMALLINT => 'SORM\Entity\Field\Mysql\Smallint',
        self::MEDIUMINT => 'SORM\Entity\Field\Mysql\Mediumint',
        self::INT => 'SORM\Entity\Field\Mysql\Int',
        self::BIGINT => 'SORM\Entity\Field\Mysql\Bigint',
        self::FLOAT => 'SORM\Entity\Field\Mysql\Float',
        self::DOUBLE => 'SORM\Entity\Field\Mysql\Double',
        self::DECIMAL => 'SORM\Entity\Field\Mysql\Decimal',
        self::DATE => 'SORM\Entity\Field\Mysql\Date',
        self::DATETIME => 'SORM\Entity\Field\Mysql\Datetime',
        self::TIMESTAMP => 'SORM\Entity\Field\Mysql\Timestamp',
        self::TIME => 'SORM\Entity\Field\Mysql\Time',
        self::YEAR => 'SORM\Entity\Field\Mysql\Year',
        self::CHAR => 'SORM\Entity\Field\Mysql\Char',
        self::VARCHAR => 'SORM\Entity\Field\Mysql\Varchar',
        self::TINYBLOB => 'SORM\Entity\Field\Mysql\Tinyblob',
        self::TINYTEXT => 'SORM\Entity\Field\Mysql\Tinytext',
        self::BLOB => 'SORM\Entity\Field\Mysql\Blob',
        self::TEXT => 'SORM\Entity\Field\Mysql\Text',
        self::MEDIUMBLOB => 'SORM\Entity\Field\Mysql\Mediumblob',
        self::MEDIUMTEXT => 'SORM\Entity\Field\Mysql\Mediumtext',
        self::LONGBLOB => 'SORM\Entity\Field\Mysql\Longblob',
        self::LONGTEXT => 'SORM\Entity\Field\Mysql\Longtext'
    ];

    /** @var mysqli */
    private $mysqli = null;
    /** @var mysqli_result|boolean */
    private $result = null;
    /** @var mysqli_stmt */
    private $stmt = null;

    protected function config() {
        $host = $this->getSetting('host');
        $user = $this->getSetting('user');
        $password = $this->getSetting('password');
        $db = $this->getSetting('db');

        $this->mysqli = new mysqli($host, $user, $password, $db);

        if ($this->mysqli->connect_errno) {
            throw new Exception("Не удалось подключиться к MySQL: " . $this->mysqli->connect_error);
        }
    }

    public function query($query) {
        $this->result = $this->mysqli->query($query);
    }

    public function fetchAssoc() {
        return $this->result->fetch_assoc();
    }

    public function fetchRow() {
        return $this->result->fetch_row();
    }

    public function fetchFields() {
        return $this->result->fetch_fields();
    }

    public function fetchAll() {
        return $this->result->fetch_all();
    }

    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }

    public function prepare($query) {
        $this->stmt = $this->mysqli->prepare($query);
    }

    public function bindParameter($types, array $parameters) {
        $preparedAttributes = [];
        $counter = 0;
        foreach ($parameters as $parameter) {
            $parameterName = "bindParam{$counter}";
            $$parameterName = $parameter;
            $preparedAttributes[] = &$$parameterName;
            $counter++;
        }
        array_unshift($preparedAttributes, $types);
        call_user_func_array([$this->stmt, 'bind_param'], $preparedAttributes);
    }

    public function execute() {
        $this->stmt->execute();
    }

    public function getResult() {
        $this->result;
    }

    public function detectFieldKey($key) {
        switch (mb_strtolower($key)) {
            case 'pri':
                $result = Field::KEY_PRIMARY;
                break;
            default:
                $result = Field::KEY_EMPTY;
        }

        return $result;
    }

    public function detectFieldNull($null) {
        return mb_strtolower($null) == 'yes';
    }

}