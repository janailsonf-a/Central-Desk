# 🛠 Central Desk

Help desk and internal request management system built with Laravel.

---

## 🚀 Overview

Central Desk is a web application designed to manage internal requests, support tickets, and workflows within organizations.

The system enables teams to create, track, and resolve requests efficiently, improving communication and operational processes.

---

## 🧠 Features

- 📌 Ticket creation and management  
- 🔄 Status tracking (open, in progress, resolved)  
- 👥 User roles and permissions  
- 📝 Comments and updates on tickets  
- 🔐 Authentication and access control  
- 📊 Organized workflow for internal processes  

---

## 🛠 Tech Stack

- PHP  
- Laravel  
- MySQL  
- Vite  
- Docker (optional)  

---

## 🏗 Architecture

The application follows a structured MVC architecture:

- **Models** → represent data and relationships  
- **Controllers** → handle application logic  
- **Views** → user interface (Blade templates)  
- **Services** → business rules and operations  

This structure ensures maintainability and scalability.

---

## 📦 Installation

```bash
git clone https://github.com/janailsonf-a/Central-Desk
cd Central-Desk

composer install
cp .env.example .env
php artisan key:generate
```

---

## 🗄 Database

```bash
php artisan migrate
```

---

## ▶️ Running

```bash
php artisan serve
```

---

## 📸 API Preview

### 🔐 Authentication - Login

Example request:

```json
{
  "email": "admin@centraldesk.com",
  "password": "123456"
}
```

Response:

```json
{
  "message": "Login realizado com sucesso.",
  "token": "...",
  "user": {
    "id": 1,
    "name": "Admin Demo",
    "email": "admin@centraldesk.com"
  }
}
```

---

## 🔐 Authentication

The API uses token-based authentication.  
After login, the token must be included in all protected requests.

---

## 🌍 Use Cases

- Internal company support systems  
- IT help desk platforms  
- Service request management  
- Workflow organization systems  

---

## 📸 Screenshots

<img width="1920" height="935" src="https://github.com/user-attachments/assets/b60bf747-422e-4a4e-be81-f86f2b8eeba1" />
<img width="1920" height="935" src="https://github.com/user-attachments/assets/cc54c8c2-4c0f-4442-9934-3796094a4cf7" />
<img width="1920" height="935" src="https://github.com/user-attachments/assets/c7637a05-3eb8-4825-93f6-c7b2d837a844" />

---

## 👨‍💻 Author

Developed by Janailson Almeida
