<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/base.php';

class Current extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('FlowModel');
    }

    private function _index($mode, $tool = null)
    {
        $tool = $this->checkTool($tool);

        try
        {
            $data = $this->aggregate->getActualityData($tool);
            $result = array(
                'status' => 'success',
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

    public function m1($tool = null)
    {
        $this->load->model('AggregateM1', 'aggregate');
        $this->_index('m1', $tool);
    }

    public function m5($tool = null)
    {
        $this->load->model('AggregateM5', 'aggregate');
        $this->_index('m5', $tool);
    }

    public function h1($tool = null)
    {
        $this->load->model('AggregateH1', 'aggregate');
        $this->_index('h1', $tool);
    }

    public function d1($tool = null)
    {
        $this->load->model('AggregateD1', 'aggregate');
        $this->_index('d1', $tool);
    }

    public function w1($tool = null)
    {
        echo json_encode(array('status' => 'error', 'message' => 'methon W1 does not supported'), JSON_PRETTY_PRINT);
        return;
        $this->load->model('AggregateW1', 'aggregate');
        $this->_index('w1', $tool);
    }
}
