<?php
require("spider/GoodsCrawler.php");
require("spider/ShopCrawler.php");
/**
 * @author: ys <4932004@qq.com>
 */

//任意淘宝连接都可以解析出店铺id 一键爬取淘宝店铺所有信息
$url = 'https://item.taobao.com/item.htm?spm=a230r.1.14.22.RiJEHt&id=540022708960&ns=1&abbucket=13#detail';

$crawler  = new ShopCrawler();
$reconnectCount = 3;//重连次数
$timeout = 10000;    //等待页面超时时间
$delayTime = 1.5;    //延迟执行,防止频繁访问被封
$crawler->request($url, $reconnectCount, $timeout, $delayTime);
$shopId = $crawler->getShopId();//获取店铺id


//获取第一页商品连接和信息
$shop  = new ShopCrawler();
$shop->request($shop->getShopHref($shopId)); //打开店铺
$info = $shop->getInfo();                //获取店铺信息
var_dump($info);
$goodsListUrl = $shop->getGoodsListUrl();//获取所有的商品连接


foreach($goodsListUrl as $url)
{
    $goods = new GoodsCrawler();
    $goods->request($url, $reconnectCount, $timeout, $delayTime);
    var_dump($goods->getBaseInfo());
    var_dump($goods->getDetailList());
    $imgList = $goods->getGoodsImgList();
    var_dump($imgList);
    foreach($imgList as $imgUrl)
    {
        //GoodsCrawler::downloadImg($imgUrl, './img/');
    }

    unset($goods);
}
