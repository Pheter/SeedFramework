<?php

class Request {
    
    public $path;
    public $method;
    public $data;
    public $resource;
    public $path_parameters;
    
    public function __construct() {
        
        $this->path = $this->determinePath();
        $this->method = $this->determineMethod();
        $this->data = $this->determineData();
    }
    
    
    private function determinePath() {
        
        $url = parse_url($_SERVER['REQUEST_URI']);
        return $url['path'];
    }
    
    
    private function determineMethod() {
        
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        
        //HTML form fix (enable usage of methods other than GET and POST)
        if ($method == 'POST') {
            if (isset($_POST['_method'])) {
                $method = strtoupper($_POST['_method']);
            }
        }
        
        unset($_POST['_method']);
        
        return $method;
    }
    
    
    private function determineData() {
        
        switch ($this->method) {
            case 'GET':
                $data = $_GET;
                break;
            default:
                parse_str(file_get_contents('php://input'), $data);
                break;
        }
        
        return $data;
    }
}
