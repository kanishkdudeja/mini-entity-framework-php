<?php

abstract class Entity
{
    protected $id;
    protected $tableName;
    protected $db;

    public function __construct(DBWrapper $db, $tableName)
    {
         $this->db = $db;
         $this->tableName = $tableName;
    }

    protected function updateSomeValuesInDB($updateParams, $conditionParams = Array())
    {
        if(count($conditionParams) == 0)
        {
            $conditionParams = Array();
            $conditionParams['id'] = $this->id;
        }

        $count = $this->db->update($this->tableName, $updateParams, $conditionParams);
        return (($count > 0) ? TRUE : FALSE);
    }

    protected function insertIntoDB($insertParams, $insertIgnore = false)
    {
        $lastInsertedID = '';

        if($insertIgnore) {
            $lastInsertedID = $this->db->insert($this->tableName, $insertParams, true);
        }
        else {
            $lastInsertedID = $this->db->insert($this->tableName, $insertParams);
        }

        if($lastInsertedID)
        {
            $this->id = $lastInsertedID;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    protected function fetchFromDB($id)
    {
        $conditionParams = Array();
        $conditionParams['id'] = $id;

        $fields = $this->db->select($this->tableName, '*', $conditionParams);

        if(!$fields) {
            return false;
        }

        $fields = APIUtils::fetch_Single_Array($fields);

        if(!((is_array($fields)) && (count($fields) > 0))) {
            return false;
        }

        return $fields;
    }

    protected function fetchFromDBByParam($name, $value)
    {
        $conditionParams = Array();
        $conditionParams[$name] = $value;

        $fields = $this->db->select($this->tableName, '*', $conditionParams);

        if(!$fields) {
            return false;
        }

        $fields = APIUtils::fetch_Single_Array($fields);

        if(!((is_array($fields)) && (count($fields) > 0))) {
            return false;
        }

        return $fields;
    }

    protected function getIdentity()
    {
        return $this->id;
    }

    protected function select($fields = '*', $conditionParams = Array(), $groupBy = NULL, $limit = '', $sort=NULL, $fetchStyle = PDO::FETCH_ASSOC, $isIterationEnabled = false)
    {
        $result = $this->db->select($this->tableName, $fields, $conditionParams, $groupBy, $limit, $sort, $fetchStyle, $isIterationEnabled);

        if(empty($result))
        {
            return FALSE;
        }
        else
        {
            return $result;
        }
    }

    protected function delete($conditionParams = Array())
    {
        if(count($conditionParams) == 0)
        {
            $conditionParams = Array();
            $conditionParams['id'] = $this->id;
        }

        $count = $this->db->delete($this->tableName, $conditionParams);

        return (($count > 0) ? TRUE : FALSE);
    }

}

?>