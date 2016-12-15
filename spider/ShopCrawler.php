<?php
/**
 * User: ys
 * Date: 16/12/14
 * Time: 上午10:45
 */
require_once("Crawler.php");
class ShopCrawler extends Crawler {
    public function response($status)
    {
        parent::response($status);
        if ($status == 200)
        {
        }
        return $status;
    }

    ///获取shopid
    public function getShopId()
    {
        $root = $this->findOne('meta[name=microscope-data]');
        if($root)
        {
            preg_match(';shopId=[0-9]+;', $root->content, $match);
            if ($match)
            {
                $match = explode('=',$match[0]);
                if ($match)
                {
                    return $match[1];
                }
            }
        }
        return '';
    }

    //店铺首页
    public function getInfo()
    {
        $info = [];
        $root = $this->findOne('//a[@class="shop-name-link"]');
        if ($root)
        {
            $info['shop_name'] = str_replace('\t','', $root->plaintext);
        } else {

            $root = $this->findOne('.J_TGoldlog');
            if ($root) {
                $info['shop_name'] = trim(str_replace(' ', '', $root->plaintext));
                $info['shop_name'] = trim(str_replace('进入店铺', '', $info['shop_name']));
            }
        }

        $root = $this->findOne('.shop-rank');
        if ($root)
        {

           $level = $this->children($root, 1);
           $leveltName = $level->class;
           if (strstr($leveltName, 'crown'))
           {
               $info['level_name'] = 'crown';
           }
           if (strstr($leveltName, 'cap'))
           {
               $info['level_name'] = 'cap';
           }
           if (strstr($leveltName, 'blue'))
           {
               $info['level_name'] = 'blue';
           }
           if (strstr($leveltName, 'red'))
           {
               $info['level_name'] = 'red';
           }

            $childs = $this->findChild($root, '//i');
            $info['level_num'] =  strval(count($childs));

        }
        $info['shop_id'] = $this->getShopId();


        $dsr = $this->find('//span[@class="dsr-num red"]');
        if(empty($dsr))
        {
            $dsr = $this->find('//span[@class="dsr-num green"]');
        }

        if (isset($dsr[0]))
        {
            $info['describe'] = $dsr[0]->plaintext;
        }

        if (isset($dsr[1]))
        {
            $info['service'] = $dsr[1]->plaintext;
        }

        if (isset($dsr[2]))
        {
            $info['logistics'] = $dsr[2]->plaintext;
        }

        return $info;
    }


    //通过shopid 获取shop连接
    public function getShopHref($shopId)
    {
        return sprintf("https://shop%s.taobao.com/search.htm?orderType=_hotsell", $shopId);
    }


    public function getGoodsListUrl()
    {
        $goodsListHref = $this->_getGoodsHref();
        $nextUrl = $this->_getNextHref();

        //分页加载商品连接地址
        while (!empty($nextUrl))
        {
            $nextShop = new ShopCrawler();
            $nextShop->request($nextUrl);
            $goodsListHref = array_merge($goodsListHref, $nextShop->_getGoodsHref());
            $nextUrl = $nextShop->_getNextHref();
            unset($nextShop);
        }

        return $goodsListHref;
    }

    //获取本页所有商品连接
    public function _getGoodsHref()
    {
        $hrefs = [];
        $root = $this->find('//a[@class="item-name J_TGoldData]');
        foreach ($root as $a)
        {
            $hrefs[] = 'https:'.$a->href;
        }

        return $hrefs;
    }

    //获取下一页地址
    public function _getNextHref()
    {
        $root = $this->findOne('//a[@class="J_SearchAsync next"]');
        if (!empty($root) and $root->href)
        {
            return 'https:'.$root->href;
        }
        return '';
    }
} 