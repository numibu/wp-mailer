<?php namespace mailer\base;

/**
 * 
 */

abstract class BaseActivRecord{

    public function save()
    {
        if($this->primaryKey()){
            $this->insert();
        }else{
            $this->update();
        }
    }

    public function remove()
    {
        $this->delete();
    }

    abstract public function prymaryKey();
    abstract protected function select();
    abstract protected function insert();
    abstract protected function update();
    abstract protected function delete();
}
