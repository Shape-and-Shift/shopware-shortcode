# Shortcodes in Shopware 6

Use Wordpress like Shortcodes within the Text Editor in Shopware 6.

<a href="k4mbhUD7vQ4" target="_blank"><img width="818" alt="Bildschirmfoto 2021-04-05 um 10 35 50" src="https://user-images.githubusercontent.com/8193345/122733773-2994cb80-d286-11eb-9372-4bdd1116b544.png"></a>

### Installation

`composer require sas/shortcode`

### Configuration

After the plugin installation, you are now able to use these three shortcodes from within the Shopware Editor or CMS page as well.

* `{{ product=firstProductId,secondProductId }}`
* `{{ category=firstCategoryId,secondCategoryId }}`
* `{{ icon=ICONNAME }}`, for example `{{ icon=heart }}`

After installation you also have a config option for the plugin to use custom theme icons, this can be enabled in the plugin configuration.

*We're currently working on more Shortcodes, like for example to have the ability to create columns with Shortcodes.*

![Shortcode in texteditor](https://res.cloudinary.com/dtgdh7noz/image/upload/v1624259577/Bildschirmfoto_2021-06-21_um_10.10.54_nm7kke.png)

![Storefront which shows the product from the Shortcode](https://res.cloudinary.com/dtgdh7noz/image/upload/v1624259577/Bildschirmfoto_2021-06-21_um_10.12.18_pg13ud.png)
