<?php
class MetaDataException extends Exception {
    // just an alias
}

class MetaData {
    private $type = 'default';
    private $cache = [];
    private $keyCache = [];

    public function __construct($type) {
        $this->type = (string) $type;
        $this->cache = []; // [ key=>value ]
        $this->keyCache = []; // [ value=>key ]

        $db = db::conn();
        $sql = 'SELECT `key`,`value` FROM `metadata` WHERE `type`=? ORDER BY `sort` ASC';
        $data = [$type];

        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new MetaDataException('MetaData::__construct(): SQL预处理失败');
        }

        $stat = $rs->execute($data);

        if (!$stat) {
            throw new MetaDataException('MetaData::__construct(): SQL执行失败');
        }
        
        while (false !== ($data = $rs->fetch(db::ass))) {
            $this->cache[$data['key']] = $data['value'];
            $this->keyCache[$data['value']] = $data['key'];
        }
    }

    public function __get($key) {
        return $this->value($key);
    }

    public function value($key) {
        return $this->cache[$key];
    }

    public function key($value) {
        return $this->keyCache[$value];
    }

    public function keyValues() {
        return $this->cache;
    }

    public function valueKeys() {
        return $this->keyCache;
    }
}
