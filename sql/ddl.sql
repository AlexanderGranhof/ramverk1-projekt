DROP TABLE IF EXISTS comment_votes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS post_votes;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    password varchar(64) NOT NULL,

    PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE posts (
    id INT AUTO_INCREMENT,
    title TEXT NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    tags TEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE post_votes (
    id INT AUTO_INCREMENT,
    score INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),

    post_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id),

    UNIQUE INDEX `user_id:post_id` (user_id, post_id),

    PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE comments (
    id INT AUTO_INCREMENT,
    comment_text TEXT,
    post_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    comment_reply_id INT,
    FOREIGN KEY (comment_reply_id) REFERENCES comments(id),
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    answer BIT DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE comment_votes (
    id INT AUTO_INCREMENT,
    score INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    comment_id INT NOT NULL,
    FOREIGN KEY (comment_id) REFERENCES comments(id),

    UNIQUE INDEX `user_id:comment_id` (user_id, comment_id), 
    PRIMARY KEY (id, user_id, comment_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;