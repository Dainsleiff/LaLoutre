-- CREATE TABLE image (
-- id INTEGER primary key autoincrement,
-- path text,
-- category text,
-- comment text
-- );

-- ALTER TABLE image ADD vote int;

-- ALTER TABLE image ADD nbvote int;

-- ALTER TABLE image ADD local varchar(255) NOT NULL DEFAULT 'true';

UPDATE image SET local =  'true';

