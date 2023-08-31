<?php

class User {

    private $_db,
            $_data,
            $_sessionName,
            $_cookieName,
            $_isLoggedIn = false;

    function __construct($user = null)
    {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('xSession/session_name');

        $this->_cookieName = Config::get('xRemember/cookie_name');
        if (!$user)
        {
            if (Session::exists($this->sessionName()))
            {
                $user = Session::get(Config::get('xSession/session_name'));
                if ($this->find($user))
                {
                    $this->_isLoggedIn = true;
                }
                else
                {
                    //process logout
                }
            }
        }
        else
        {
            $this->find($user);
        }
    }

    public function create($field = [])
    {
        if (!$this->_db->insert('users', $field))
        {
            throw new Exception('There was a problem creating an account');
        }
    }

    public function find($user = null)
    {
        if ($user)
        {

            $field = (is_numeric($user)) ? 'id' : 'username';

            $data = $this->_db->get('users', [$field, '=', $user]);
            if ($data->count())
            {
                $this->_data = $data->result();
                return true;
            }
        }

        return false;
    }

    public function update($field = [], $id = null)
    {
        if ($field)
        {

            if ($this->_db->update('users', $id, $field))
            {
                //$this->_data = $data->result();
                return true;
            }
        }
        return false;
    }

    public function login($user = null, $pass = null, $remember = false)
    {
        if (!$user && !$pass && $this->exists())
        {
            //Log in   
            Session::put($this->_sessionName, $this->data()->id);
        }
        else
        {
            if ($this->find($user))
            {
                $password = Hash::make($pass, $this->data()->salt);
                if ($this->data()->password == $password)
                {

                    Session::put($this->_sessionName, $this->data()->id);

                    if ($remember)
                    {
                        $callIdentity = Hash::unique();

                        $callCheck = $this->_db->get('users_session', ['user_id', '=', $this->data()->id]);

                        if (!$callCheck->count())
                        {

                            $this->_db->insert('users_session', ['user_id' => $this->data()->id, 'hash' => $callIdentity]);
                            echo $this->data()->id;
                        }
                        else
                        {

                            $callIdentity = $callCheck->result()->hash;
                        }

                        Cookies::put($this->_cookieName, $callIdentity, Config::get('xRemember/cookie_expiry'));
                    }

                    return true;
                }
                else
                {
                    //  echo 'password incorrect';
                    return false;
                }
            }
        }

        return false;
    }

    public function data()
    {
        return $this->_data;
    }

    private function sessionName()
    {
        return $this->_sessionName;
    }

    private function cookieName()
    {
        return $this->_cookieName;
    }

    private function password()
    {
        return $this->_data->password;
    }

    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }

    public function hasPermission($key)
    {
        $group = $this->_db->get('groups', ['id', '=', $this->data()->group_id]);
 

        if ($group->count())
        {
            $permission = json_decode($group->result()->permissions, true);
       
            if ($permission[$key] == true)
            {
                return true;
            }
        }
        return false;
    }

    public function logout()
    {
        $this->_db->delete('users_session', ['user_id', '=', $this->data()->id]);
        Session::delete($this->_sessionName);
        Cookies::delete($this->cookieName());
    }

    public function exists()
    {
        return (empty($this->data)) ? true : false;
    }

}
