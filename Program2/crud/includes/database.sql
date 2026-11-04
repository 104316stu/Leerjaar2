-- Paste this in the SQL tab of phpMyAdmin, with your database selected.
-- Old users and messages tables are renamed to a backup, not deleted.

SET @backup = UNIX_TIMESTAMP();

SET @sql = (SELECT IF(COUNT(*) > 0, CONCAT('RENAME TABLE messages TO messages_backup_', @backup), 'DO 0')
    FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'messages');
PREPARE statement FROM @sql;
EXECUTE statement;
DEALLOCATE PREPARE statement;

SET @sql = (SELECT IF(COUNT(*) > 0, CONCAT('RENAME TABLE users TO users_backup_', @backup), 'DO 0')
    FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'users');
PREPARE statement FROM @sql;
EXECUTE statement;
DEALLOCATE PREPARE statement;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    category ENUM('general', 'question', 'idea') NOT NULL DEFAULT 'general',
    priority TINYINT NOT NULL,
    event_date DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- This account only owns the example messages. Its password is random and unknown.
INSERT INTO users (id, username, password_hash) VALUES
(1, 'demo', '$2y$12$KHVy8RkqWyVM.eQWQgYepOd.zHP/sbghax9.dk.UEhYcd3DI6fQ2K');

INSERT INTO messages (user_id, title, message, category, priority, event_date) VALUES
(1, 'Welcome to the board', 'This is the first message on the board.', 'general', 3, '2026-11-04'),
(1, 'Project deadline', 'Who else is still working on the CRUD project?', 'question', 5, '2026-11-20'),
(1, 'Study group', 'We could meet in the library after class.', 'idea', 2, '2026-11-10'),
(1, 'Room change', 'The PHP class has moved to another room this week.', 'general', 4, '2026-11-06'),
(1, 'Best PHP tutorial', 'Which tutorial helped you most with PDO?', 'question', 1, '2026-11-13');
