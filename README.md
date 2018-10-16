# wp-acme-mdx-plus

Wordpress插件，适用于MDX主题（构建在1.8.3版本上），对其进行增强，以便符合自己的要求

### 增强内容如下：

- 增加TOC功能（主要参考jquery.titleNav.js和阿里云帮助文档页面的目录样式）
- 当支持Markdown语法时，在保存文章时对文章中图片进行过滤，查询图片信息，在输出的<code>img</code>标签中增加图片的宽度和高度
- 当支持Markdown语法时，在保存文章时对文章中表格进行过滤，增加class样式mdui-table
- 修改文章中特殊图片的百分比，由82%改为100%
- 将插件<code>Markdown-Extra-Customize-Attribute</code>的功能集成进来
- 增加三种引用的样式：danger、tip、warning
- 支持instantClick.js
- 解决支持instantClick.js后引起的与wp-editormd插件代码高亮不兼容的问题
