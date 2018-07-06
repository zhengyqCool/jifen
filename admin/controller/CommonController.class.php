<?php
defined("ADMIN_IN") or die('illegal origin');
class CommonController extends Controller {
    public function checkcode(){
        $checkcode = getRandID(4,false);
        $_SESSION['checkcode'] = $checkcode;
        getSecode($checkcode);
    }
}
