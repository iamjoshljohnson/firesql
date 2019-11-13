<?php

/**
 *    __  _____   ___   __          __
 *   / / / /   | <  /  / /   ____ _/ /_  _____
 *  / / / / /| | / /  / /   / __ `/ __ `/ ___/
 * / /_/ / ___ |/ /  / /___/ /_/ / /_/ (__  )
 * `____/_/  |_/_/  /_____/`__,_/_.___/____/
 *
 * @package FireSql
 * @author UA1 Labs Developers https://ua1.us
 * @copyright Copyright (c) UA1 Labs
 */

namespace UA1Labs\Fire\Sql;

use \PDO;
use \UA1Labs\Fire\Bug\Panel\FireSqlPanel;
use \UA1Labs\Fire\Sql\Panel\SqlStatement;

/**
 * This class is responsible for connecting and interacting with the database
 * object. It contains functionality to execute and query the database itself.
 * This class will also track all SQL queries and store them for the FireBug
 * Panel.
 */
class Connector
{

    /**
     * The PDO Object.
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * The FireBug instance.
     *
     * @var \UA1Labs\Fire\Bug;
     */
    private $fireBug;

    /**
     * The class constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        if (class_exists(\UA1Labs\Fire\Bug)) {
            $this->fireBug = \UA1Labs\Fire\Bug::get();
        }
    }

    /**
     * Executes a SQL statement. This method is meant for executing
     * SQL statements that manipulate data within the database.
     *
     * @param string $sql The SQL statement
     */
    public function exec($sql) {
        // get start time of sql execution
        $start = $this->fireBug->timer();
        // execute sql
        $this->pdo->exec($sql);
        // record sql statement
        if ($this->fireBug->isEnabled()) {
            $trace = debug_backtrace();
            $this->recordSqlStatement($start, $sql, $trace);
        }
    }

    /**
     * Executes a SQL query. This meathod is meant for executing
     * SQL queries that will return records.
     *
     * @param string $sql The SQL query
     * @return array
     */
    public function query($sql)
    {
        // get start time of sql execution
        $start = $this->fireBug->timer();
        // execute sql
        $records = $this->pdo->query($sql);
        // record sql statement
        if ($this->fireBug->isEnabled()) {
            $trace = debug_backtrace();
            $this->recordSqlStatement($start, $sql, $trace);
        }

        return $records;
    }

    /**
     * Places quotes around input strings for SQL Queries.
     *
     * @param string $statement
     * @return string
     */
    public function quote($statement)
    {
        return $this->pdo->quote($statement);
    }

    /**
     * Records SQL statements to the FireBug Panel created to
     * track SQL queries.
     *
     * @param float $start A timestamp created by FireBug::timer()
     * @param string $sql The SQL Statement to Record
     * @param array $trace The trace generated by debug_backtrace()
     */
    private function recordSqlStatement($start, $sql, $trace)
    {
        if ($this->fireBug) {
            $sqlStatement = new SqlStatement();
            $sqlStatement->setStatement($sql);
            $sqlStatement->setTime($this->fireBug->timer($start));
            $sqlStatement->setTrace($trace);
            $this->fireBug
                ->getPanel(FireSqlPanel::ID)
                ->addSqlStatement($sqlStatement);
        }
    }

}
