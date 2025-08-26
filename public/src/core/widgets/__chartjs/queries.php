<?php

Gila\Config::addList('chartjs-query', [
  'label' => 'Posts: Categories Published',
  'query' => 'SELECT
(SELECT title FROM postcategory x WHERE x.id=metadata.metavalue) a,
(SELECT CASE WHEN publish=0 THEN "Draft" ELSE "Published" END FROM post y WHERE y.id=metadata.content_id) b,
COUNT(*)
FROM metadata
WHERE metakey="post.category"
GROUP BY a,b;'
]);

if (in_array('shop', Gila\Config::packages())) {
    Gila\Config::addList('chartjs-query', [
    'label' => __('Shop: Inventory status', ['es' => 'Estado de inventario']),
    'query' => 'SELECT
(SELECT title FROM shop_category x WHERE x.id=shop_product.category_id) a,
"Stock" b,
SUM(qty)
FROM shop_product, shop_stock
WHERE shop_product.id = shop_stock.product_id
GROUP BY a,b;'
    ]);
}
