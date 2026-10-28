<?php namespace App\Password;
/**
 * Validate the password
 *
 * @param string $password
 * @param string $error
 *
 * @return bool
 */
class PasswordRules
{
	/**
	 * @var AuthConfig
	 */
	protected $config;

	public function __construct()
	{
		
	}
	public function strong_password(string $password, string &$error = null): bool
	{
	    $password = trim($password);

		$regex_lowercase = '/[a-z]/';
		$regex_uppercase = '/[A-Z]/';
		$regex_letter = '/[A-Za-z]/';
		$regex_number = '/[0-9]/';
		$regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

		if (empty($password))
		{
			$error = 'The password field is required.';

			return false;
		}

		if (strlen($password) < 6)
		{
			$error = 'The password field must be at least 6 characters in length.';

			return false;
		}

		if (strlen($password) > 32)
		{
			$error = 'The password field cannot exceed 32 characters in length.';

			return false;
		}
		/*

		if (preg_match_all($regex_lowercase, $password) < 1)
		{
			$error = 'The password field must be at least one lowercase letter.';

			return false;
		}

		if (preg_match_all($regex_uppercase, $password) < 1)
		{
			$error = 'The password field must be at least one uppercase letter.';

			return false;
		}
        */
        if (preg_match_all($regex_letter, $password) < 1)
		{
			$error = 'The password field must be at least one letter.';

			return false;
		}

		if (preg_match_all($regex_number, $password) < 1)
		{
			$error = 'The password field must have at least one number.';

			return false;
		}

		if (preg_match_all($regex_special, $password) < 1)
		{
			$error = 'The password field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~');

			return false;
		}

		

		return true;
	}
	
}