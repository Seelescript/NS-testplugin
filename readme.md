# PHP Test plugin - Products

Wordpress plugin to add cusomt post type products.
JUST FOR TEST.

## Installation

Install as plugin for wordpress

```bash
wp-content/plugins
```

Permalink Settings to  Post name	
```bash
/%postname%/
```
```bash
Apache directory need set 'AllowOverrite All'
```


## Notes:

Install hestia template.
```bash
https://www.creative-tim.com/tools/themeisle
```

The plugin create a shortcode for product list, create a page and use the shortcode
```bash
[infinite-products]
```
## API End points:
The plugin add custom endpoint to wordpress API
```bash
SITEROOT/wp-json/ns-api/v1/brands 
http://dev.nicasource/wp-json/ns-api/v1/brands

SITEROOT/wp-json/ns-api/v1/product-categories

SITEROOT/wp-json/ns-api/v1/products
SITEROOT/wp-json/ns-api/v1/products?rated 
SITEROOT/wp-json/ns-api/v1/products?featured 
SITEROOT/wp-json/ns-api/v1/products?brand=NAMEOFTHEBRAND  
SITEROOT/wp-json/ns-api/v1/products?category=CATEGORYNAME  

```

## License
[MIT](https://choosealicense.com/licenses/mit/)