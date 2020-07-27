<?php

namespace Tests\PHPUnit\API;

use Exception;
use Network;
use PHPUnit\Framework\TestCase as BaseTestCase;

if (! defined('ZBP_PATH')) {
    require_once __DIR__ . '/../../../zb_system/function/c_system_base.php';
    $GLOBALS['zbp']->Load();
}

class TestCase extends BaseTestCase
{
    protected $response;

    public function callAPI($mod, $act = null, $method = 'GET', $query_data = [], $post_data = [], $headers = [])
    {
        global $zbp;$zbp->host = 'https://1.7-dev.zblogphp.test/';

        $url = $zbp->host . 'api.php?mod=' . $mod . '&act=' . $act;
        if (count($query_data) > 0) {
            $url .= '&' . http_build_query($query_data);
        }

        $network = Network::Create();
        if (!$network) {
            throw new Exception('Cannot use Network class.');
        }

        $network->open($method, $url);
        $network->enableGzip();
        $network->setTimeOuts(120, 120, 0, 0);
        foreach ($headers as $bstrHeader => $bstrValue) {
            $network->setRequestHeader($bstrHeader, $bstrValue);
        }
        $network->send($post_data);

        $result = json_decode($network->responseText, true);
        unset($result['runtime']);

        $this->response = [
            'status' => $network->status,
            'text' => $network->responseText,
            'result' => $result,
        ];

        return $this;
    }

    public function assertStatus($status)
    {
        return $this->assertEquals($status, $this->response['status']);
    }

    public function assertText($text)
    {
        return $this->assertEquals($text, $this->response['text']);
    }
}

