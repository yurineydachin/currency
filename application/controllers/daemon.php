<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/../daemons/flow.php';

class Daemon extends CI_Controller
{
    const MODE_START   = 'start';
    const MODE_STOP    = 'stop';
    const MODE_RESTART = 'restart';
    const MODE_STATUS  = 'status';

    const PID_FILE_FLOW      = '/tmp/daemon_flow.pid';
    const PID_FILE_AGGREGATE = '/tmp/daemon_aggregate.pid';

    public function __construct()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            die('Command Line Only!');
        }
        parent::__construct();
    }

    public function index()
    {
        echo "Use one of this methods:\n";
        echo "0. daemon - Show this help\n";
        echo "1. daemon/flow/[start|stop|restart|status]\n";
        echo "2. daemon/aggregate\n";
        echo "3. daemon/dropaggregate/(m1|m5|h1)\n";
    }

    public function flow($mode = self::MODE_START)
    {
        $this->_checkDaemon(self::PID_FILE_FLOW, $mode);

        $this->load->database();
        $this->load->model('FlowModel');

        $config = array(
            'service' => '178.62.145.164',
            'port'    => '10000',
            'model'   => $this->FlowModel,
        );

        if ($mode == self::MODE_START)
        {
            $flowDaemon = new FlowDaemon($config);
            $flowDaemon->run();
            $flowDaemon->stop();
        }
    }

    public function aggregate()
    {
        $this->load->database();
        $this->load->model('FlowModel');
        $this->load->model('AggregateM1');
        $this->load->model('AggregateM5');
        $this->load->model('AggregateH1');
        $this->load->model('AggregateD1');

        $dataM1 = $this->FlowModel->aggregate('m1');
        $this->AggregateM1->update($dataM1);
        $this->AggregateM5->update($dataM1);
        $this->AggregateH1->update($dataM1);
        $this->AggregateD1->update($dataM1);

        $lastItem = array_pop($dataM1);
        $maxId = 0;
        foreach ($lastItem as $tool => $toolData)
        {
            $maxId = max($maxId, $toolData['max_id']);
        }
        $dataM1 = $this->FlowModel->deleteBeforeId($maxId);
    }

    public function dropaggregate($mode = null)
    {
        $this->load->database();
        if ($mode == 'm1')
        {
            $this->load->model('AggregateM1');
            $this->AggregateM1->dropOldData();
        }
        elseif ($mode == 'm5')
        {
            $this->load->model('AggregateM5');
            $this->AggregateM5->dropOldData();
        }
        elseif ($mode == 'h1')
        {
            $this->load->model('AggregateH1');
            $this->AggregateH1->dropOldData();
        }
        else
        {
            echo "Nothing to do for mode '$mode'\n";
        }
    }

    private function _checkDaemon($pidFile, $mode)
    {
        echo date("Y-m-d H:i:s\n");
        if ($mode == self::MODE_STATUS)
        {
            if ($pid = $this->_getPidFromFile($pidFile))
            {
                if ($this->_checkPid($pid))
                {
                    die("Daemon is runnig: $pid\n");
                }
                else
                {
                    unlink($pidFile);
                    die("Daemon is not runnig\n");
                }
            }
            else
            {
                die("Daemon is not runnig. File $pidFile does not exist\n");
            }
        }
        elseif ($mode == self::MODE_START)
        {
            if (($pid = $this->_getPidFromFile($pidFile)) && $this->_checkPid($pid))
            {
                die("Daemon is runnig: $pid. Exit\n");
            }

            $pid = posix_getpid();
            $this->_setPidFile($pidFile, $pid);
            echo "Run daemon: $pid\n";
        }
        elseif ($mode == self::MODE_STOP)
        {
            if (($pid = $this->_getPidFromFile($pidFile)) && $this->_checkPid($pid))
            {
                exec("kill -9 $pid");
                die("Daemon was spopped: $pid\n");
            }
            else
            {
                die("Daemon is not runnig: $pid\n");
            }
        }
        elseif ($mode == self::MODE_RESTART)
        {
            if (($pid = $this->_getPidFromFile($pidFile)) && $this->_checkPid($pid))
            {
                exec("kill -9 $pid");
                echo "Stop daemon: $pid\n";
            }
            $pid = posix_getpid();
            $this->_setPidFile($pidFile, $pid);
            echo "Run daemon: $pid\n";
        }
        else
        {
            die("Unknown command '$mode'. Exit\n");
        }
    }

    private function _setPidFile($pidFile, $pid)
    {
        file_put_contents($pidFile, $pid);
    }

    private function _getPidFromFile($pidFile)
    {
        return file_exists($pidFile) ? (int) file_get_contents($pidFile) : null;
    }

    private function _checkPid($pid)
    {
        $command = "ps au | grep ' $pid .*cli.php' | grep -v grep";
        $output = exec($command);
        echo "$command\n";
        echo "_checkPid($pid) -> $output\n";
        return $output;
    }
}
