CREATE DATABASE IF NOT EXISTS coachProTest;
USE coachProTest;

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
        FOREIGN KEY (sportif_id) REFERENCES sportifs(user_id)
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
        FOREIGN KEY (author_id) REFERENCES sportifs(user_id)
        ON DELETE CASCADE
);

-- Review Replies Table (coach replies to reviews)
CREATE TABLE review_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    coach_id INT NOT NULL,
    reply_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_reply_review
        FOREIGN KEY (review_id) REFERENCES reviews(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reply_coach
        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
        ON DELETE CASCADE,

    UNIQUE KEY idx_review_reply (review_id)
);

-- Client Plans Table (subscription plans)
CREATE TABLE client_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2),
    duration_days INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Coach-Client Relationships Table (tracks client relationships with coaches)
CREATE TABLE coach_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    sportif_id INT NOT NULL,
    plan_id INT,
    status ENUM('active', 'inactive', 'paused') DEFAULT 'active',
    progress INT DEFAULT 0 CHECK (progress BETWEEN 0 AND 100),
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_cc_coach
        FOREIGN KEY (coach_id) REFERENCES coach_profiles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_sportif
        FOREIGN KEY (sportif_id) REFERENCES sportifs(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_plan
        FOREIGN KEY (plan_id) REFERENCES client_plans(id)
        ON DELETE SET NULL,

    UNIQUE KEY idx_coach_sportif (coach_id, sportif_id)
);

-- Indexes for performance
CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_reservations_coach ON reservations(coach_id);
CREATE INDEX idx_reservations_sportif ON reservations(sportif_id);
CREATE INDEX idx_availabilities_coach ON availabilities(coach_id);
CREATE INDEX idx_availabilities_date ON availabilities(date);
CREATE INDEX idx_reservations_created ON reservations(created_at);
CREATE INDEX idx_coach_sports_sport ON coach_sports(sport_id);
CREATE INDEX idx_coach_clients_coach ON coach_clients(coach_id);
CREATE INDEX idx_coach_clients_sportif ON coach_clients(sportif_id);
CREATE INDEX idx_coach_clients_status ON coach_clients(status);
CREATE INDEX idx_review_replies_review ON review_replies(review_id);
CREATE INDEX idx_review_replies_coach ON review_replies(coach_id);

-- INSERT DATA

-- Roles
INSERT INTO roles (name) VALUES ('coach'), ('sportif');

-- Users (password for all: 'password123')
INSERT INTO users (role_id, firstname, lastname, email, password, phone) VALUES
-- Coaches
(1, 'Hamza', 'Lafsioui', 'hamza.coach@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0612345678'),
(1, 'John', 'Doe', 'john.doe@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0600000001'),
(1, 'Alice', 'Smith', 'alice.smith@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0600000002'),
(1, 'Michael', 'Brown', 'michael.brown@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0600000003'),
(1, 'Sarah', 'Wilson', 'sarah.wilson@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0600000004'),
-- Sportifs
(2, 'Sara', 'Sportif', 'sara.sportif@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0698765432'),
(2, 'Bob', 'Johnson', 'bob.johnson@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0698765433'),
(2, 'Emma', 'Davis', 'emma.davis@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0698765434'),
(2, 'James', 'Miller', 'james.miller@email.com', '$2y$12$rJsON6G/CBvfvR26cSQeGu1lOJRjWQZEHxGiyZIcVQvzb4uajXEjG', '0698765435');

-- Coach Profiles
INSERT INTO coach_profiles (user_id, bio, experience_years, certifications, photo, rating_avg) VALUES
(1, 'Professional fitness coach specializing in HIIT and strength training. With over 5 years of experience, I help athletes achieve their peak performance through personalized training programs.', 5, 'Certified Personal Trainer, Crossfit L1, Strength and Conditioning Specialist', 'hamza.jpg', 4.80),
(2, 'Expert tennis coach for all levels. Former professional player with 8 years of coaching experience. Specialized in technique improvement and match strategy.', 8, 'ATP Coach Certification, USPTA Professional', 'john.jpg', 4.50),
(3, 'Yoga instructor focusing on mindfulness and flexibility. I combine traditional yoga practices with modern movement science to help you find balance and strength.', 3, '200h RYT Certification, Yoga Therapy Certification', 'alice.jpg', 4.90),
(4, 'Elite basketball coach with a passion for developing young talent. I focus on fundamentals, team play, and mental toughness.', 6, 'USA Basketball Certified Coach, Level 3', 'michael.jpg', 4.75),
(5, 'Cardio and endurance specialist. I design programs for runners, cyclists, and triathletes looking to improve their performance and break personal records.', 4, 'Certified Running Coach, Triathlon Coach Level 2', 'sarah.jpg', 4.85);

-- Sportifs
INSERT INTO sportifs (user_id) VALUES
(6), (7), (8), (9);

-- Sports
INSERT INTO sports (name) VALUES 
('Football'), 
('Fitness'), 
('Yoga'), 
('Tennis'), 
('Basketball'), 
('Padel'),
('Cardio'),
('Strength Training'),
('HIIT'),
('Crossfit');

-- Coach Sports (linking coaches to their specialties)
INSERT INTO coach_sports (coach_id, sport_id) VALUES
-- Hamza: Fitness, Strength Training, HIIT
(1, 2), (1, 8), (1, 9),
-- John: Tennis, Padel
(2, 4), (2, 6),
-- Alice: Yoga, Fitness
(3, 3), (3, 2),
-- Michael: Basketball, Fitness
(4, 5), (4, 2),
-- Sarah: Cardio, Fitness, HIIT
(5, 7), (5, 2), (5, 9); 

-- Availabilities (sample slots - using fixed dates for consistency)
-- Note: In production, these would be generated from recurring slots
-- Using dates starting from today and going forward
INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available) VALUES
-- Coach 1 (Hamza) - Available slots (IDs 1-6)
(1, '2025-01-20', '09:00:00', '10:00:00', 0),  -- ID 1: Booked (completed)
(1, '2025-01-20', '10:00:00', '11:00:00', 1),  -- ID 2: Available
(1, '2025-01-20', '18:00:00', '19:00:00', 1),  -- ID 3: Available
(1, '2025-01-21', '09:00:00', '10:00:00', 0),  -- ID 4: Booked (confirmed)
(1, '2025-01-21', '18:00:00', '19:00:00', 0),  -- ID 5: Booked (pending)
(1, '2025-01-23', '18:00:00', '19:00:00', 1),  -- ID 6: Available
-- Coach 2 (John) - Available slots (IDs 7-9)
(2, '2025-01-20', '14:00:00', '15:00:00', 0),  -- ID 7: Booked (completed)
(2, '2025-01-21', '10:00:00', '12:00:00', 0),  -- ID 8: Booked (pending)
(2, '2025-01-22', '14:00:00', '15:00:00', 1),  -- ID 9: Available
-- Coach 3 (Alice) - Available slots (IDs 10-12)
(3, '2025-01-20', '08:00:00', '09:00:00', 0),  -- ID 10: Booked (completed)
(3, '2025-01-21', '08:00:00', '09:00:00', 1),  -- ID 11: Available
(3, '2025-01-22', '17:00:00', '18:00:00', 1),  -- ID 12: Available
-- Coach 4 (Michael) - Available slots (IDs 13-14)
(4, '2025-01-20', '16:00:00', '17:00:00', 0),  -- ID 13: Booked (confirmed)
(4, '2025-01-21', '16:00:00', '17:00:00', 1),  -- ID 14: Available
-- Coach 5 (Sarah) - Available slots (IDs 15-17)
(5, '2025-01-20', '07:00:00', '08:00:00', 0),  -- ID 15: Booked (confirmed)
(5, '2025-01-21', '07:00:00', '08:00:00', 1),  -- ID 16: Available
(5, '2025-01-22', '19:00:00', '20:00:00', 1);  -- ID 17: Available

-- Recurring Slots (weekly schedule templates)
INSERT INTO coach_recurring_slots (coach_id, day_of_week, start_time, end_time) VALUES
-- Coach 1 (Hamza) - Monday and Wednesday evenings
(1, 'monday', '18:00:00', '19:00:00'),
(1, 'wednesday', '18:00:00', '19:00:00'),
(1, 'friday', '18:00:00', '19:00:00'),
-- Coach 2 (John) - Tuesday mornings
(2, 'tuesday', '10:00:00', '12:00:00'),
(2, 'thursday', '14:00:00', '16:00:00'),
-- Coach 3 (Alice) - Morning yoga sessions
(3, 'monday', '08:00:00', '09:00:00'),
(3, 'wednesday', '08:00:00', '09:00:00'),
(3, 'friday', '08:00:00', '09:00:00'),
(3, 'sunday', '17:00:00', '18:00:00'),
-- Coach 4 (Michael) - Afternoon basketball
(4, 'monday', '16:00:00', '17:00:00'),
(4, 'wednesday', '16:00:00', '17:00:00'),
(4, 'friday', '16:00:00', '17:00:00'),
-- Coach 5 (Sarah) - Early morning cardio
(5, 'tuesday', '07:00:00', '08:00:00'),
(5, 'thursday', '07:00:00', '08:00:00'),
(5, 'saturday', '07:00:00', '08:00:00');

-- Statuses
INSERT INTO statuses (name) VALUES ('pending'), ('confirmed'), ('completed'), ('cancelled');

-- Reservations (sample bookings)
-- Note: sportif_id references user_id from sportifs table
-- status_id: 1=pending, 2=confirmed, 3=completed, 4=cancelled
INSERT INTO reservations (sportif_id, coach_id, availability_id, status_id, price) VALUES
-- Completed sessions (status_id = 3)
(6, 1, 1, 3, 50.00),  -- Sara (user_id 6) booked with Hamza (coach_id 1) - availability_id 1 (completed)
(7, 2, 7, 3, 75.00),  -- Bob (user_id 7) booked with John (coach_id 2) - availability_id 7 (completed)
(8, 3, 10, 3, 40.00), -- Emma (user_id 8) booked with Alice (coach_id 3) - availability_id 10 (completed)
-- Confirmed/Upcoming sessions (status_id = 2)
(6, 1, 4, 2, 50.00),  -- Sara has upcoming session with Hamza - availability_id 4 (confirmed)
(7, 4, 13, 2, 60.00), -- Bob has upcoming session with Michael - availability_id 13 (confirmed)
(9, 5, 15, 2, 45.00), -- James has upcoming session with Sarah - availability_id 15 (confirmed)
-- Pending sessions (status_id = 1)
(8, 2, 8, 1, 75.00),  -- Emma requested session with John - availability_id 8 (pending)
(9, 1, 5, 1, 50.00);  -- James requested session with Hamza - availability_id 5 (pending) 

-- Reviews (reviews for completed reservations)
INSERT INTO reviews (reservation_id, author_id, rating, comment) VALUES
(1, 6, 5, 'Amazing coach! Pushed me to my limits and helped me achieve my fitness goals. Highly recommend!'),
(2, 7, 4, 'Great tennis coach! Very patient and knowledgeable. My technique has improved significantly.'),
(3, 8, 5, 'Best yoga instructor I have ever had. The sessions are relaxing yet challenging. Perfect balance!');

-- Client Plans (subscription plans available)
INSERT INTO client_plans (name, description, price, duration_days) VALUES
('Premium - Personal Training', 'One-on-one personalized training sessions with dedicated attention and custom workout plans.', 200.00, 30),
('Standard - HIIT', 'High-intensity interval training sessions in small groups for maximum results.', 150.00, 30),
('Basic - Strength', 'Fundamental strength training program focusing on building muscle and power.', 100.00, 30),
('Premium - Cardio', 'Advanced cardiovascular training with heart rate monitoring and performance tracking.', 180.00, 30),
('Elite - Complete Package', 'Comprehensive training package including all specialties with nutrition guidance.', 300.00, 30);

-- Coach-Client Relationships (tracking when clients joined each coach and their plans)
-- This data is derived from first reservation dates and assigned plans
-- Note: Only active relationships are stored here. Pending reservations create relationships when confirmed.
INSERT INTO coach_clients (coach_id, sportif_id, plan_id, status, progress, joined_at) VALUES
-- Sara (sportif_id 6) relationships
(1, 6, 1, 'active', 75, '2024-11-15 10:00:00'),  -- Joined Hamza on Nov 15, Premium Personal Training, 75% progress
-- Bob (sportif_id 7) relationships  
(2, 7, 2, 'active', 60, '2024-12-01 14:00:00'),  -- Joined John on Dec 1, Standard HIIT, 60% progress
(4, 7, 4, 'active', 45, '2024-12-10 16:00:00'),  -- Also with Michael, Premium Cardio, 45% progress
-- Emma (sportif_id 8) relationships
(3, 8, 3, 'active', 85, '2024-10-20 08:00:00'),  -- Joined Alice on Oct 20, Basic Strength, 85% progress
-- James (sportif_id 9) relationships
(5, 9, 4, 'active', 55, '2024-12-05 07:00:00');  -- Joined Sarah on Dec 5, Premium Cardio, 55% progress
