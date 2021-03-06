<?php

require_once __DIR__.'/../tools.php';

if (! count($_GET)) {
    $host = get_origin().'api/v1';
    return array(
        "description" => _("search for codepoints by their properties"),
        "search_url" => "$host/search{?property}{&page}{&per_page}{&callback}",
        "properties" => array(
            "q" => _("free search"),
            "int" => _("decimal codepoint"),
        ) + UnicodeInfo::get()->getAllCategories(),
    );
}

$result = new SearchResult(array(), $api->_db);
$cats = array_merge(UnicodeInfo::get()->getCategoryKeys(), array('int'));
foreach ($_GET as $k => $v) {
    if ($k === 'q' && $v) {
        // "q" is a special case: We parse the query and try to
        // figure, what's searched
        if (mb_strlen($v, 'UTF-8') === 1) {
            // seems to be one single character
            $result->addQuery('cp', unpack('N', mb_convert_encoding($v,
                                    'UCS-4BE', 'UTF-8')));
        } else {
            foreach (preg_split('/\s+/', $v) as $vv) {
                if (ctype_xdigit($vv) && in_array(strlen($vv), array(4,5,6))) {
                    $result->addQuery('cp', hexdec($vv), '=', 'OR');
                }
                if (substr(strtolower($vv), 0, 2) === 'u+' &&
                    ctype_xdigit(substr($vv, 2))) {
                    $result->addQuery('cp', hexdec(substr($vv, 2)), '=', 'OR');
                }
                if (ctype_digit($vv) && strlen($vv) < 8) {
                    $result->addQuery('cp', intval($vv), '=', 'OR');
                }
                $result->addQuery('na', $vv, 'LIKE', 'OR');
                $result->addQuery('na1', $vv, 'LIKE', 'OR');
                $vv = "%$vv%";
                $result->addQuery('kDefinition', $vv, 'LIKE', 'OR');
                $result->addQuery('alias', $vv, 'LIKE', 'OR');
                $result->addQuery('abstract', $vv, 'LIKE', 'OR');
                if (preg_match('/\blowercase\b/i', $vv)) {
                    $result->addQuery('gc', 'lc', '=', 'OR');
                }
                if (preg_match('/\buppercase\b/i', $vv)) {
                    $result->addQuery('gc', 'uc', '=', 'OR');
                }
                if (preg_match('/\btitlecase\b/i', $vv)) {
                    $result->addQuery('gc', 'tc', '=', 'OR');
                }
            }
        }
    } elseif ($v && $k === 'scx') {
        // scx is a list of sc's
        $result->addQuery($k, $v);
        $v2 = explode(' ', $v);
        foreach($v2 as $v3) {
            $result->addQuery($k, "%$v3%", 'LIKE', 'OR');
        }
    } elseif ($k === 'int' && $v !== "") {
        $v = preg_split('/\s+/', $v);
        foreach($v as $v2) {
            if (ctype_digit($v2)) {
                $result->addQuery($k, $v2, '=', 'OR');
            }
        }
    } elseif ($v && in_array($k, $cats)) {
        $result->addQuery($k, $v);
    }
    // else: that's an unrecognized GET param. Ignore it.
}

$page = isset($_GET['page'])? intval($_GET['page']) : 1;
$limit = isset($_GET['per_page'])? min(1000, intval($_GET['per_page'])) : 1000;
$result->pageLength = $limit;
$result->page = $page - 1;

$return = array(
    "page" => $page,
    "last_page" => 1,
    "per_page" => $limit,
    "count" => $result->getCount(),
    "result" => array(),
);

if ($return['count'] > 0) {
    $pagination = new Pagination($result->getCount(), $limit);
    $pagination->setPage($page);
    $last_page = $pagination->getNumberOfPages();
    $return["last_page"] = $last_page;
    $link_header = 'Link: <http://codepoints.net/api/v1/search?';
    header('Link: <http://codepoints.net/search?'.http_build_query($_GET).'>; rel=alternate', false);
    if ($page > 1 && $page <= $last_page) {
        $get = $_GET;
        $get['page'] = $page - 1;
        header($link_header.http_build_query($get).'>; rel=prev', false);
    } elseif ($page > $last_page) {
        $get = $_GET;
        $get['page'] = $last_page;
        header($link_header.http_build_query($get).'>; rel=prev', false);
    }
    if ($page < $last_page) {
        $get = $_GET;
        $get['page'] = $page + 1;
        header($link_header.http_build_query($get).'>; rel=next', false);
    }
    $get = $_GET;
    $get['page'] = $last_page;
    header($link_header.http_build_query($get).'>; rel=last', false);
    $get['page'] = 1;
    header($link_header.http_build_query($get).'>; rel=first', false);

    foreach ($result->get() as $cp => $na) {
        $return["result"][] = $cp;
    }
}

return $return;


#EOF
