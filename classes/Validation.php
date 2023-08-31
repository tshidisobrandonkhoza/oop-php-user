<?php

class Validation {

    private $_passed = false,
            $_errors = [],
            $_db = null;

    function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function check($method, $items = [])
    {
        foreach ($items as $item => $rules)
        {
            foreach ($rules as $rule => $condition)
            {
                $value = trim($method[$item]);
                if (empty($value) && $rule === 'required')
                {
                    $this->arrayError("{$item} is  required");
                }
                $this->switchError($method, $item, $rule, $value, $condition);
            }
        }
        if (empty($this->_errors))
        {
            $this->_passed = true;
        }
        else
        {
            return $this;
        }
    }

    public function arrayError($error = '')
    {
        return $this->_errors[] = $error;
    }

    public function errors()
    {
        return $this->_errors;
    }

    public function passed()
    {
        return $this->_passed;
    }

    private function switchError($method, $item, $rule, $value, $condition)
    {
        switch ($rule)
        {
            case 'min':
                if (strlen($value) <= $condition)
                {
                    $this->arrayError("{$item} should be greater than {$condition}");
                }
                break;
            case 'max':
                if (strlen($value) >= $condition)
                {
                    $this->arrayError("{$rule} should be less than {$condition}");
                }
                break;
            case 'matches':
                if ($value != $method[$condition])
                {
                    $this->arrayError("{$item} should match {$condition}");
                }
                break;
            case 'unique':
                $check = $this->_db->get($condition, [$item, '=', $value]);
                if ($check->count())
                {
                    $this->arrayError("{$item} already exist");
                }
                break;
        }
    }

}
