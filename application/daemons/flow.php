<?php
class FlowDaemon
{
    private $service;
    private $port;
    private $socket;
    private $model;

    public function __construct(array $config)
    {
        $this->service = $config['service'];
        $this->port    = $config['port'];
        $this->model   = $config['model'];
    }

    private function openSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            echo "Не удалось выполнить socket_create(): причина: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "socket_create - OK.\n";
        }

        echo "Пытаемся соединиться с '{$this->service}', порт '{$this->port}'. ";
        $result = socket_connect($this->socket, $this->service, $this->port);
        if ($result === false) {
            echo "Не удалось выполнить socket_connect().\nПричина: ($result) " . socket_strerror(socket_last_error($this->socket)) . "\n";
        } else {
            echo "OK.\n";
        }
        return $this->socket;
    }

    public function run()
    {
        if (! $this->openSocket())
        {
            return;
        }

        $i= 0;
        while ($out = socket_read($this->socket, 2048))
        {
            foreach (array_filter(explode("\n", $out)) as $outItem)
            {
                if (preg_match_all('/([\w]+)=([\w\/\.:]+)/', $outItem, $match))
                {
                    $data = array();
                    foreach ($match[1] as $key => $type)
                    {
                        $data[$type] = $match[2][$key];
                    }
                    try {
                        $this->model->addRow($data);
                        echo ($i++) . "  " . $outItem . "\n";
                    } catch (FlowAddRowException $e) {
                        echo $e->getMessage() . " - " . $outItem . "\n";
                    }
                }
                else
                {
                    echo "Unrecogrized ". $outItem . "\n";
                }
            }
        }
    }

    public function stop()
    {
        if ($this->socket)
        {
            echo "Закрываем сокет...";
            socket_close($this->socket);
            echo "OK.\n\n";
        }
        $this->socket = null;
    }
}
