-- CREATE DATABASE IF NOT EXISTS apirone;
-- create the databases
CREATE DATABASE IF NOT EXISTS apirone;

-- create the users for each database
CREATE USER 'apirone'@'%' IDENTIFIED BY 'apirone';
GRANT CREATE, ALTER, INDEX, LOCK TABLES, REFERENCES, UPDATE, DELETE, DROP, SELECT, INSERT ON `apirone`.* TO 'apirone'@'%';

FLUSH PRIVILEGES;