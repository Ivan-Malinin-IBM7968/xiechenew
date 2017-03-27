<?php

class TestAction extends CommonAction
{
    /**
     * curl_sms demo
     */
    public function demo()
    {
        var_dump($this->curl_sms(array(
            'phones'  => '15853205347',
            'content' => array(
                '2313', '3'
            )
        ), null, 4));die;

    }
}