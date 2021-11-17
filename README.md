
```title
_____   _   _   _____   _____    _____        _       _   __   _   _   _    _____  
/  ___/ | | | | /  _  \ |  _  \  |_   _|      | |     | | |  \ | | | | / /  /  ___/ 
| |___  | |_| | | | | | | |_| |    | |        | |     | | |   \| | | |/ /   | |___  
\___  \ |  _  | | | | | |  _  /    | |        | |     | | | |\   | | |\ \   \___  \ 
 ___| | | | | | | |_| | | | \ \    | |        | |___  | | | | \  | | | \ \   ___| | 
/_____/ |_| |_| \_____/ |_|  \_\   |_|        |_____| |_| |_|  \_| |_|  \_\ /_____/ 

```

# Short Links

市面上已有短网址项目要么对新手不友好，要么添加数据操作繁琐，或不易根据自己喜好返回动态链接。

本项目可以帮助你更快速的添加键值，且支持对接各类API，只要你会简单的 if...else...; 等基础 PHP 代码即可。

如果你需要实现以下功能且只会一些基础代码，那本项目将特别合适：

```need
    # 指定后缀KEY
    host.com/baidu => baidu.com
    # 子城名
    host.com/blog/hello.html => blog.host.com/hello.html
    # 动态链接
    host.com/mail/xxx@gmail => mailto:xxx@gmail.com
    # 手气不错
    host.com/random 
        => one.host.com/two.html
        => two.host.com/one.html
```

## 安装使用

1. 将所有文件下载并上传到网站根目录
2. 设置伪静态现则
  ```Nginx
if (!-e $request_filename) {
	rewrite ^/(?!.well-known)(.*)$ /index.php?$1 last;
}
  ```

4. 根据你的需求修改 index.php 及 env/xxx.ini 文件/代码内容
5. 访问你的网站域名查看效果

## 文件结构

```file
    ╓/ env         # 配置文件夹
    ╟─ app.ini       # 各APP中打开链接处理
    ╟─ cdn.ini       # 添加于一些链接前面以解决访问慢的问题
    ╟─ file.ini      # 所需文件路径
    ╟─ host.ini      # 域名黑白名单
    ╟─ link.ini      # 默认自定义短链
    ╟─ mail.ini      # 针对邮箱的处理
    ╟─ replace.ini   # 字符串替换
    ╟─ ...           # 根据自己需求添加
    ║
    ╟/ Template    # 模板文件夹
    ╟─ link.ini      # 链接类型拦截提示
    ╟─ text.ini      # 文本类型数据展示
    ╟─ ....          # 根据自己需求添加，记得写入 env/file.ini
    ║
    ╟ index.php    # 入口文件及处理
    ╟ favicon.ico  # 网页Logo
    ╙ readme.md    # 你目前正在看的说明文件
```
## 代码部分

详见 index.php。写的很乱，但不难看懂。

## 程序思路

1. 先获取链接请求部分内容，使用正则表达式分割，再组合成新的变量，以便后续处理。

```url
    完整链接：https://your.domain.com/1_3/5_7/9?11
    请求部分：/1_3/5_7/9?11 
    正则分割：$URL              组合变量：$URLs
    Array (                     Array ( 
        [0] => /1_3/5_7/9?11        [0] => 1_3/5_7/9
        [1] => 1                    [1] => 1_3 
        [2] => _                    [2] => 5_7 
        [3] => 3                    [3] => /9 
        [4] => /                    [4] => /5_7/9
        [5] => 5                    [5] => ?11 
        [6] => _               )
        [7] => 7 
        [8] => / 
        [9] => 9 
        [10] => ? 
        [11] => 11 
    )
```

2. 从 env/link.ini 中逐一匹配 1_3 ，无则从 1、3、... 分级进行自定义处理。生成 $DataT 字符串

```deal
    if( empty( xxx ) ){
        $DataT = ....... ;
    }else if( xxx && xxx ){
        /*
        Tips：
            可以在此添加几行代码，获取已有数据库中的链接
            本代码图省事，直接读 env/link.ini 中的内容
        */
        $DataT = ....... ;
    }else if( xxx || xxx ){
        $DataT = ....... ;
    }else{
        switch ( xxx ){
        case 'xxx':
            $DataT = ....... ;
            break;
        default:
            $DataT = ....... ;
        }
    }
```

3. 判断 $DataT 字符串数据类型，并进一步处理输出数据

```type
$DataT = "http:"   /   "text:"  /   "image:"  / "mailto:"  / ... xxx ... 
        链接(link) / 文本(text) / 图片(image) / 邮箱(mail)
```

4. 使用自定义模板输出数据

# 一些说明

作者大三土木狗，码字属业余爱好，编程能力有限，故代码会显得十分混乱，不建议直接使用。

目前能满足自己各类需求，可能不会再更新了，也许吧(?)

如有更好实现或项目，欢迎与我交流。
