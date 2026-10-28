<?php namespace App\Libraries;


class RestLibrary
{
		protected $config;
		protected $client;
		protected $options;
		protected $base_uri = 'http://localhost/car_wash/api/';
		 public function __construct()
	    {
	    	$this->config = config('CustomConfig');

	        $this->client = \Config\Services::curlrequest();
	        $this->client->setAuth($this->config->httpUser, $this->config->httpPass, $this->config->httpAuth);
	        $this->client->setHeader('X-API-Key', $this->config->apiKey);
	        
	        $this->options = [];
        	$this->options['baseURI'] = $this->base_uri;
        	$this->options['baseURI'] = $this->base_uri;
        	$this->options['http_errors'] = false;
        	$this->options['verify'] = false;


	    }
        public function get($uri, $params = array())
        {  
        	$this->options['query'] = $params;
        	$response = $this->client->request('GET', $uri, $this->options);
        	return json_decode($response->getBody());
        }
        //public function post($uri, $params = array(), $format = NULL,$upload=false)
        public function post($uri, $params = array(),$upload=null)
        {               if(!empty($upload)){
                            $this->client->setHeader('Content-Type', 'multipart/form-data');
                            $this->options['multipart'] = $params;
                        }else{
                        $this->client->setHeader('Content-Type', 'application/x-www-form-urlencoded');
                            $this->options['form_params'] = $params;    
                        }
			
        	$response = $this->client->request('POST', $uri, $this->options);
        	return json_decode($response->getBody());
        }
        
		
}