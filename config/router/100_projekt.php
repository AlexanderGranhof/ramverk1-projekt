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
            "mount" => "user",
            "handler" => "\Algn\Controller\UserController"
        ],
        [
            "mount" => "",
            "handler" => "\Algn\Controller\HomeController"
        ]
    ]
];
