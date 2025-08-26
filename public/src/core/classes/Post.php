<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Post
{
    public static function getById($id)
    {
        return self::getByIdSlug($id);
    }

    public static function getByIdSlug($id)
    {
        $ql = "SELECT id,description,title,post,`language`,publish,slug,created,updated,user_id,
      `image` as img,
      (SELECT GROUP_CONCAT(b.metavalue SEPARATOR ',') FROM metadata b WHERE b.content_id=post.id AND metakey='post.tag') as tags
      FROM post WHERE (id=? OR slug=?)";

        if ($row = DB::getOne($ql, [$id,$id])) {
            if ($blocks = DB::value("SELECT blocks FROM post WHERE (id=? OR slug=?);", [$id,$id])) {
                $blocks = json_decode($blocks);
                $row['post'] = ($row['post'] ?? '') . View::blocks($blocks, 'post' . $row['id']);
            }
            $row['url'] = Config::url('blog', ['p' => $row['id'],'slug' => $row['slug']]);
            $row['user'] = [];
            if (!empty($row['user_id'])) {
                $row['user'] = User::getById($row['user_id']) ?? [];
            }
            return $row;
        }
        return false;
    }

    public static function fetchByIdSlug($id)
    {
        $post = Cache::remember('post_data_', 86400, function ($u) {
            $post = self::getByIdSlug($u[0]);
            return json_encode($post);
        }, [$id, Config::mt('post')]);
        return json_decode($post, true);
    }

    public static function meta($id, $meta, $value = null, $multi = false)
    {
        if ($value == null) {
            $ql = "SELECT metavalue FROM metadata where content_id=? and metakey=?;";
            return DB::getList($ql, [$id, $meta]);
        }
        if ($multi == false) {
            if (DB::value("SELECT COUNT(*) FROM metadata WHERE content_id=? AND metakey=?;", [$id, $meta])) {
                $ql = "UPDATE metadata SET metavalue=? WHERE content_id=? AND metakey=?;";
                return DB::query($ql, [$value, $id, $meta]);
            }
        }
        $ql = "INSERT INTO metadata(content_id,metakey,metavalue) VALUES(?,?,?);";
        return DB::query($ql, [$id, $meta, $value]);
    }

    public static function getMeta($meta)
    {
        $ql = "SELECT metavalue,COUNT(*) AS count FROM metadata where metakey=? GROUP BY metavalue;";
        return DB::get($ql, [$meta]);
    }

    public static function getByUserID($id)
    {
        return DB::get("SELECT * FROM post WHERE user_id=?", $id)[0];
    }

    public static function total($args = [])
    {
        $where = self::where($args);
        return DB::value("SELECT COUNT(*) FROM post WHERE $where;");
    }

    public static function getLatest($n = 8)
    {
        return self::getPosts(['posts' => $n,'from' => 0]);
    }

    public static function getPosts($args = [])
    {
        $ppp = isset($args['posts']) ? $args['posts'] : 8;
        if (!empty($args['category']) && $args['category'] == 'demo') {
            $posts = Config::include('data/demo.posts.php');
            foreach ($posts as $post) {
                yield $post;
            }
            return;
        }
        $where = self::where($args);
        $start_from = isset($args['from']) ? $args['from'] : 0;
        if (isset($args['page'])) {
            $start_from = ($args['page'] - 1) * $ppp;
        }

        $order = 'ORDER BY id DESC';
        if (Config::get('blog_orderby_publish') == 1) {
            $order = 'ORDER BY publish_at DESC,id DESC';
        }
        $ql = "SELECT id,title,description,slug,SUBSTRING(post,1,300) as post,created,updated,user_id,
      `image` as img,
      (SELECT GROUP_CONCAT('{', CONCAT('\"',metavalue,'\":\"',title,'\"'), '}' SEPARATOR ',') FROM metadata,postcategory p WHERE content_id=post.id AND metakey='post.category' AND metavalue=p.id) as categories,
      (SELECT username FROM user WHERE post.user_id=id) as author, `language`
      FROM post
      WHERE $where
      $order LIMIT $start_from,$ppp";
        $rows = DB::get($ql);
        foreach ($rows as $i => $r) {
            $r['url'] = Config::url('blog/' . $r['id'] . '/' . urlencode($r['slug']));
            $r['post'] = HtmlInput::DOMSanitize($r['post'], false);
            $r['user'] = User::getById($r['user_id']) ?? [];
            yield $r;
        }
    }

    public static function where($args = [])
    {
        $category = !empty($args['category']) ? "AND id IN(SELECT content_id from metadata where metakey='post.category' and metavalue='{$args['category']}')" : '';
        $tag = isset($args['tag']) ? "AND id IN(SELECT content_id from metadata where metakey='post.tag' and metavalue='{$args['tag']}')" : '';
        $user_id = isset($args['user_id']) ? "AND user_id='{$args['user_id']}'" : '';
        $language = isset($args['language']) ? "AND (language='{$args['language']}' OR language IS NULL)" : '';
        return "publish=1 $category $tag $user_id $language";
    }

    public static function search($s)
    {
        $res = DB::query("SELECT id,description,title,slug,SUBSTRING(post,1,300) as post, `image` as img
      FROM post WHERE publish=1
      AND match(title,post) AGAINST(? IN NATURAL LANGUAGE MODE) ORDER BY id DESC", $s);
        if ($res) {
            while ($r = mysqli_fetch_array($res)) {
                yield $r;
            }
        }
    }

    public static function categories($id = null)
    {
        if ($id) {
            return DB::get("SELECT c.id,c.title FROM postcategory c, metadata m
      WHERE m.content_id=? AND c.id=m.metavalue AND metakey='post.category';", [$id]);
        }
        return DB::get("SELECT id,title FROM postcategory;");
    }

    public static function categoryOptions($id = null)
    {
        if ($id) {
            return DB::get("SELECT c.id,c.title FROM postcategory c, metadata m
      WHERE m.content_id=? AND c.id=m.metavalue AND metakey='post.category';", [$id]);
        }
        return array_merge(
            [0 => '*'],
            DB::getOptions("SELECT id,title FROM postcategory;"),
            ['demo' => '--Demo--']
        );
    }


    public static function inCachedList($id)
    {

        $array = Cache::remember('post_cache_list', 86400, function ($u) {
            $ql = "SELECT id FROM post WHERE publish=1 UNION SELECT slug FROM post WHERE publish=1";
            return json_encode(DB::getList($ql));
        }, [Config::mt('post')]);
        return (in_array($id, json_decode($array, true)));
    }

    public static function ping($id)
    {
        $p = self::getById($id);

        $hubUrl = 'https://pubsubhubbub.superfeedr.com';
        $feedUrl = Config::base('blog/feed');
        $data = http_build_query(['hub.mode' => 'publish', 'hub.url' => $feedUrl]);
        $ch = curl_init($hubUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            file_put_contents('log/ping_hub_response.txt', 'Error pinging hub: ' . curl_error($ch));
        } else {
            file_put_contents('log/ping_hub_response.txt', 'Hub pinged successfully.');
        }
        curl_close($ch);

        Config::setMt('last_post_ping');
    }

    public static function indexNow($post)
    {
        if ($post['publish'] != 1) {
            return;
        }
        $key = 'post-' . str_pad($post['id'], 6, '0', STR_PAD_LEFT);
        $url = Config::base('blog/'. $post['id']. '/'. $post['slug']);
        Http::get("https://api.indexnow.org/indexnow?url=$url&key=$key");
    }
}
