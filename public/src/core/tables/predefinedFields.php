<?php

return [
  'contact_id' => [
    'title' => 'Contact',
    'options' => [0 => '-'],
    'qoptions' => 'SELECT id,`name` FROM contact',
    'qtype' => 'INT UNSIGNED',
    'show' => false,
    'type' => 'v-select-ajax',//key',
    'table' => 'contact',
    'src' => Config::base() . 'cm/contact?token=' . $tokenId . '&search=',
    //'create'=>false,
    'edit' => false,
  ],
];
