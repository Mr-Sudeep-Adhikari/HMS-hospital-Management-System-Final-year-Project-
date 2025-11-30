# Hospital Management System (HMS)

## 🛠️ Technology Stack

*   **Frontend**: HTML5, Tailwind CSS, JavaScript, FontAwesome.
*   **Backend**: PHP (Vanilla).
*   **Database**: MySQL

## ⚙️ Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone <repository-url>
    cd HMS-hospital-Management-System-Final-year-Project-
    ```

2.  **Database Setup**
    *   Create a database named `myhmsdb` in your MySQL server.
    *   Import the `myhmsdb.sql` file provided in the root directory.

4.  **Run the Application**
    You can use the built-in PHP server for testing:
    ```bash
    php -S localhost:8000
    ```
    Access the application at `http://localhost:8000`.

## 🔑 Default Credentials

If you have run the database migration/fix scripts, the default passwords are:

| Role | Username / Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `admin123` |
| **Doctor** | *(Existing usernames)* | `doc123` |
| **Patient** | *(Existing emails)* | `pass123` |

> **Note:** For new registrations, the password you set will be used.

## 📂 Project Structure

*   `index.php` - Main landing page with Login/Registration tabs.
*   `admin-panel1.php` - Administrator Dashboard.
*   `doctor-panel.php` - Doctor Dashboard.
*   `patient_panel.php` - Patient Dashboard.
*   `config.php` - Database configuration and helper functions.
*   `func.php`, `func1.php`, `func2.php`, `func3.php` - Backend logic handlers.

## 🎨 UI Theme

The project uses the **Emerald Pulse** theme
