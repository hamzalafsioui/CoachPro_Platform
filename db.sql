CREATE DATABASE IF NOT EXISTS coach_pro;
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

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


CREATE TABLE sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);


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


CREATE TABLE statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sportif_id INT NOT NULL,
    coach_id INT NOT NULL,
    availability_id INT NOT NULL,
    status_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservation_sportif
        FOREIGN KEY (sportif_id) REFERENCES users(id)
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
        FOREIGN KEY (author_id) REFERENCES users(id)
        ON DELETE CASCADE
);


INSERT INTO statuses (name) VALUES
('pending'),
('confirmed'),
('completed'),
('cancelled');


CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_reservations_coach ON reservations(coach_id);
CREATE INDEX idx_reservations_sportif ON reservations(sportif_id);
CREATE INDEX idx_availabilities_coach ON availabilities(coach_id);
