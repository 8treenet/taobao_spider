#taobaoSprider
#介绍
#作者使用 ubuntu系统
1. sudo apt-get install libfontconfig
2. git clone git@github.com:yangshu369/taobaoSprider.git



#下载安装 PhantomJS  http://phantomjs.org/download.html
#作者使用执行文件,源码安装自行研究. 淘宝有各种重定向和异步加载,PhantomJS基于webkit 浏览器渲染方式,更加稳妥,相对慢一些. 可自行修改curl方式

3. wget https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-2.1.1-linux-x86_64.tar.bz2

4. tar -xf phantomjs-2.1.1-linux-x86_64.tar.bz2

5.  cd phantomjs-2.1.1-linux-x86_64/bin/

6.  ./phantomjs -v
2.1.1

#移动phantomjs到项目bin 目录下
7. mv phantomjs 你的/taobaoSprider/bin

#cd xxx 进入项目根目录taobaoSprider
8. php Execute.php







#2.0 coding中........
.数据库
.多线程
.分布式
