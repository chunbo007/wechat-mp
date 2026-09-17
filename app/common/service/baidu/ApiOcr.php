<?php

namespace app\common\service\baidu;

use support\baidu\AipOcr;

class ApiOcr
{
  private $ocr;
  private $BAIDU_APP_ID;
  private $BAIDU_API_KEY;
  private $BAIDU_SECRET_KEY;

  public function __construct()
  {
    $this->BAIDU_APP_ID = env('BAIDU_APP_ID');
    $this->BAIDU_API_KEY = env('BAIDU_API_KEY');
    $this->BAIDU_SECRET_KEY = env('BAIDU_SECRET_KEY');
    $this->ocr = new AipOcr($this->BAIDU_APP_ID, $this->BAIDU_API_KEY, $this->BAIDU_SECRET_KEY);
  }

  public function getOcr()
  {
    return $this->ocr;
  }

  public function __call($name, $arguments)
  {
    return call_user_func_array([$this->ocr, $name], $arguments);
  }

}