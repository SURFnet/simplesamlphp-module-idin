<?php

class sspmod_idin_iDIN_Store_Database extends sspmod_idin_Store {
    private $dsn;
    private $username;
    private $password;
    private $dateTime;
    private $table;

    private $db;

    public function __construct($config) {
        parent::__construct($config);
        
        if (!array_key_exists('dsn', $config)) {
            throw sspmod_idin_Exception::fromString('Missing required option \'dsn\'.');
        }

        $this->dsn = $config['dsn'];
        $this->dateTime = (0 === strpos($this->dsn, 'sqlite:')) ? 'DATETIME("NOW")' : 'NOW()';

        if (array_key_exists('username', $config)) {
            $this->username = $config['username'];
        } else {
            $this->username = NULL;
        }

        if (array_key_exists('password', $config)) {
            $this->password = $config['password'];
        } else {
            $this->password = NULL;
        }
        
        if (array_key_exists('table', $config)) {
            $this->table = $config['table'];
        } else {
            $this->table = '';
        }
    }
    
    public function getDirectory() {
        $st = $this->execute(
            'SELECT `value` ' .
            'FROM `' . $this->table . '` ' .
            'WHERE `key` = \'directory\'',
            array()
        );

        if ($st === false) {
            throw sspmod_idin_Exception::fromString('Cannot get directory!');
        }

        $row = $st->fetch(1);
        return $row->value;
    }
    
    public function saveDirectory($dirRes) {
        $st = $this->execute(
            'UPDATE `' . $this->table .
            '` SET `value` = \'' . $dirRes->getRawMessage() . '\'' .
            'WHERE `key` = \'directory\'',
            array()
        );
        
        if ($st === false) {
            throw sspmod_idin_Exception::fromString('Cannot save directory!');
        }
    }
    
    private function getDB() {
        if ($this->db !== null) {
            return $this->db;
        }

        $this->db = new PDO($this->dsn, $this->username, $this->password);

        return $this->db;
    }
    
    private function execute($statement, $parameters) {
        assert('is_string($statement)');
        assert('is_array($parameters)');

        $db = $this->getDB();
        if ($db === false) {
            return false;
        }

        $st = $db->prepare($statement);
        if ($st === false) {
            SimpleSAML_Logger::error('idin:Database - Error preparing statement');
            return false;
        }

        if ($st->execute($parameters) !== true) {
            SimpleSAML_Logger::error('idin:Database - Error preparing statement');
            return false;
        }

        return $st;
    }
}
