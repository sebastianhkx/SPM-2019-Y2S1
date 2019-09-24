# extra tables

use g6t6;

CREATE TABLE if not exists admin (
  userid varchar(20) NOT NULL,
  password varchar(64) NOT NULL
);

#u: admin p: adminbios
INSERT INTO admin (userid, password) VALUES
('admin', '$2y$10$uRDXEiX888QQgXQoFLmK4OzqT1kTns.7gwYWyrZ71l9htRvK3NciS');




