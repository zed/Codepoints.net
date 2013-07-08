<?php

require_once __DIR__.'/../tools.php';

$host = get_origin().'api/v1';

return array(
    "description" => _("Welcome to codepoint.net’s Unicode API. Most requests are JSON-based. The API supports for those requests JSONP via the `callback` GET parameter. The `glyph` and `property` methods return PNG images. When problems appear, the API response conforms to <http://tools.ietf.org/html/draft-nottingham-http-problem-04>."),
    "codepoint_url" => "$host/codepoint/{codepoint}{?property*}{?callback}",
    "block_url" => "$host/block/{block}{?callback}",
    "plane_url" => "$host/plane/{plane}{?callback}",
    "glyph_url" => "$host/glyph/{codepoint}",
    "name_url" => "$host/name/{codepoint}{?callback}",
    "transform_url" => "$host/transform/{action}/{data}{?callback}",
    "filter_url" => "$host/filter/{data}{?property*}{?callback}",
    "property_url" => "$host/property/{property}",
    "script_url" => "$host/script/{iso}{?callback}",
    "patterns" => array(
        "codepoint" => "(10)?[A-Fa-f0-9]{4}|0[A-Fa-f0-9]{5}",
        "data" => ".{1,1024}",
        "iso" => "[A-Z][a-z]{3}",
    ),
);

#EOF
