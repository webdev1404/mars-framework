<?php
/**
* The User Class
* @package Mars
*/

namespace Mars;

/**
 * The User Class
 * Encapsulates methods for working with users
 */
class User extends Item
{
    /**
     * @var string $password_cleanThe clean password
     */
    public string $password_clean = '';

    /**
     * @internal
     */
    protected static array $validation_rules = [
        'username' => 'req|username:5:100',
        'email' => 'req|email',
        'password' => 'req|password'
    ];

    /**
     * @internal
     */
    protected static string $table = 'users';

    /**
     * @internal
     */
    protected static array $ignore = ['password_clean'];

    /**
     * @internal
     */
    public function validate(array|object $data = []) : bool
    {
        if (!parent::validate($data)) {
            return false;
        }

        $ok = true;

        //check for existing username and email
        $username_exists = $this->app->db->exists($this->getTable(), ['username_crc32' => strtolower(crc32($this->username)), 'username' => $this->username]);
        if ($username_exists) {
            $this->errors->add(App::__('err_username_exists'));
            $ok = false;
        }

        $email_exists = $this->app->db->exists($this->getTable(), ['email_crc32' => strtolower(crc32($this->email)), 'email' => $this->email]);
        if ($email_exists) {
            $this->errors->add(App::__('err_email_exists'));
            $ok = false;
        }

        return $this->app->plugins->run('user_validate', $ok, $this);
    }

    /**
     * @internal
     */
    public function save() : int
    {
        $this->app->plugins->run('user_save_before', $this);

        $ret = parent::save();

        $this->app->plugins->run('user_save_after', $this);

        return $ret;
    }

    /**
     * @internal
     */
    protected function prepare()
    {
        parent::prepare();

        $this->app->plugins->run('user_prepare', $this);
    }

    /**
     * @internal
     */
    protected function process()
    {
        $this->username_crc32 = strtolower(crc32($this->username));
        $this->email_crc32 = strtolower(crc32($this->email));

        if ($this->password_clean) {
            $this->password = $this->app->security->hashPassword($this->password_clean);
        }

        if (!$this->id) {
            $this->status = 1;
            $this->activated = 0;
            $this->activation_code = $this->app->random->getString(32);
            $this->registration_timestamp = time();
            $this->registration_ip = ['function' => 'INET6_ATON', 'value' => $this->app->ip];
        }

        $this->app->plugins->run('user_process', $this);
    }
}
