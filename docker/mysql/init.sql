-- 既にデータベースが存在する場合は削除
DROP DATABASE IF EXISTS posse;

-- MySQLのデータベースを作成
CREATE DATABASE posse;

-- 作成したデータベースを選択
USE posse;

DROP TABLE IF EXISTS users;

CREATE TABLE users(
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);

INSERT INTO users (email, password)
VALUES ('posse@example.com', 'password');

DROP TABLE IF EXISTS todos;

CREATE TABLE todos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  text VARCHAR(255),
  complete BOOLEAN,
  user_id INT NOT NULL,
  CONSTRAINT fk_user
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO todos (text, complete, user_id) VALUES
('寝る' , true , 1),
('起きる' , false , 1),
('ご飯食べる' , false , 1);

