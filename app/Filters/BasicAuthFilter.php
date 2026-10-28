<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class BasicAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    { 
        $apikey = $_SERVER['HTTP_X_API_KEY'] ?? NULL;
        $username = $_SERVER['PHP_AUTH_USER'] ?? NULL;
        $password = $_SERVER['PHP_AUTH_PW'] ?? NULL;
        if(empty($apikey))
        {
            die('Api key is required.');
        }else{
            if($apikey != 'twentytwo22bookmymatchtwentytwo22'){
                die('Invalid API Key');
            }
        }
        if(empty($username))
        {
        	//die('Wrong Authentication');
        }else{
        	if($username != 'admin' || $password != 'bookmymatch@@22'){
        		die("Client isn't authorized");
        	}
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}