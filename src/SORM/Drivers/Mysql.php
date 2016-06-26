<?php
namespace SORM\Drivers;


use Exception;
use PDO;
use PDOStatement;
use SORM\DriverAbstract;

/**
 * Class Mysql
 *
 * Driver for MySQL and MariaDB
 */
class Mysql extends DriverAbstract {

    /** @var PDO */
    protected $PDO;
    /** @var PDOStatement */
    protected $PDOStatement;

    /** @var string */
    protected $host;
    /** @var string */
    protected $DBName;
    /** @var string */
    protected $charset;
    /** @var string */
    protected $user;
    /** @var string */
    protected $pass;
    /** @var array */
    protected $options;

    /**
     * Mysql constructor.
     * @param string $host Host
     * @param string $DBName Data base name
     * @param string $charset Charset
     * @param string $user User name
     * @param string $pass Password
     * @param array $options
     */
    public function __construct($host, $DBName, $charset, $user, $pass, array $options = [])
    {
        $this->host = $host;
        $this->DBName = $DBName;
        $this->charset = $charset;
        $this->user = $user;
        $this->pass = $pass;
        $this->options = $options;
    }

    /**
     * Connecting
     *
     * @param bool $isPersistent
     * @return $this
     */
    public function connect($isPersistent = false)
    {
        $dsn = "mysql:host={$this->host};dbname={$this->DBName};charset={$this->charset}";
        $this->PDO = new PDO($dsn, $this->user, $this->pass, $this->options);

        return $this;
    }

    /**
     * Fetch row
     *
     * @return array
     * @throws Exception
     */
    public function fetchRow()
    {
        if (is_null($this->PDOStatement)) {
            throw new Exception('No query results.');
        }

        return $this->PDOStatement->fetch();
    }

    /**
     * Fetch row as assoc array
     *
     * @return array <string, mixed>
     * @throws Exception
     */
    public function fetchRowAssoc()
    {
        if (is_null($this->PDOStatement)) {
            throw new Exception('No query results.');
        }

        return $this->PDOStatement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all rows
     * @return array[]
     *
     * @throws Exception
     */
    public function fetchAll()
    {
        if (is_null($this->PDOStatement)) {
            throw new Exception('No query results.');
        }

        return $this->PDOStatement->fetchAll();
    }

    /**
     * Fetch all rows as assoc array
     *
     * @return array <string, mixed>[]
     * @throws Exception
     */
    public function fetchAllAssoc()
    {
        if (is_null($this->PDOStatement)) {
            throw new Exception('No query results.');
        }

        return $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get last inserted PK
     *
     * @return int
     */
    public function getLastInsertedPK()
    {
        return $this->PDO->lastInsertId();
    }

    /**
     * Execute query
     *
     * @param string $query Query string
     * @param array $parameters Parameters
     * @return $this
     */
    public function query($query, $parameters = [])
    {
        $this->PDOStatement = $this->PDO->prepare($query);
        $this->PDOStatement->execute($parameters);

        return $this;
    }

}