# EWU Student Academic & Predictive Analytics Portal 🎓

A full-stack web application designed for East West University (EWU) students to log, evaluate, and predict their academic performance across semesters. Built with procedural PHP, MySQL, and vanilla JavaScript, the system automates grade calculations based on the official EWU grading policy, provides dynamic CGPA analytics, and offers predictive goal modeling.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

---

## 📌 Core Features & Module Breakdown

| Feature | Description | Tech Stack |
| :--- | :--- | :--- |
| **Session-Based Authentication** | Isolated authentication ensuring students access only their specific course data using `$_SESSION['student_id']`. | PHP, MySQL |
| **Multi-Course Batch Entry** | Dynamic JavaScript row generation allowing multi-row course record insertions (1–7 courses) in a single submission. | JS, HTML5, PHP |
| **Automated Grade Computation** | Server-side mapping of numeric marks (0–100) to official EWU letter grades and grade points via `ewu_grade()`. | PHP |
| **Real-Time Grade Preview** | Instant client-side grade chip updates with responsive color coding as marks are typed into input fields. | JS, CSS3 |
| **CGPA & Analytics Dashboard** | Computes weighted Cumulative GPA, total completed credits, course counts, best grade point, and lowest grade point. | PHP, MySQL, CSS3 |
| **Motivational Feedback Banner** | Contextual banner styling (Gold, Green, or Blue) rendered dynamically based on CGPA performance tiers. | PHP, CSS3 |
| **Predictive GPA Target Calculator** | Computes exact next-semester GPA requirements based on current CGPA, earned credits, and target CGPA goals. | JS (Client-side) |
| **Academic Records Table & Delete** | Interactive record view with progress bar mark indicators and secure deletion with ownership validation. | PHP, MySQL, JS |
| **Printable Transcript Mode** | Styled layout optimized for window printing, hiding interactive controls via dedicated print stylesheets. | CSS3 (`@media print`) |
| **Persistent Theme Switcher** | Toggles between Light and Dark interface themes using custom CSS properties stored in `localStorage`. | JS, CSS Variables |

---

## 📊 Official EWU Grading Scale

The system computes grade points ($\text{GP}$) and letter grades automatically based on the official East West University grading scheme:

| Marks Range | Letter Grade | Grade Point ($\text{GP}$) |
| :---: | :---: | :---: |
| 80 – 100 | A+ | 4.00 |
| 75 – 79 | A | 3.75 |
| 70 – 74 | A- | 3.50 |
| 65 – 69 | B+ | 3.25 |
| 60 – 64 | B | 3.00 |
| 55 – 59 | C+ | 2.50 |
| 50 – 54 | C | 2.00 |
| 40 – 49 | D | 1.00 |
| 0 – 39 | F | 0.00 |

---

## 🧮 Core Analytical Formulas

### 1. Cumulative Grade Point Average (CGPA)
The system calculates weighted CGPA using total grade points multiplied by credit hours divided by total credits:

$$\text{CGPA} = \frac{\sum_{i=1}^{n} (\text{Grade Point}_i \times \text{Credits}_i)}{\sum_{i=1}^{n} \text{Credits}_i}$$

### 2. Required Next-Semester GPA (Predictive Calculator)
To determine the required GPA needed in upcoming credits to hit a target CGPA:

$$\text{Required GPA} = \frac{(\text{Target CGPA} \times \text{Total Future Credits}) - (\text{Current CGPA} \times \text{Current Credits})}{\text{Next Semester Credits}}$$

---

## 📁 Repository Structure

```text
ewu_portal/
├── db.php              # Database connection configuration (mysqli)
├── delete.php          # Record deletion logic with user-isolation check
├── ewu_portal.sql      # Database schema and table export
├── index.php           # Main portal dashboard (Analytics, Entry Form, Records Table)
├── login.php           # Student authentication page
├── process.php         # Server-side batch processing & grade calculation
├── style.css           # Global styles, glassmorphism UI, themes, & print rules
└── welcome.php         # Landing page
```

---

## 🚀 Local Installation & Setup

1. **Environment Setup:** Download and install [XAMPP](https://www.apachefriends.org/).
2. **Move Project Files:** Place the project folder into the local server directory:
   ```powershell
   C:\xampp\htdocs\ewu_portal
   ```
3. **Database Setup:**
   * Open XAMPP Control Panel and start **Apache** and **MySQL**.
   * Navigate to `http://localhost/phpmyadmin` in your web browser.
   * Create a new database named **`ewu_portal`**.
   * Select `ewu_portal` and click the **Import** tab. Choose `ewu_portal.sql` from the repository and run the import.
4. **Launch Application:** Open your browser and navigate to:
   ```text
   http://localhost/ewu_portal/login.php
   ```

---

## 👤 Academic Information

* **Course:**  Web Programming
* **Project Title:** EWU Student Academic & Predictive Analytics Portal
* **Developer:** Nishat Subha Mithela 
* **Institution:** East West University
