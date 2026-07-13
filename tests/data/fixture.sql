DELETE FROM status;
DELETE FROM movie;
DELETE FROM user;

START TRANSACTION;

SET sql_mode = 'STRICT_TRANS_TABLES';

INSERT INTO user (id, date_update, email, password, nickname) VALUES
(1001, '2020-01-01', 'user1@example.com', SHA1('user1'), 'user1'),
(1002, '2020-01-02', 'user2@example.com', SHA1('user2'), 'user2'),
(1003, '2020-01-03', 'user3@example.com', SHA1('user3'), 'user3');

INSERT INTO movie (id, date_update, title, year, date_search, search_count, identity, quality, score, source) VALUES
(96446, '2020-02-01', 'Willow', 1988, NULL, 0, 0, 0, 0, NULL),
(103064, '2020-02-02', 'Terminator 2', 1991, '2020-03-01', 2, 3, 4, 5, 'some link'),
(28650488, '2020-02-03', 'The Super Mario Galaxy Movie', 2026, NULL, 0, 0, 0, 0, NULL);

INSERT INTO status (id, user_id, movie_id, date_update, is_latest, rating) VALUES
(2001, 1001, 96446, '2020-01-01', 0, 3),
(2002, 1001, 96446, '2020-01-02', 1, 5),
(2003, 1001, 103064, '2020-01-03', 0, 2),
(2004, 1001, 103064, '2020-01-04', 1, 0),
(2005, 1002, 96446, '2020-01-02', 1, 1),
(2006, 1002, 28650488, '2020-01-02', 1, 5),
(2007, 1003, 96446, '2020-01-02', 1, 1);

COMMIT;
