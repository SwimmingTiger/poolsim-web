<?php
class SimServiceException extends Exception {
    // just an alias
}

class SimService {
    private $config;
    private $id;

    private $daemonConfPath;
    private $serviceConfPath;
    private $logDir;

    public function __get($key) {
        return $this->$key;
    }

    public function __construct($config) {
        $this->config = $config;
        $this->id = $config->id;

        if (!is_int($this->id)) {
            throw new SimServiceException('SimService::__construct(): id不是整数');
        }

        $this->daemonConfPath = POOLSIM_DAEMON_CONF_DIR . '/poolsim-' . $this->id . '.conf';
        $this->serviceConfPath = POOLSIM_CONF_DIR . '/' . $this->id . '.conf';
        $this->logDir = POOLSIM_LOG_DIR . '/' . $this->id;
    }

    public function makeDaemonConfig() {
        $workDir = POOLSIM_ROOT_DIR;
        $process = POOLSIM_PROCESS_PATH;
		$user = POOLSIM_RUNNING_USER;

        $conf = <<<CONF
[program:poolsim-{$this->id}]
directory={$workDir}
command={$process} -c {$this->serviceConfPath} -l {$this->logDir}
autostart=true
autorestart=true
startsecs=3
startretries=100
user={$user}
redirect_stderr=true
stdout_logfile_backups=5
stdout_logfile={$this->logDir}/stdout.log
CONF;

        return $conf;
    }

    public function initWorkDir() {
        mkdir($this->logDir);

        if (!is_dir($this->logDir)) {
            throw new SimServiceException("SimService::initWorkDir(): 无法创建日志目录 {$this->logDir}");
        }
    }

    public function writeConfig() {
        $configText = $this->config->makeConfig();
        $stat = file_put_contents($this->serviceConfPath, $configText);

        if (FALSE === $stat) {
            throw new SimServiceException("SimService::writeConfig(): 配置文件 {$this->serviceConfPath} 写入失败");    
        }

        $configText = $this->makeDaemonConfig();
        $stat = file_put_contents($this->daemonConfPath, $configText);

        if (FALSE === $stat) {
            throw new SimServiceException("SimService::writeConfig(): 配置文件 {$this->daemonConfPath} 写入失败");    
        }
    }

    public function removeConfig() {
        unlink($this->daemonConfPath);
        
        if (is_file($this->daemonConfPath)) {
            throw new SimServiceException("SimService::removeConfig(): 配置文件 {$this->daemonConfPath} 删除失败");
        }

        unlink($this->serviceConfPath);
        
        if (is_file($this->serviceConfPath)) {
            throw new SimServiceException("SimService::removeConfig(): 配置文件 {$this->serviceConfPath} 删除失败");
        }
    }

    public function updateDaemon() {
        ob_start();
        system('supervisorctl update');
        return ob_get_clean();
    }

    public function startService() {
        $this->initWorkDir();
        $this->writeConfig();
        return $this->updateDaemon();
    }

    public function stopService() {
        $this->removeConfig();
        return $this->updateDaemon();
    }

    public function getStatus() {
        ob_start();
        system("supervisorctl status poolsim-{$this->id}");
        return ob_get_clean();
    }

    /**
    * @return [
    *             'status' => 'STARTING' | 'RUNNING' | 'STOPED' | 'FAULT' | 'UNKNOWN',
    *             'statusString' => $this->getStatus(),
    *             'pid' => n, // when status == 'RUNNING'
    *             'uptime' => G:i:s, // when status == 'RUNNING'
    *         ]
    */
    public function getStatusData() {
        $status = $this->getStatus();
        $data = [];

        if (false !== strpos($status, 'STARTING')) {
            $data['status'] = 'STARTING';
        }
        elseif (false !== strpos($status, 'ERROR')) {
            $data['status'] = 'STOPED';
        }
        elseif (false !== strpos($status, 'BACKOFF') || false !== strpos($status, 'FATAL')) {
            $data['status'] = 'FAULT';
        }
        elseif (false !== strpos($status, 'RUNNING')) {
            $data['status'] = 'RUNNING';

            preg_match('/pid\s*(\d+)/i', $status, $pid);
            preg_match('/uptime\s*([0-9:]+)/i', $status, $uptime);

            $data['pid'] = $pid[1];
            $data['uptime'] = $uptime[1];
        }
        else {
            $data['status'] = 'UNKNOWN';
        }

        $data['noticeString'] = preg_replace('/^[^\s]*\s*/', '', trim($status));
        $data['statusString'] = $status;

        return $data;
    }

    public function getCountData() {
        $data = $this->getStatusData();

        if ('RUNNING' == $data['status']) {
            $pid = (int) $data['pid'];
            $data['connections'] = exec(POOLSIM_CONNECTION_LOOKUP . " $pid");
            echo $x;
        }

        return $data;
    }

    public static function getAllStatus() {
        ob_start();
        system("supervisorctl status");
        return ob_get_clean();
    }

    public function clearLog() {
    	if (!is_dir($this->logDir)) {
            return;
		}

    	$dh = opendir($this->logDir);
		
		if (!$dh) {
            throw new SimServiceException("SimService::clearLog(): 日志目录 {$this->logDir} 无法打开");
        }
        
        while (false !== ($file = readdir($dh))) {
            if ('.' != $file && '..' != $file) {
                $path = $this->logDir . '/' . $file;
                unlink($path);

                if (file_exists($path)) {
                    throw new SimServiceException("SimService::clearLog(): 日志文件 {$path} 无法删除");
                }
            }
        }

        closedir($dh);
    }

    public function removeWorkDir() {
        $this->clearLog();
        rmdir($this->logDir);

        if (is_dir($this->logDir)) {
            throw new SimServiceException("SimService::removeWorkDir(): 日志目录 {$this->workDir} 无法删除");
        }
    }

    public function destory() {
        $this->stopService();
        $this->removeWorkDir();
        $this->config->destory();
    }
}

