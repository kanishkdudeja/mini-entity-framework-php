<?php

//including classes
require_once 'Environment.php';
require_once 'AbstractRestAPI.php';
require_once 'DBWrapper.php';
require_once 'Entity.php';
require_once 'User.php';

//extending the class AbstractRestAPI
class API extends AbstractRestAPI
{
    //Database object
    private $db;
    
    //Constructor
    public function __construct($request, $db)
    {
        //Calling the parent constructor
        parent::__construct($request);

        //Assigning the database parameter object to its own local object
        $this->db = $db;
    }

    //Main controller function
    public function controllerMain() {

        //Generates a resource hierarchy array from the resource string
        //Example:- /categories/2 to Array([0]->categories,[1]->2)
        $urlHierarchy = explode('/', $this->resource);

        //Storing count of elements in array in variable $count
        $count = count($urlHierarchy);

        if(isset($urlHierarchy[$count-1]) && $urlHierarchy[$count-1] == 'user') {
            switch($this->method) {
                case 'GET':         // add function here for getting user details
                                    break;
                case 'DELETE':      // add function here for deleting a user
                                    break;
                case 'PUT':         // add function here for updating a user
                                    break;
                default   :         //Any other method not allowed
                                    $this->_response(array('error' => "Method not allowed"),'405');
                                    break;
            }
        }
    }
}

try {

    //Making a new database wrapper Object
    $db = new DBWrapper();

    //Creating API Object and passing the parameter received from HTACCESS, the database object and the resource object
    $API = new API($_REQUEST['request'], $db);

    //calling the authenticate function to authenticate the user
    $customerAuthentication = $API->authenticate();

    $API->processRequests();
}

catch (Exception $e) {
    // log $e->getMessage() somewhere

    echo json_encode(Array('status' => 'failure'));
}

?>
