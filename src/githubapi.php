<?php

//namespace diversen;

//use diversen\mycurl;


//include_once "mycurl.php";

/**
 * contains simple class for using oauth with github
 * @package githubapi
 */

/**
 * Simple class for using the github oauth api:
 * 
 * 
 * 
 * 
 * @package githubapi
 */
class githubapi {
    /**
     *
     * @var array $errors holding errors
     */
    public $errors = array ();
    
    /**
     * @var holding return code
     * @var string 
     */
    public $returnCode = null;
    /**
     * We need a github OAuth login url from configuration
     * @param array $config e.g. 
     * 
     * <code>
     * $config = array (
     *     'redirect_uri' => 'http://localhost:8080/callback.php',
     *     'client_id' => 'app id',
     *     'state' =>  md5(uniqid()),
     *     'scope' => 'user'
     * );
     * </code>
     * 
     * If you don't set scope you can only get users basic profile info,
     * but you can still use it as e.g. a login method.
     *  
     * @return string $url a github url where you can obtain users accept of 
     *                     using his account according to scope
     */
    public function getAccessUrl ($config) {
        $_SESSION['state'] = $config['state'];     
        $url = 'https://github.com/login/oauth/authorize';
        $query =  http_build_query($config);
        $url.= '?' . $query;    
        return $url;
    }

    /**
     * Sets the access token in a session variable, which
     * then can be used when calling the api
     * @param array $post e.g. 
     * 
     * <code>
     * $post = array (
     *     'redirect_uri' => 'http://localhost:8080/callback.php',
     *     'client_id' => 'app_id',
     *     'client_secret' => 'app_secret',
     * );
     * </code>
     * 
     * @return boolean $res true on success and false on failure
     *                 any errors will be stored in githubapi::$errors
     */
    public function setAccessToken ($post) {
        if (isset($_GET['error'])) {
            $this->errors[] = $_GET['error'];
            return false;
        }
        
        if (isset($_GET['code'])) {
            $c = new mycurl('https://github.com/login/oauth/access_token');
            $post['code'] = $_GET['code'];
            $post['state'] = $_SESSION['state'];

            $c->setPost($post);
            $c->createCurl();
            $resp = $c->getWebPage();
            
            parse_str($resp, $ary);
            
            if (isset($ary['access_token']) && isset($ary['token_type']) && $ary['token_type'] == 'bearer') {
                $_SESSION['access_token'] = $ary['access_token'];
                return true;
            } else {
                $this->errors[] = "No access token returned";
                return false;
            }
        }
        return false;
    }

    /**
     * Make an API call. For all 
     * 
     * @see http://developer.github.com/v3/
     * 
     * @param string $command e.g "/users"
     * @param string $request e.g "POST" or PATCH, DELETE - if empty it is a GET
     * @param array $post vaiables $_POST variables to send
     * @param boolean $json should we return output as json. Default is false
     * @return boolean|array false if failure. Else: $ary response from github server
     */
    public function apiCall ($command, $request = null, $post = null, $json = false, $quantidadeItens = null) {
        if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
            $this->errors[] = 'No valid token';
            return false;
            
        }

        // se não tiver que ter paginação
        if($quantidadeItens == null)
        {

            $end_point = 'https://api.github.com';
            $command = $end_point . "$command";
            $command.= "?access_token=$_SESSION[access_token]";
            $command.= "&per_page=100";

            $c = new mycurl($command);
            if (isset($request)) {
                $c->setRequest($request);
            }
            if (isset($post)) {
                $json = json_encode($post);
                $c->setPost($json);
            }
               
            $c->createCurl();
            $resp = $c->getWebPage();
            $this->returnCode = $c->getHttpStatus();
            if ($json) {
                return $resp;
            } else {
                $ary = json_decode($resp, true);  
                return $ary;
            }
            die();
        }
        // Se tiver que ter paginação
        else
        {

            $totalPaginas = ceil($quantidadeItens/100);
            $retorno      = array();

            $command_original = $command;
            $request_original = $request;
            $post_original    = $post;

            //Para cada página, faz uma chamada diferente
            for($i=1; $i<=$totalPaginas; $i++)
            {

                    $command = $command_original;
                    $request = $request_original;
                    $post    = $post_original;

                    $end_point = 'https://api.github.com';
                    $command = $end_point . "$command";
                    $command.= "?access_token=$_SESSION[access_token]";
                    $command.= "&page=".$i;
                    $command.= "&per_page=100";

                    $c = new mycurl($command);
                    if (isset($request)) {
                        $c->setRequest($request);
                    }
                    if (isset($post)) {
                        $json = json_encode($post);
                        $c->setPost($json);
                    }
                       
                    $c->createCurl();
                    $resp = $c->getWebPage();
                    $this->returnCode = $c->getHttpStatus();
                    if ($json) {
                        return $resp;

                    } else {
                        $ary        = json_decode($resp, true); 
                        $retorno    = array_merge($retorno, $ary);
                    }
            }
            return $retorno;
        }


    }


    /**
     * Make an API call. For all 
     * 
     * @see http://developer.github.com/v3/
     * 
     * @param string $command e.g "/users"
     * @param string $request e.g "POST" or PATCH, DELETE - if empty it is a GET
     * @param array $post vaiables $_POST variables to send
     * @param boolean $json should we return output as json. Default is false
     * @return boolean|array false if failure. Else: $ary response from github server
     */
    public function apiCallSearch ($command, $request = null, $post = null, $json = false) {
        if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
            $this->errors[] = 'No valid token';
            return false;
            
        }

        $end_point = 'https://api.github.com';
        $command = $end_point . "$command";
        //$command.= "?access_token=$_SESSION[access_token]";
        //$command.= "&per_page=100";

        $c = new mycurl($command);
        if (isset($request)) {
            $c->setRequest($request);
        }
        if (isset($post)) {
            $json = json_encode($post);
            $c->setPost($json);
        }
           
        $c->createCurl();
        $resp = $c->getWebPage();
        $this->returnCode = $c->getHttpStatus();
        if ($json) {
            return $resp;
        } else {
            $ary = json_decode($resp, true);  
            return $ary;
        }
        die();
    }


    /**
     * Make an API call. For all 
     * 
     * @see http://developer.github.com/v3/
     * 
     * @param string $command e.g "/users"
     * @param string $request e.g "POST" or PATCH, DELETE - if empty it is a GET
     * @param array $post vaiables $_POST variables to send
     * @param boolean $json should we return output as json. Default is false
     * @return boolean|array false if failure. Else: $ary response from github server
     */
    public function apiCallSearch2 ($command, $request = null, $post = null, $json = false) {
        if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
            $this->errors[] = 'No valid token';
            return false;
            
        }

        $end_point = 'https://api.github.com';
        $command = $end_point . "$command";
        //$command.= "?access_token=$_SESSION[access_token]";
        $command.= "&per_page=100";

        $c = new mycurl($command);
        if (isset($request)) {
            $c->setRequest($request);
        }
        if (isset($post)) {
            $json = json_encode($post);
            $c->setPost($json);
        }
           
        $c->createCurl();
        $resp = $c->getWebPage();
        $this->returnCode = $c->getHttpStatus();
        if ($json) {
            return $resp;
        } else {
            $ary = json_decode($resp, true);  
            return $ary;
        }
        die();
    }    


}
