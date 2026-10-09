# 💼 OpenLedger Lite
![OpenLedger Banner](assets/screenshots/banner.png)
> A simple, elegant, and open-source financial tracking system for individuals, NGOs, and small teams.

![PHP](https://img.shields.io/badge/PHP-8+-blue)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-UI-teal)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 🌍 Overview

**OpenLedger Lite** is a lightweight financial management system designed to help users:

* 💰 Track income
* 💸 Manage expenses
* 📊 Visualize financial performance

Built with simplicity and usability in mind, it is ideal for:

* Individuals managing personal finances
* NGOs tracking funds and donations
* Small businesses monitoring cash flow
* Community groups and savings clubs

---

## 🖼️ Screenshots

### 📊 Dashboard

![Dashboard](assets/screenshots/dashboard.png)

---

### 💰 Income Management

![Income](assets/screenshots/income.png)

---

### 💸 Expense Management

![Expenses](assets/screenshots/expenses.png)

---

### ➕ Add Income

![Add Income](assets/screenshots/add-income.png)

---

## ✨ Features

### 📊 Dashboard

* Financial summary (Income, Expenses, Balance)
* Monthly trends (Income vs Expenses)
* Expense category breakdown (Pie Chart)

### 💰 Income Management

* Add income entries
* Dynamic source creation (auto-add new sources)
* Contributor tracking
* Edit and delete entries
* Date, source and text filtering with pagination
* CSV export of the filtered results
* Summary cards

### 💸 Expense Management

* Add expenses
* Dynamic category creation
* Description support
* Edit and delete entries
* Date, category and text filtering with pagination
* CSV export of the filtered results
* Summary cards

### 🔐 Authentication

* Login with hashed passwords, session-ID regeneration and brute-force throttling
* CSRF protection on every state-changing form, POST-only logout
* Output escaping and strict server-side validation

### 📁 Clean Architecture

* Organized folder structure
* PDO-based database connection
* Modular and extendable

---

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/kettalevi/openledger-lite.git
cd openledger-lite
```

---

### 2. Setup Database

Create database:

```
openledger_lite
```

Import:

```
database.sql
```

---

### 3. Configure Database

Edit `includes/config.php`, or (recommended) create a git-ignored `includes/config.local.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'openledger_lite');
define('DB_USER', 'root');
define('DB_PASS', '');
define('CURRENCY', 'UGX');   // shown across the app
```

Environment variables `OL_DB_HOST`, `OL_DB_NAME`, `OL_DB_USER`, `OL_DB_PASS`, `OL_CURRENCY` and `OL_BASE_URL` also work.

---

### 4. Create First User

From the command line (the password is hashed, min. 8 characters):

```bash
php bin/create-user.php "Admin" you@example.com
```

---

### 5. Run the Project

Open:

```
http://localhost/openledger-lite
```

Log in with the user you created in step 4.

---

## 🧠 Project Structure

```
openledger-lite/
│
├── auth/
├── dashboard/
├── income/
├── expenses/
├── includes/
├── assets/
│   └── screenshots/
├── database.sql
└── README.md
```

---

## 🚀 Roadmap

* 📄 PDF Reports
* 🏦 Bank Account Integration
* 👥 Multi-user roles
* 📊 Advanced analytics
* 🌐 API support

---

## 💡 Why OpenLedger Lite?

Most financial tools are either:

* Too complex
* Too expensive
* Too rigid

**OpenLedger Lite solves this by being:**

* Simple
* Flexible
* Open-source
* Developer-friendly

---

## 🤝 Contributing

Contributions are welcome!

* Fork the repo
* Create a feature branch
* Submit a pull request

---

## 📜 License

This project is licensed under the MIT License.

---

## 👨‍💻 Author

**Martin Levi K.A.**
Systems Developer | IT Solutions Architect | Technical Author

Passionate about building practical systems that solve real-world problems across Africa and beyond.

---

## 🌟 Support

If you find this project useful:

⭐ Star the repository
🍴 Fork it
📢 Share it

---

## 🔥 Commercial Version

A premium version with advanced features (multi-user, reporting, integrations) is under development.

Stay tuned 🚀
