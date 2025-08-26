<?php

return [
  'fields' => [
    'style' => [
      'type' => 'radio',
      'options' => ['square' => 'Square','circle' => 'Circle','none' => 'None'],
      'default' => 'square'
    ],
    'align' => [
      'type' => 'radio', 'default' => 'center',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'facebook-URL' => [],
    'twitter-URL' => [],
    'linkedin-URL' => [],
    'pinterest-URL' => [],
    'youtube-URL' => [],
    'instagram-URL' => [],
    'tiktok-URL' => [],
    'newsletter-URL' => [],
    'google-URL' => [],
    'medium-URL' => [],
    'github-URL' => [],
    'tumblr-URL' => [],
    'vk-URL' => [],
    'twitch-URL' => [],
    'slack-URL' => [],
    'codepen-URL' => [],
    'stack-overflow-URL' => [],
    'soundcloud-URL' => [],
    'rss-URL' => []
  ],
  'keys' => 'page,post,widget,social',
  'group' => 'social',
];
