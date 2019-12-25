DROP TABLE IF EXISTS comment_votes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS post_votes;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    password varchar(64) NOT NULL,

    PRIMARY KEY (id)
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT,
    title TEXT NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),

    PRIMARY KEY (id)
);

CREATE TABLE post_votes (
    id INT AUTO_INCREMENT,
    score BIT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),

    post_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id),

    PRIMARY KEY (id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT,
    comment_text TEXT,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    comment_reply_id INT,
    FOREIGN KEY (comment_reply_id) REFERENCES comments(id),
    
    PRIMARY KEY (id)
);

CREATE TABLE comment_votes (
    id INT AUTO_INCREMENT,
    score BIT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),

    comment_id INT NOT NULL,
    FOREIGN KEY (comment_id) REFERENCES comments(id),

    PRIMARY KEY (id)
);