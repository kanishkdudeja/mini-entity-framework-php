<?php

class User extends Entity
{
    private $role;
    private $name;
    private $email;
    private $password_hash;
    private $created_time_stamp;
    private $last_login_time_stamp;
    private $last_modified_time_stamp;

    public function __construct(DBWrapper $db) {
        parent::__construct($db, 'users');
    }

    public function setById($id) {
        $fields = $this->fetchFromDB($id);
        
        foreach($fields as $key=>$value)
        {
            $this->$key = $value;
        }
    }

    public function setByEmail($email) {
        $conditionParamsArray = Array();
        $conditionParamsArray['email'] = $email;

        $result = $this->select('*', $conditionParamsArray);

        if(!$result)
        {
            return false;
        }

        $fields = APIUtils::fetch_Single_Array($result);

        if(!((is_array($fields)) && (count($fields) > 0)))
        {
            return false;
        }

        foreach($fields as $key=>$value)
        {
            $this->$key = $value;
        }

        return true;
    }

    public function setValues($array) {
        foreach($array as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function insertIntoDatabase() {
        $insertParams = Array();
        $insertParams['name'] = $this->name;
        $insertParams['email'] = $this->email;
        $insertParams['role'] = $this->role;
        $insertParams['password_hash'] = $this->password_hash;
        $insertParams['created_time_stamp'] = gmdate('Y-m-d H:i:s');
        $insertParams['last_modified_time_stamp'] = gmdate('Y-m-d H:i:s');

        return $this->insertIntoDB($insertParams);
    }

    public function getId()
    {
        return $this->getIdentity();
    }
    
    public function getEmail() {
        return $this->email;
    }

    public function getRole(){
        return $this->role;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getCreatedTimeStamp()
    {
        return $this->created_time_stamp;
    }

    public function getLastLoginTimeStamp() {
        return $this->last_login_time_stamp;
    }

    public function isUserPasswordCorrect($email, $password) {
        $conditionParamsArray = Array();
        $conditionParamsArray['email'] = $email;

        $result = $this->select('password_hash', $conditionParamsArray);

        if(!$result)
        {
            return false;
        }

        $fields = APIUtils::fetch_Single_Array($result);

        if(!((is_array($fields)) && (count($fields) > 0)))
        {
            return false;
        }

        $hashStoredInDb = $fields['password_hash'];

        if (password_verify($password, $hashStoredInDb)) {
            return true;
        } else {
            return false;
        }
    }

    public function login($email, $password) {
        if(!($this->isUserPasswordCorrect($email, $password))) {
            return false;
        }

        $conditionParamsArray = Array();
        $conditionParamsArray['email'] = $email;

        $updateArray = Array();
        $updateArray['last_login_time_stamp'] = gmdate('Y-m-d H:i:s');

        $this->updateSomeValuesInDB($updateArray, $conditionParamsArray);

        return true;
    }

    public function changePassword($password) {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);

        $conditionParamsArray = Array();
        $conditionParamsArray['email'] = $this->email;

        $updateArray = Array();
        $updateArray['password_hash'] = $this->password_hash;

        return $this->updateSomeValuesInDB($updateArray, $conditionParamsArray);
    }

    public function updateValuesInDB($updateParams) {
        $this->setValues($updateParams);

        return $this->updateSomeValuesInDB($updateParams,Array());
    }

    public function deleteUser() {
        return $this->delete();
    }

}

?>