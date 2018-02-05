<?php

require_once 'APIUtils.php';

abstract class AbstractRestAPI
{
    // The HTTP method of the request, either GET, POST, PUT or DELETE
    protected $method = '';

    // The Resource requested in the URI. eg: /products/2/categories/1
    protected $resource = '';

    // Any query parameters appended with the URL for a PUT Request
    protected $query_params = Array();

    //Stores the input file of the POST or the PUT request
    protected $input_file = Null;

     //Constructor
    public function __construct($request) {
        header("Content-Type: application/json");

        //Removing any slashes and the end of the requested resource and assigning it to $resource
        $this->resource = rtrim($request,'/');

        //Storing the request method in its local variable
        $this->method = $_SERVER['REQUEST_METHOD'];

        //since DELETE and PUT requests are hidden inside a POST request.
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        switch($this->method) {

        case 'POST':   //Storing JSON input into input_file variable
                       $this->input_file = file_get_contents("php://input");

                       //Condition to check whether input is in JSON format
                       if(!(APIUtils::isJson($this->input_file)))
                       {
                           $this->_response(array('error' => "Input not in correct format"),'415');
                           exit;
                       }
                       break;

         case 'DELETE':   //Storing JSON input into input_file variable
                       //$this->input_file = file_get_contents("php://input");

                       /*//Condition to check whether input is in JSON format
                       if(!(APIUtils::isJson($this->input_file)))
                       {
                           $this->_response(array('error' => "Input not in correct format"),'415');
                           exit;
                       }*/
                       break;

        case 'GET':    //Storing $_GET query parameters into query_params variable
                       $this->query_params = APIUtils::sanitizeInputs($_GET);
                       break;

        case 'PUT':    //Storing JSON input into input_file variable
                       $this->input_file = file_get_contents("php://input");

                       //Condition to check whether input is in JSON format
                       if(!(APIUtils::isJson($this->input_file)))
                       {
                           $this->_response(array('error' => "Input not in correct format"),'415');
                           exit;
                       }

                       break;

        default:       $this->_response('Invalid Method', 405);
                       break;
        }
   }

    //Controller function of the Abstract API. Calls the main controller function of the extended class
    public function processRequests() {
           $this->controllerMain();
    }

    //Function to output response
    protected function _response($data, $status = 200, $flush = false)
    {   header("HTTP/1.1 " . $status . " " . $this->getStatusMessage($status));
        header('Content-Type: application/json; charset=utf8');

        if($flush) {
          header("Connection: close\r\n");
          ignore_user_abort(true);
          header("Content-encoding: none\r\n");
          header("Access-Control-Allow-Origin: *\r\n");
          header("Content-Length: ".strlen(json_encode($data)));
        }

        echo json_encode($data);

        if($flush) {
            ob_flush();
            flush();
            fastcgi_finish_request();
        }

        if(!($flush)) {
            exit;
        }
    }

    //Function to return the status message according to the Status Code
    private function getStatusMessage($code)
    {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            415 => 'Unsupported Media Type',
            500 => 'Internal Server Error'
            );
            
            return $status[$code];
    }
}
?>