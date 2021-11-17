# 说明

本模板仅适用于 [Mirages For Typecho](https://hovthen.com/mirages) 博客主题。

![Mirages For Typecho](https://cdn.get233.com/hran/2017/11/01/150955141331011_mirages-4.png?imageView2/2/w/1920/q/75/format/webp)

# 在线演示

[错误示例](https://www.hovthen.com/url)

[文本示例](https://www.hovthen.com/url?hello)

[电子邮箱示例](https://www.hovthen.com/url?mailto:me@hovthen.com)

[内部链接示例](https://www.hovthen.com/url?boke:107)

[外部链接示例](https://www.hovthen.com/url?https://github.com/Hovthen/ShortLinks)

# 使用

1. 将文件上传至 Mirages For Typecho 主题根目录下；
2. 在 Typecho 根目录下新建 Env/host.ini 文件。格式参考本项目同名文件，如需修改路径见 383 行；
  ```php
  Path = __TYPECHO_ROOT_DIR__ ."/Env/host.ini";
  ```
3. 添加 独立页面，模板选择 Raw url，slug 为 url，如果不是请修改 16 行，以便多个独立页面进行不同且更灵活的处理；
  ```php
  case 'url':
  ```
4. 访问 your.boke.com/url?hello 看看效果，如需使用 / 替换链接中的 ? 请使用伪静态；

# 获取帮助

请访问我的 [博客](https://hovthen.com/boke/107) 留言或给我发送电子邮件。

