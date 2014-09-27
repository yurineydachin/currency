<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/base.php';

class History extends ApiBaseController
{
    private function _index($mode, $tool, $start, $finish)
    {
        $start = strtotime($start);
        $finish = strtotime($finish);
        $tool = $this->checkTool($tool);

        try
        {
            $params = array(
                'start'  => $start,
                'finish' => $finish,
                'tool'   => $tool,
            );
            $data = $this->aggregate->getDataSaved($params);
            $result = array(
                'status' => 'success',
                'start'  => date('Y-m-d H:i:s', $start),
                'finish' => date('Y-m-d H:i:s', $finish),
                'count'  => count($data),
                'data'   => $data,
            );
        }
        catch (Exception $e)
        {
            $result = array(
                'status'  => 'exception',
                'class'   => get_class($e),
                'message' => $e->getMessage(),
            );
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function m1($tool, $start, $finish)
    {
        $this->load->model('AggregateM1', 'aggregate');
        $this->_index('m1', $tool, $start, $finish);
    }

    public function m5($tool, $start, $finish)
    {
        $this->load->model('AggregateM5', 'aggregate');
        $this->_index('m5', $tool, $start, $finish);
    }

    public function h1($tool, $start, $finish)
    {
        $this->load->model('AggregateH1', 'aggregate');
        $this->_index('h1', $tool, $start, $finish);
    }

    public function d1($tool, $start, $finish)
    {
        $this->load->model('AggregateD1', 'aggregate');
        $this->_index('d1', $tool, $start, $finish);
    }

    public function w1($tool, $start, $finish)
    {
        echo json_encode(array('status' => 'error', 'message' => 'methon W1 does not supported'), JSON_PRETTY_PRINT);
        return;
        $this->load->model('AggregateW1', 'aggregate');
        $this->_index('w1', $tool, $start, $finish);
    }
}
