# CoachPro - Your Personal Fitness Journey Starts Here 

CoachPro is a premium, professional platform designed to connect certified sports coaches with athletes (sportifs). It simplifies the process of finding specialized trainers, booking sessions, and tracking fitness progress through a modern, intuitive web interface.

##  Key Features

### For Athletes (Sportifs)

- **Coach Discovery**: Browse a network of certified professional coaches specialized in various sports (Football, Fitness, Yoga, etc.).
- **Easy Booking**: Real-time availability checks and instant session reservations.
- **Progress Tracking**: Monitor your fitness journey with detailed analytics and session history.
- **Reviews & Ratings**: Make informed decisions based on authentic feedback from the community.
- **Secure Profile**: Manage personal details and security settings with a glassmorphic dashboard.

### For Coaches

- **Profile Management**: Professional bio, certifications, and experience years display.
- **Availability Control**: Manage recurring and custom time slots for training sessions.
- **Reservation Dashboard**: Track upcoming sessions, manage requests, and view performance stats.
- **Earnings & Stats**: Quick overview of bookings and client engagement.

##  Tech Stack

- **Backend**: PHP 8.x
- **Database**: MySQL (using MySQLi driver)
- **Frontend**:
  - HTML5 & CSS3
  - [Tailwind CSS](https://tailwindcss.com/) (Custom UI with glassmorphism effects)
  - JavaScript (ES6+)
- **Icons**: Font Awesome 6
- **Typography**: Google Fonts (Outfit & Inter)

##  Project Structure

```text
CoachPro/
├── actions/        # Backend PHP logic for CRUD operations
├── assets/         # UI resources (CSS, JS, Images)
├── config/         # Database and system configuration
├── functions/      # Reusable business logic & database helpers
├── includes/       # Reusable UI components (header, footer, etc.)
├── pages/          # Application views (Auth, Coach, Sportif)
├── db.php          # Database connection handler
├── db.sql          # Database schema and initial data
└── index.php       # Landing page
```

##  Installation & Setup

1. **Clone the repository**:

   ```bash
   git clone [repository-url]
   cd CoachPro
   ```

2. **Database Setup**:

   - Create a new MySQL database named `coach_pro`.
   - Import the `db.sql` file into your database.
   - Update `config/database.php` (or relevant connection file) with your database credentials.

3. **Web Server**:

   - Serve the project folder using Apache or any PHP-compatible web server (e.g., XAMPP, WAMP, or built-in PHP server).
   - Ensure the server root points to the `CoachPro` directory.

4. **Access**:
   - Open your browser and navigate to `http://localhost/CoachPro`.

## Security

CoachPro implements industry-standard security measures, including:

- Password hashing for user accounts.
- Prepared statements using MySQLi to prevent SQL injection.
- Role-based access control (RBAC) for Coaches and Athletes.

##  License

...

---

