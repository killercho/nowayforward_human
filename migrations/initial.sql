SET @username = 'default';
SET @password = 'Password1234';

-- Check if the user exists
SELECT COUNT(*) INTO @user_exists
FROM mysql.user
WHERE user = @username AND host = 'localhost';

-- Create the user if it does not exist
IF @user_exists = 0 THEN
    CREATE USER @username@'localhost' IDENTIFIED BY @password;
END IF;

GRANT ALL PRIVILEGES ON db.* TO @username@'localhost';
FLUSH PRIVILEGES;

-- Create the database if it does not exist
CREATE DATABASE IF NOT EXISTS db;
USE db;

-- Create the users table if it does not exist
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
