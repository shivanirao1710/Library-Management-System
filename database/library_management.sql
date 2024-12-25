-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

-- 1. Create the `users` table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'member') NOT NULL
);

-- 2. Create the `categories` table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE
);

-- 3. Create the `books` table
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `genre` VARCHAR(100) NOT NULL,
    `publication_year` YEAR NOT NULL,
    `total_quantity` INT NOT NULL,
    `available_quantity` INT NOT NULL
);

-- 4. Create the `members` table
CREATE TABLE IF NOT EXISTS `members` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- 5. Create the `transactions` table
CREATE TABLE IF NOT EXISTS `transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT NOT NULL,
    `member_id` INT NOT NULL,
    `issue_date` DATE NOT NULL,
    `return_date` DATE,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`),
    FOREIGN KEY (`member_id`) REFERENCES `members`(`id`)
);

-- 6. Create the `book_categories` table (Many-to-Many relationship between books and categories)
CREATE TABLE IF NOT EXISTS `book_categories` (
    `book_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
    PRIMARY KEY (`book_id`, `category_id`)
);

-- Insert sample data into `users` table
INSERT INTO users (username, password, role) VALUES
('admin1', 'adminpass1', 'admin'),
('admin2', 'adminpass2', 'admin'),
('member1', 'memberpass1', 'member'),
('member2', 'memberpass2', 'member'),
('member3', 'memberpass3', 'member');

-- Insert sample data into `categories` table
INSERT INTO categories (name) VALUES
('Fiction'),
('Dystopian'),
('Romance'),
('Science Fiction'),
('Non-fiction');

-- Insert sample data into `books` table
INSERT INTO books (title, author, genre, publication_year, total_quantity, available_quantity) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', 1925, 10, 10),
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', 1960, 8, 8),
('1984', 'George Orwell', 'Dystopian', 1949, 5, 5),
('The Catcher in the Rye', 'J.D. Salinger', 'Fiction', 1951, 12, 12),
('Pride and Prejudice', 'Jane Austen', 'Romance', 1813, 6, 6);

-- Insert sample data into `members` table
INSERT INTO members (user_id) VALUES
(3),  -- member1 (user_id 3)
(4),  -- member2 (user_id 4)
(5);  -- member3 (user_id 5)

-- Insert sample data into `transactions` table
INSERT INTO transactions (book_id, member_id, issue_date) VALUES
(1, 1, '2024-01-15'),  -- member1, book_id 1
(2, 2, '2024-01-20'),  -- member2, book_id 2
(3, 3, '2024-01-22'),  -- member3, book_id 3
(4, 1, '2024-01-25'),  -- member1, book_id 4
(5, 2, '2024-02-01');  -- member2, book_id 5

-- Insert sample data into `book_categories` table (Many-to-Many relationship)
INSERT INTO book_categories (book_id, category_id) VALUES
(1, 1),  -- The Great Gatsby - Fiction
(2, 1),  -- To Kill a Mockingbird - Fiction
(3, 2),  -- 1984 - Dystopian
(4, 1),  -- The Catcher in the Rye - Fiction
(5, 3);  -- Pride and Prejudice - Romance

-- Trigger to update available quantity when a book is issued
DELIMITER //
CREATE TRIGGER update_available_quantity_on_issue
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    UPDATE books
    SET available_quantity = available_quantity - 1
    WHERE id = NEW.book_id;
END;
//
DELIMITER ;

-- Trigger to update available quantity when a book is returned
DELIMITER //
CREATE TRIGGER update_available_quantity_on_return
AFTER UPDATE ON transactions
FOR EACH ROW
BEGIN
    IF NEW.return_date IS NOT NULL AND OLD.return_date IS NULL THEN
        UPDATE books
        SET available_quantity = available_quantity + 1
        WHERE id = NEW.book_id;
    END IF;
END;
//
DELIMITER ;
