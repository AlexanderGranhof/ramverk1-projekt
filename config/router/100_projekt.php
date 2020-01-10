<?php
/**
 * Load the stylechooser as a controller class.
 */
return [
    "routes" => [
        [
            "info" => "Style chooser.",
            "mount" => "posts",
            "handler" => "\Algn\Controller\PostsController",
        ],
        [
            "mount" => "profile",
            "handler" => "\Algn\Controller\UserController"
        ],
        [
            "mount" => "popular",
            "handler" => "\Algn\Controller\PopularController"
        ],
        [
            "mount" => "tags",
            "handler" => "\Algn\Controller\TagsController"
        ],
        [
            "mount" => "about",
            "handler" => "\Algn\Controller\AboutController"
        ],
        [
            "mount" => "reset",
            "handler" => "\Algn\Controller\ResetController",
        ],
        [
            "mount" => "",
            "handler" => "\Algn\Controller\HomeController",
        ],
    ]
];
