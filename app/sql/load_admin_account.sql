# extra tables

use g6t6;

CREATE TABLE if not exists admin (
  username varchar(20) NOT NULL,
  password_hash varchar(64) NOT NULL
);

INSERT INTO admin (username, password_hash) VALUES
('admin', '$2y$10$uRDXEiX888QQgXQoFLmK4OzqT1kTns.7gwYWyrZ71l9htRvK3NciS');