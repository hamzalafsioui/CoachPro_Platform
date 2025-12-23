CREATE DATABASE IF NOT EXISTS coachPro;
USE coachPro;

-- Roles Table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE RESTRICT
);

-- Coach Profiles Table
CREATE TABLE coach_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    bio TEXT,
    experience_years INT,
    certifications TEXT,
    photo VARCHAR(255),
    rating_avg DECIMAL(3,2) DEFAULT 0.00,

    CONSTRAINT fk_coach_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

-- Sportif Table
CREATE TABLE sportifs (
    user_id INT PRIMARY KEY,

    CONSTRAINT fk_sportif_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

-- Sports Table
CREATE TABLE sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Coach-Sports Mapping
CREATE TABLE coach_sports (
    coach_id INT NOT NULL,
    sport_id INT NOT NULL,

    PRIMARY KEY (coach_id, sport_id),

    CONSTRAINT fk_cs_coach
        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cs_sport
        FOREIGN KEY (sport_id) REFERENCES sports(id)
        ON DELETE CASCADE
);

-- Availabilities (Specific Slots)
CREATE TABLE availabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,

    CONSTRAINT fk_availability_coach
        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
        ON DELETE CASCADE
);

-- Recurring Slots (Weekly Planning)
CREATE TABLE coach_recurring_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,

    CONSTRAINT fk_recurring_coach
        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
        ON DELETE CASCADE,

    UNIQUE KEY idx_coach_day_time (coach_id, day_of_week, start_time)
);

-- Statuses Table
CREATE TABLE statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Reservations Table
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sportif_id INT NOT NULL,
    coach_id INT NOT NULL,
    availability_id INT NOT NULL,
    status_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservation_sportif
        FOREIGN KEY (sportif_id) REFERENCES sportif(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reservation_coach
        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reservation_availability
        FOREIGN KEY (availability_id) REFERENCES availabilities(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservation_status
        FOREIGN KEY (status_id) REFERENCES statuses(id)
        ON DELETE RESTRICT
);

-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL UNIQUE,
    author_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_review_reservation
        FOREIGN KEY (reservation_id) REFERENCES reservations(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_review_author
        FOREIGN KEY (author_id) REFERENCES sportif(user_id)
        ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_reservations_coach ON reservations(coach_id);
CREATE INDEX idx_reservations_sportif ON reservations(sportif_id);
CREATE INDEX idx_availabilities_coach ON availabilities(coach_id);
CREATE INDEX idx_availabilities_date ON availabilities(date);
CREATE INDEX idx_reservations_created ON reservations(created_at);
CREATE INDEX idx_coach_sports_sport ON coach_sports(sport_id);

-- INSERT DATA

-- Roles
INSERT INTO roles (name) VALUES ('coach'), ('sportif');
s
-- Users
INSERT INTO users (role_id, firstname, lastname, email, password, phone) VALUES
(1, 'Hamza', 'Lafsioui', 'hamza.coach@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0612345678'),
(1, 'John', 'Doe', 'john.doe@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0600000001'),
(1, 'Alice', 'Smith', 'alice.smith@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0600000002'),
(2, 'Sara', 'Sportif', 'sara.sportif@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0698765432'),
(2, 'Bob', 'Johnson', 'bob.johnson@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0698765433');

-- Coach Profiles
INSERT INTO coach_profiles (user_id, bio, experience_years, certifications, photo, rating_avg) VALUES
(1, 'Professional fitness coach specializing in HIIT and strength training.', 5, 'Certified Personal Trainer, Crossfit L1', 'hamza.jpg', 4.80),
(2, 'Expert tennis coach for all levels.', 8, 'ATP Coach Certification', 'john.jpg', 4.50),
(3, 'Yoga instructor focusing on mindfulness and flexibility.', 3, '200h RYT Certification', 'alice.jpg', 4.90);

-- Sportifs
INSERT INTO sportif (user_id) VALUES
(4), (5);

-- Sports
INSERT INTO sports (name) VALUES ('Football'), ('Fitness'), ('Yoga'), ('Tennis'), ('Basketball'), ('Padel');

-- Coach Sports
INSERT INTO coach_sports (coach_id, sport_id) VALUES
(1, 2), 
(2, 4), 
(3, 3); 

-- Availabilities
INSERT INTO availabilities (coach_id, date, start_time, end_time) VALUES
(1, '2025-01-20', '09:00:00', '10:00:00'),
(1, '2025-01-20', '10:00:00', '11:00:00'),
(2, '2025-01-21', '14:00:00', '15:00:00'),
(3, '2025-01-22', '08:00:00', '09:00:00');

-- Recurring Slots
INSERT INTO coach_recurring_slots (coach_id, day_of_week, start_time, end_time) VALUES
(1, 'monday', '18:00:00', '19:00:00'),
(1, 'wednesday', '18:00:00', '19:00:00'),
(2, 'tuesday', '10:00:00', '12:00:00');

-- Statuses
INSERT INTO statuses (name) VALUES ('pending'), ('confirmed'), ('completed'), ('cancelled');

-- Reservations
INSERT INTO reservations (sportif_id, coach_id, availability_id, status_id, price) VALUES
(4, 1, 1, 2, 200.00), 
(5, 2, 3, 1, 300.00); 

-- Reviews
INSERT INTO reviews (reservation_id, author_id, rating, comment) VALUES
(1, 4, 5, 'Great coaching session! Highly recommend.');
