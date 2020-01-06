<?php
/**
 * Supply the basis for the navbar as an array.
 */
$temp = explode("/", $_SERVER["REQUEST_URI"]);
$last = array_pop($temp);

return [
    // Use for styling the menu
    "wrapper" => null,
    "class" => "my-navbar rm-default rm-desktop",
 
    // Here comes the menu items
    "items" => [
        [
            "text" => "Home",
            "url" => "",
            "class" => $last == "" ? "active" : null,
            "title" => "Första sidan, börja här.",
        ],
        [
            "text" => "Popular",
            "url" => "popular",
            "class" => $last == "popular" ? "active" : null,
            "title" => ""
        ],
        [
            "text" => "Posts",
            "url" => "posts",
            "class" => $last == "posts" ? "active" : null,
            "title" => ""
        ],
        [
            "text" => "Tags",
            "url" => "tags",
            "class" => $last == "tags" ? "active" : null,
            "title" => ""
        ],
        [
            "text" => "Profile",
            "url" => "profile",
            "class" => $last == "profile" ? "active" : null,
            "title" => ""
        ]
    ]
];
