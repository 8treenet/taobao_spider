<?php
/**
 * Created by PhpStorm.
 * User: ys
 * Date: 16/12/12
 * Time: 下午4:28
 */
require_once("Crawler.php");
class GoodsCrawler extends Crawler {


    public function response($status){
        parent::response($status);
        if ($status == 200)
        {
        }
        return $status;
    }

    //返回商品基础信息
    public function getBaseInfo()
    {
        $baseInfo = [];

        $root = $this->findOne('//div[@class="tb-wrap tb-wrap-newshop"]');
        $baseInfo['title'] = trim($this->findChildOne($root, '.tb-main-title')->plaintext);

        $baseInfo['price'] = $this->findChildOne($root, '.tb-rmb-num')->plaintext;//价格

        $promoPrice = $this->findChildOne($root, '#J_PromoPriceNum');
        if($promoPrice)
        {
            $baseInfo['coupon_price'] = $promoPrice->plaintext;//优惠价格
        } else {
            $baseInfo['coupon_price'] = $baseInfo['price'];
        }


            //J_PromoPriceNum
        $couponText  = $this->findChildOne($root, '//div[@class="tb-other-discount"]');
        $baseInfo['coupon_text'] = trim(preg_replace("/\s(?=\s)/","\\1",$couponText->plaintext));

        $baseInfo['address'] =  $this->findChildOne($root, '#J-From')->plaintext;//发货地址

        $baseInfo['store_count'] = $this->findChildOne($root, '#J_SpanStock')->plaintext;//库存
        $baseInfo['comment_count'] = $this->findChildOne($root, '#J_RateCounter')->plaintext;//评论数
        $baseInfo['sale_count'] = $this->findChildOne($root, '#J_SellCounter')->plaintext;//销量


        $url = $this->getUrl();
        $query = parse_url($url);
        $query = $this->_convertUrlQuery($query['query']);
        $baseInfo['goods_id'] = isset($query['id']) ? $query['id'] : '0';


        return $baseInfo;
    }

    //返回商品详细信息
    public function  getDetailList()
    {
        $detailList = [];
        $root = $this->findOne('//ul[@class="attributes-list"]');
        $childs = $this->findChild($root, '//li');
        foreach ($childs as $child)
        {
            $detailList[] = trim(str_replace('&nbsp;','',$child->plaintext));
        }
        return $detailList;
    }

    //返回商品图片列表
    public function getGoodsImgList()
    {
        $imgList = [];
        $root = $this->findOne('//ul[@class="tb-thumb tb-clearfix"]');
        $childs = $this->findChild($root, '//a/img');
        foreach($childs as $child)
        {
            $imgList[] = 'http:' . $child->src;
            //$this->downloadImg('http:' . $child->src, './');
        }

        return $imgList;
    }

    //返回路径地址
    public static function downloadImg($url, $path, $w=500, $h=500)
    {
        $new = $w.'x'.$h;
        $url = str_replace('50x50', $new, $url);
        $data = file_get_contents($url);
        $filePath = $path.md5($url).'.jpg';
        file_put_contents($filePath, $data);
        return $filePath;
    }

    //返回卖家信息
    public function getUserInfo()
    {
        $user = [];
        $root = $this->findOne('//div[@class="tb-shop-info-wrap"]');
        $user['shop_name'] = trim($this->findChildOne($root, '.tb-shop-name')->plaintext);
        $level = $this->children($root, 1);
        $level = $this->children($level, 2);
        $leveltName = $level->class;
        if (strstr($leveltName, 'crown'))
        {
            $user['level_name'] = 'crown';
        }
        if (strstr($leveltName, 'cap'))
        {
            $user['level_name'] = 'cap';
        }
        if (strstr($leveltName, 'blue'))
        {
            $user['level_name'] = 'blue';
        }
        if (strstr($leveltName, 'red'))
        {
            $user['level_name'] = 'red';
        }

        $childs = $this->findChild($root, '//i');
        $user['level_num'] =  strval(count($childs));

        return $user;
    }

    function _convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param)
        {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

} 