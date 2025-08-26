<?php

$srcOptions = [];
foreach (Config::$content as $key => $item) {
    $srcOptions[$key] = $key;
}
$functionOptons = [
  'COUNT(*)' => 'Count',
  'SUM(price)' => 'Total price',
  'SUM(amount)' => 'Total amount',
];
$set1Options = [
  'DATE_FORMAT(FROM_UNIXTIME(create_at),"%M-%m")' => 'Creation (month)',
  'DATE_FORMAT(FROM_UNIXTIME(update_at),"%M-%m")' => 'Update (month)',
  'DATE_FORMAT(FROM_UNIXTIME(publish_at),"%M-%m")' => 'Publish (month)',
  'DATE_FORMAT(FROM_UNIXTIME(date),"%Y-%m")' => 'Date (month)',
  'CONCAT(DATE_FORMAT(FROM_UNIXTIME(create_at),"%M Q"), QUARTER(date))' => 'Creation (quarter)',
  'CONCAT(DATE_FORMAT(FROM_UNIXTIME(update_at),"%M Q"), QUARTER(date))' => 'Update (quarter)',
  'CONCAT(DATE_FORMAT(FROM_UNIXTIME(publish_at),"%M Q"), QUARTER(date))' => 'Publish (quarter)',
  'CONCAT(DATE_FORMAT(FROM_UNIXTIME(date),"%Y Q"), QUARTER(FROM_UNIXTIME(date)))' => 'Date (quarter)',
];
$json = json_encode([0 => 'Borrador',1 => 'OK']);
$status_q = "SELECT name FROM json_table('$json', '$[*]' columns(name  varchar(10) path '$.name', id int path '$.id'))";
$set2Options = [
  '' => 'N/A',
  'status_id' => 'Status',
  'active' => 'Active',
  '(SELECT username FROM user WHERE user.id=user_id)' => 'User',
  '(SELECT username FROM user WHERE user.id=owner_id)' => 'Owner',
];

return [
  'fields' => [
    'type' => [
      'type' => 'select',
      'options' => [
        'bar' => 'Bar',
        'bar.horizontal' => 'Bar Horizontal',
        'bar.stacked' => 'Bar Stacked',
        'line' => 'Line',
        'pie' => 'Pie',
        'doughnut' => 'Doughnut',
        'radar' => 'Radar',
        'gauge' => 'Gauge',
      ]
    ],
    'legend' => [
      'type' => 'select',
      'options' => [
        '' => 'No','left' => 'Left','right' => 'Right','top' => 'Top','bottom' => 'Bottom',
      ]
    ],
    'data_src' => [
      'type' => 'select',
      'options' => $srcOptions,
    ],
    'function' => [
      'type' => 'select',
      'options' => $functionOptons,
    ],
    'set1' => [
      'title' => 'Dataset 1',
      'type' => 'select',
      'options' => $set1Options,
    ],
    'set2' => [
      'title' => 'Dataset 2',
      'type' => 'select',
      'options' => $set2Options,
    ],
  ],
  'keys' => 'page,post,widget,chart'
];
