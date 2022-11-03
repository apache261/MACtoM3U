<?php
require 'vendor/autoload.php';
use Curl\Curl;

class MacToM3U{
    private $isDebug;
    private  $baseUrl;
    private  $mainUrl;
    private  $urlEncodemac;
    private  $token;
    private $curl;
    private $profileInfo;
    private $linkInfo;
    private $vodInfo;
    private $vodInfoToken;
    private $username;
    private $password;

    
    public function __construct(String $baseUrl, String $mac)
    {
        $isDebug = true;
        $this->curl = new Curl();
        $this->mainUrl = $baseUrl;
        $this->baseUrl = $baseUrl.'/portal.php';
        $this->urlEncodemac = urlencode($mac);
        $this->token  = '';
       
    }
    private function debugPrint($msg):void{
        if($this->isDebug === true){print($msg.'\n');}
        
    }


    public function APISender(array $query=array()):bool{
        if(!$this->curl){return false;}
        $this->curl->setUserAgent('Mozilla/5.0');
        $this->curl->setHeader('Authorization','Bearer '.$this->token);
        $this->curl->setCookie('mac',$this->urlEncodemac);
        $this->curl->setCookie('stb_lang', 'en');
        $this->curl->setCookie('timezone', 'Europe%2FParis');
        if(count($query)>0){
            $this->curl->get($this->baseUrl,$query);
        }else{
            $this->curl->get($this->baseUrl);
        }

        return !$this->curl->error;
    }
    /**
     * {"js":{"token":string}}
     */
    public function getToken():bool{
        $query = array(
            'action'=>'handshake',
            'type'=>'stb',
            'token'=>''
        );
        $sendRequest = $this->APISender($query);

        if(!$sendRequest){
            $this->debugPrint ("Failed to Get Token");
          return false;
        }
        $this->token = json_decode($this->curl->response)->js->token;
        return strlen($this->token) > 0;
    }

    
    public function getProfile():bool{
        $query = array(
            'action'=>'get_profile',
            'type'=>'stb'
        );
        $sendRequest = $this->APISender($query);
        if(!$sendRequest){
            $this->debugPrint ("Failed to Get Profile");
            return false;
          }
          $this->profileInfo = (object) json_decode($this->curl->response);
          return count($this->profileInfo) > 0;
    }
    public function getOrderedVODList():bool{
        $query = array(
            'action'=>'get_ordered_list',
            'type'=>'vod',
            'p'=>1,
            'JsHttpRequest'=>'1-xml'
        );
         $sendRequest = $this->APISender($query);
        if(!$sendRequest){
            $this->debugPrint ("Failed to VOD List");
            return false;
          }
          $this->vodInfo = json_decode($this->curl->response);
          $this->vodInfoToken = $this->vodInfo->js->data[1]->cmd;
          return strlen($this->vodInfoToken) >0;
    }
    public function getLink(){
        $query = array(
            'action'=>'create_link',
            'type'=>'vod',
            'cmd'=>$this->vodInfoToken,
            'JsHttpRequest'=>'1-xml'
        );
        $sendRequest = $this->APISender($query);
        if(!$sendRequest){
            $this->debugPrint ("Failed to Get Link");
            return false;
          }
          $this->linkInfo = json_decode($this->curl->response);
          $tmp = explode('/',$this->linkInfo->js->cmd);
          $userIndex = count($tmp) - 3;
          $passIndex = count($tmp) - 2;

          $this->username = $tmp[$userIndex];
          $this->password = $tmp[$passIndex];
          return count($tmp)>0;
    }

    public function getCredentials():string{
        return $this->mainUrl.'/get.php?username='.$this->username.'&password='.$this->password.
        '&type=m3u_plus&output=ts';
    }
}
