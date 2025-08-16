# My Simple Blog

This is a simple blog application built with PHP and MySQL. It is designed to be a starting point for learning about web development fundamentals, with a strong focus on implementing modern security practices.

The application allows users to register, log in, create blog posts, and view a paginated list of posts. Users with elevated permissions (`admin` or `editor`) can create new posts, while regular members can only view them.

---

### **Key Features**

* **User Authentication:** Secure user registration and login system.
* **Role-Based Access Control:** Differentiates between `admin`, `editor`, and `member` users.
* **Blog Post Management:** Users with correct roles can create new blog posts.
* **Search and Pagination:** Allows users to search for posts and browse through pages of content.
* **Secure Sessions:** Uses PHP sessions to maintain user login state securely.

---

### **Security Measures Implemented**

This application was developed with a strong emphasis on security to protect against common web vulnerabilities.

* **1. Prepared Statements (SQL Injection Prevention)**
    All database queries (`SELECT`, `INSERT`) use **PDO Prepared Statements**. This separates the SQL command from user data, effectively preventing malicious SQL code from being executed and protecting the database from injection attacks.

* **2. Password Hashing**
    User passwords are **never stored in plain text**. Instead, they are hashed using PHP's built-in `password_hash()` and verified with `password_verify()`. This makes it impossible to reverse-engineer passwords even if the database is compromised.

* **3. Form Validation**
    * **Server-Side:** All form submissions are validated on the server to ensure data is clean and meets integrity requirements before being processed or stored in the database.
    * **Client-Side:** HTML `required` and other attributes are used to provide immediate user feedback and enhance the user experience.

* **4. Role-Based Access Control (RBAC)**
    User roles are assigned and stored in the database. Access to sensitive features, such as creating a new blog post, is strictly controlled based on the user's role, ensuring that only authorized users can perform specific actions.

* **5. Output Sanitization (XSS Prevention)**
    All user-submitted content (`title`, `content`, `username`) is passed through `htmlspecialchars()` before being displayed on the page. This prevents Cross-Site Scripting (XSS) attacks by converting special characters into their HTML entities.

---

### **Getting Started**

#### **Prerequisites**
* A web server with PHP installed (e.g., XAMPP, MAMP).
* A MySQL or MariaDB database.

#### **Step 1: Database Setup**
1.  Create a new database named `new_app_db`.
2.  Run the following SQL queries to create the necessary tables:

    ```sql
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'member',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        user_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ```

#### **Step 2: Configure the Application**
1.  Open the `db_connect.php` file.
2.  Update the database credentials (`$user`, `$password`, `$port`) to match your local setup.

#### **Step 3: Run the Application**
1.  Place all the project files in your web server's root directory (e.g., `C:\xampp\htdocs\new_app`).
2.  Open your browser and navigate to `http://localhost/new_app/index.php`.

---

### **Usage**
* **Register:** Create a new user account. By default, your role will be `member`.
* **Login:** Log in with your new user account.
* **Create Posts:** To test the post creation feature, you will need to manually change your user's role to `admin` or `editor` directly in the `users` table of your database.
