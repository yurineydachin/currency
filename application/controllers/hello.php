<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hello extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -  
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function empty_page()
    {
        echo __METHOD__ . "\n";
    }
    public function index()
    {
        $this->output->enable_profiler(TRUE);
        //echo "Hello World!\n";
        $data = array(
            'param1' => 'Some text are here',
            'param2' => 'Just a test',
        );
        $this->load->view('hello_world', $data);
    }
    public function world($param1, $param2)
    {
        echo "Hello World2!\n";
        echo "<br/>\n";
        echo "param1:$param1, param2:$param2\n";
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
