<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 🎟️ Event Management & Ticketing System (Backend)

This project is a **backend system** for managing events, ticketing, and user roles (Attendees, Organizers, and Admins) using **Laravel 10**.  
It includes authentication with Sanctum, event and ticket management, orders, notifications, PDF generation for tickets, and QR code support.

---

## 🚀 Features

-   User authentication & role management (attendee, organizer, admin)
-   Event creation, updates, and approval system
-   Ticket types and orders with validation and QR codes
-   Notifications (database + email)
-   PDF ticket download (DomPDF)
-   Admin dashboard with statistics
-   RESTful API with structured routes and role-based middleware

---

## ⚙️ Installation & Setup

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/your-username/event-ticketing-backend.git
cd event-ticketing-backend
2️⃣ Install Dependencies
bash

composer install
3️⃣ Setup Environment
Copy the .env.example file:

bash

cp .env.example .env
Update .env with your database and email credentials (example for Gmail):

env

APP_NAME="Event Management"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_ticketing_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_gmail@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
⚠️ For Gmail: You must enable App Passwords.

4️⃣ Run Migrations & Seeders
bash

php artisan migrate --seed
5️⃣ Start Development Server
bash

php artisan serve
🔑 Authentication
The API uses Laravel Sanctum for authentication.

Register or Login to get a Bearer Token.

Then include it in your requests:

makefile

Authorization: Bearer your_token_here
📡 API Endpoints
🔓 Public Routes
Method	Endpoint	Description
POST	/register	Register a new user
POST	/login	Login and get token
GET	/events	Get all events
GET	/events/{event}	Get single event details
GET	/events/{id}/tickets	Get tickets for an event
GET	/qr	Generate a QR code
GET	/pdf	Generate sample PDF

🔐 Protected Routes (Require Authentication)
👤 Auth Routes
Method	Endpoint	Description
POST	/logout	Logout user
GET	/user	Get authenticated user
PUT	/user/profile	Update profile
PUT	/user/password	Update password

🔔 Notification Routes
Method	Endpoint	Description
GET	/notifications	Get all notifications
POST	/notifications/{id}/read	Mark a notification as read
POST	/notifications/mark-all-read	Mark all as read
GET	/notifications/unread-count	Count unread notifications

🎟️ Attendee Routes (role: attendee)
Method	Endpoint	Description
POST	/events/{event}/orders	Place an order for tickets
GET	/user/orders	Get user’s orders
GET	/orders/{order}	View single order
GET	/user/tickets	Get user’s tickets
GET	/tickets/{ticket}	View single ticket
GET	/tickets/{ticket}/download	Download ticket as PDF

🎤 Organizer Routes (role: organizer, admin)
Method	Endpoint	Description
GET	/organizer/events	Get organizer’s events
POST	/organizer/events	Create new event
POST	/organizer/events/{event}	Update event
DELETE	/organizer/events/{event}	Delete event
POST	/organizer/events/{event}/ticket-types	Create ticket type
PUT	/organizer/ticket-types/{ticketType}	Update ticket type
DELETE	/organizer/ticket-types/{ticketType}	Delete ticket type
POST	/tickets/{ticket}/validate	Validate scanned ticket

🛠️ Admin Routes (role: admin)
Method	Endpoint	Description
GET	/admin/dashboard	Get admin dashboard stats
GET	/admin/events	Get all events
POST	/admin/events/{event}/status	Approve or reject event

📂 Project Structure
lua

app/
 ├── Http/
 │   ├── Controllers/
 │   │   ├── AuthController.php
 │   │   ├── DashboardController.php
 │   │   ├── EventController.php
 │   │   ├── NotificationController.php
 │   │   ├── OrderController.php
 │   │   ├── TicketController.php
 │   │   └── TicketTypeController.php
 │   └── Middleware/
 ├── Models/
 ├── routes/
 │   ├── api.php   <-- API routes
 │   └── web.php
 └── ...
📖 Testing API
Use Postman or Thunder Client to test API endpoints.

Example login request:

http

POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
Response:

json

{
  "token": "your_sanctum_token",
  "user": {
    "id": 1,
    "name": "John Doe",
    "role": "attendee"
  }
}


### 2️⃣ Install Dependencies

`composer install`

### 3️⃣ Setup Environment

Copy the `.env.example` file:

`cp .env.example .env`

Update `.env` with your database and email credentials (example for Gmail):

`APP_NAME="Event Management" APP_URL=http://127.0.0.1:8000  DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=event_ticketing_db DB_USERNAME=root DB_PASSWORD=  MAIL_MAILER=smtp MAIL_HOST=smtp.gmail.com MAIL_PORT=587 MAIL_USERNAME=your_gmail@gmail.com MAIL_PASSWORD=your_gmail_app_password MAIL_ENCRYPTION=tls MAIL_FROM_ADDRESS="your_gmail@gmail.com" MAIL_FROM_NAME="${APP_NAME}"`

⚠️ For Gmail: You must enable App Passwords.

### 4️⃣ Run Migrations & Seeders

`php artisan migrate --seed`

### 5️⃣ Start Development Server

`php artisan serve`

---

## 🔑 Authentication

The API uses **Laravel Sanctum** for authentication.

-   Register or Login to get a **Bearer Token**.
-   Then include it in your requests:

`Authorization: Bearer your_token_here`

---

## 📡 API Endpoints

### 🔓 Public Routes

| Method | Endpoint             | Description              |
| ------ | -------------------- | ------------------------ |
| POST   | /register            | Register a new user      |
| POST   | /login               | Login and get token      |
| GET    | /events              | Get all events           |
| GET    | /events/{event}      | Get single event details |
| GET    | /events/{id}/tickets | Get tickets for an event |
| GET    | /qr                  | Generate a QR code       |
| GET    | /pdf                 | Generate sample PDF      |

---

### 🔐 Protected Routes (Require Authentication)

#### 👤 Auth Routes

| Method | Endpoint       | Description            |
| ------ | -------------- | ---------------------- |
| POST   | /logout        | Logout user            |
| GET    | /user          | Get authenticated user |
| PUT    | /user/profile  | Update profile         |
| PUT    | /user/password | Update password        |

#### 🔔 Notification Routes

| Method | Endpoint                     | Description                 |
| ------ | ---------------------------- | --------------------------- |
| GET    | /notifications               | Get all notifications       |
| POST   | /notifications/{id}/read     | Mark a notification as read |
| POST   | /notifications/mark-all-read | Mark all as read            |
| GET    | /notifications/unread-count  | Count unread notifications  |
```

### ✅ Todo / Future Improvements

💳 Integrate real payment gateway (Stripe/PayPal)

📊 Advanced analytics dashboard

🌍 Multi-language support

📱 Mobile app integration

👨‍💻 Author
Developed by Osama Altal

📌 Backend: Laravel 10, Sanctum, DomPDF, QRCode
📌 Database: MySQL markdown readme.md
