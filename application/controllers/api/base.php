<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class ApiBaseController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function index()
    {
        echo json_encode(array('status' => 'error', 'message' => 'Please, use one of methods: m1, m5, h1, d1'), JSON_PRETTY_PRINT);
    }

    protected function checkTool($tool)
    {
        if (!$tool || is_numeric($tool) || $tool == 'null')
        {
            $tool = null;
        }
        else
        {
            $tool = strtoupper($tool);
            $tool = preg_replace('/[^a-zA-Z0-9]/', '/', $tool);
        }
        return $tool;
    }
}
