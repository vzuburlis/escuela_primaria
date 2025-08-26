<?php

return [
  'fields' => [
    'n_post' => [
      'title' => 'Number of posts',
      'default' => '2',
      'type' => 'select',
      'options' => [1 => '1',2 => '2',3 => '3',4 => '4',6 => '6']
    ],
    'category' => [
      'title' => 'Category',
      "type" => "postcategory"
    ],
    'language' => [
      'type' => 'language'
    ],
  ],
  'keys' => 'email,template,campaign',
  'group' => 'text-email',
];
