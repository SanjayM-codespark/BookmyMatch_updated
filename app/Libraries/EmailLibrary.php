<?php 
namespace App\Libraries;
use Config\Email;
 

class EmailLibrary
{
	protected $custom;
  /**
   * @var string
   */

	public function __construct()
	{
		$this->custom = config('Custom');
	}
 
	/**
     * Sends an activation email
     *
     * @param $user
     *
     * @return bool
     */
    public function send($user = null): bool
    {
        $email = service('email');
        $config = new Email();

        $sent = $email->setFrom($config->fromEmail, $config->fromName)
              ->setTo($user['email'])
              ->setSubject($user['subject'])
              ->setMessage($user['message'])
              ->setMailType('html')
              ->send();

        if (! $sent)
        {
            return false;
        }

        return true;
    }
}