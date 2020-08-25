<?php
/**
 * Created by PhpStorm.
 * User: ping
 * Date: 2018/9/19
 * Time: 9:44
 */
  include_once('./config.php');
  include_once('../model/Code.class.php');
  $code = new SecCreateCode();
  $code->showImage();
?>