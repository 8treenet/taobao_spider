<?php
/**
 * User: ys
 * Date: 16/12/12
 * Time: 下午4:19
 */
use JonnyW\PhantomJs\Client;
require_once("vendor/autoload.php");
require_once("simple_html_dom.php");

class Crawler {
    private $_dom;
    private $_url;

    public function Crawler()
    {
        $this->_dom = new simple_html_dom();
    }

    //请求地址 重连次数,等待页面超时时间,延迟执行时间
    public function request($url, $reconnectCount = 3, $timeout = 10000, $delayTime = 1.5)
    {

        sleep($delayTime);
        $client = Client::getInstance();
        $client->getEngine()->addOption('--load-images=false');
        $client->isLazy();

        /**
         * @see JonnyW\PhantomJs\Http\Request
         **/
        $request = $client->getMessageFactory()->createRequest($url, 'GET',$timeout);
        $request->setTimeout($timeout);
        $response = $client->getMessageFactory()->createResponse();

        $client->send($request, $response);
        $this->_url = $response->getUrl();
        if($response->getStatus() == 200) {
            $this->_dom->load($response->getContent());
            return $this->response(200);
        } else {
            $this->request($url, $reconnectCount - 1, $timeout);
        }
        return $this->response($response->getStatus());
    }

    public function response($status)
    {
        return $status;
    }

    public function getElementById($id)
    {
        return $this->_dom->getElementById($id);
    }

    //返回一个查找到的对象数组
    public function find($path)
    {
        return $this->_dom->find($path);
    }

    //返回一个查找到的对象
    public function findOne($path)
    {
        $list = $this->_dom->find($path);
        if(count($list) > 0)
        {
            return $list[0];
        }
        return null;
    }


    //通过$selector 查找某个节点
    public function findChildOne($root, $selector)
    {
        $list = $root->find($selector);
        if(count($list) > 0)
        {
            return $list[0];
        }
        return null;
    }

    //通过$selector 查找某个节点组
    public function findChild($root, $selector)
    {
        return $root->find($selector);;
    }

    //获取子节点
    public function children($root, $index)
    {
        if (!$root->has_child())
        {
            return null;
        }

        return $root->children($index-1);
    }

    public function getUrl()
    {
        return $this->_url;
    }
} 