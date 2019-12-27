<?php
/**
 * Supply the basis for the navbar as an array.
 */
return [
    // Use for styling the menu
    "wrapper" => null,
    "class" => "my-navbar rm-default rm-desktop",
 
    // Here comes the menu items
    "items" => [
        [
            "text" => "Hem",
            "url" => "",
            "title" => "Första sidan, börja här.",
        ],
        [
            "text" => "Posts",
            "url" => "posts",
            "title" => ""
        ],
        [
            "text" => "User",
            "url" => "user",
            "title" => ""
        ]
    ],
];
