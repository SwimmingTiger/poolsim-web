<?php
class SimConfigException extends Exception {
    // just an alias
}

class SimConfig {
    private $number_clients = 10;
	private $ss_ip = "";
    private $ss_port = 0;
    private $username = "";
    private $minername_prefix = "simulator";

    // autoset by loadFromDb() or saveToDb()
    // read only
    private $id;

    public function __get($key) {
        return $this->$key;
    }

    public function setClientsNumber($number) {
        if ($number < 1) {
            throw new SimConfigException('矿机数不能少于1台');
        }

        $this->number_clients = (int)$number;
    }

    public function setStratumServer($ip, $port) {
        if ($port < 1 || $port > 65535) {
            throw new SimConfigException('矿池端口应在1到65535之间');
        }

        if (strlen($ip) < 1) {
            throw new SimConfigException('矿池IP不能为空');
        }
        
        $this->ss_ip = trim($ip);
        $this->ss_port = (int)$port;
    }

    public function setUserName($userName) {
        if (strlen($userName) < 1) {
            throw new SimConfigException('子账户名不能为空');
        }

        $this->username = trim($userName);
    }

    public function setMinerNamePrefix($prefix) {
        $this->minername_prefix = trim($prefix);
    }

    public function makeConfig() {
        $number = json_encode($this->number_clients);
        $ip = json_encode($this->ss_ip);
        $port = json_encode($this->ss_port);
        $userName = json_encode($this->username);
        $prefix = json_encode($this->minername_prefix);

        $conf = <<<TEXT
simulator = {
  number_clients = $number;
  ss_ip = $ip;
  ss_port = $port;
  username = $userName;
  minername_prefix = $prefix;
};
TEXT;

        return $conf;
    }

    public function saveToDb($id = NULL) {
        $db = db::conn();

        $isUpdate = (NULL !== $id);
        
        if ($isUpdate) {
            $sql = 'UPDATE `simconfig` SET `number_clients` = ?, `ss_ip` = ?, `ss_port` = ?, `username` = ?, `minername_prefix` = ? WHERE `id` = ?';
            $data = [$this->number_clients, $this->ss_ip, $this->ss_port, $this->username, $this->minername_prefix, (int)$id];
        } else {
            $sql = 'INSERT INTO `simconfig` (`number_clients`, `ss_ip`, `ss_port`, `username`, `minername_prefix`) VALUES (?, ?, ?, ?, ?);';
            $data = [$this->number_clients, $this->ss_ip, $this->ss_port, $this->username, $this->minername_prefix];
        }

        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new SimConfigException('SimConfig::saveToDb(): SQL预处理失败');
        }

        $stat = $rs->execute($data);

        if (!$stat) {
            throw new SimConfigException('SimConfig::saveToDb(): SQL执行失败');
        }

        $this->id = $isUpdate ? (int)$id : (int)$db->lastInsertId();
        return $this->id;
    }

    public function loadFromDb($id) {
        $this->id = $id = (int) $id;

        $db = db::conn();

        $sql = 'SELECT * FROM `simconfig` WHERE `id`=?';
        $data = [$id];

        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new SimConfigException('SimConfig::saveToDb(): SQL预处理失败');
        }

        $stat = $rs->execute($data);

        if (!$stat) {
            throw new SimConfigException('SimConfig::saveToDb(): SQL执行失败');
        }

        $data = $rs->fetch(db::ass);

        if (empty($data)) {
            throw new SimConfigException("SimConfig::loadFromDb(): id={$id}的记录不存在");
        }

        $this->setClientsNumber($data['number_clients']);
        $this->setStratumServer($data['ss_ip'], $data['ss_port']);
        $this->setUserName($data['username']);
        $this->setMinerNamePrefix($data['minername_prefix']);
    }

    public function destory() {
        $sql = 'DELETE FROM `simconfig` WHERE id=?';
        $data = [$this->id];

        $db = db::conn();
        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new SimConfigException('SimConfig::destory(): SQL预处理失败');
        }

        $stat = $rs->execute($data);

        if (!$stat) {
            throw new SimConfigException('SimConfig::saveToDb(): SQL执行失败');
        }
    }

    public static function getConfigSize() {
        $sql = 'SELECT count(*) FROM `simconfig`';

        $db = db::conn();
        $rs = $db->query($sql);

        if (!$rs) {
            throw new SimConfigException('SimConfig::getConfigSize(): SQL执行失败');
        }

        $data = $rs->fetch(db::num);

        return (int) $data[0];
    }

    public static function getConfigList($offset, $size) {
        $offset = (int) $offset;
        $size = (int) $size;

        if ($offset < 0) {
            throw new SimConfigException('SimConfig::getConfigList(): 偏移量不能为负数');
        }
        
        if ($size < 1) {
            throw new SimConfigException('SimConfig::getConfigList(): 查询数量不能少于1条');
        }

        $sql = "SELECT id FROM `simconfig` ORDER BY id LIMIT {$offset},{$size}";
        
        $db = db::conn();
        $rs = $db->query($sql);

        if (!$rs) {
            throw new SimConfigException('SimConfig::getConfigList(): SQL执行失败');
        }

        $configList = [];

        while (false !== ($id = $rs->fetch(db::num))) {
            $config = new SimConfig();
            $config->loadFromDb($id[0]);
            $configList[] = $config;
        }

        return $configList;
    }
}

