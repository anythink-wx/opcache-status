OPcache Status
---------------

一个单页面opcache状态查看工具,需要PHP5.5以上版本.

如果你的服务器不支持opcache 将会加载data-sample.php显示示例数据

I know it is rather ugly, so please spruce it up. But I would like
to keep it relatively small and to a single file so it is easy to 
drop into a directory anywhere without worrying about separate css/js/php
files.


这是一个fork版本,经过了翻译和加入了重置链接

[![Screenshot](https://raw.githubusercontent.com/anythink-wx/opcache-status/master/screenshot2.png)](https://raw.githubusercontent.com/anythink-wx/opcache-status/master/screenshot2.png)

### TODO

 - The ability to sort the list of cached scripts by the various columns
 - A better layout that can accommodate more of the script data without looking cluttered
 - A tuning suggestion tab (need to add a couple of things to the opcache output first though)

