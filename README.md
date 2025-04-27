
# Task Allocator Pro

**Task Allocator Pro** is a comprehensive web-based task management system designed to streamline task allocation, assignment, and progress tracking within teams. It uses a role-based access control model, empowering managers, project leaders, and team members with distinct functionalities.

## Table of Contents
- [System Overview](#system-overview)
- [Features](#features)
- [Technical Implementation](#technical-implementation)
- [Installation and Setup](#installation-and-setup)
- [Database Configuration](#database-configuration)
- [User Registration](#user-registration)
- [Task Management Workflow](#task-management-workflow)
- [Test User Accounts](#test-user-accounts)
- [System Requirements](#system-requirements)
- [Data Structure](#data-structure)
- [Browser Compatibility](#browser-compatibility)

---

## System Overview

Task Allocator Pro implements a three-tier role-based access control model:
- **Manager**: Creates projects and assigns project leaders.
- **Project Leader**: Creates tasks and assigns them to team members.
- **Team Member**: Accepts/rejects tasks and updates their task progress.

---

## Features

### Manager Features
- Create new projects with detailed specifications.
- Assign project leaders.
- Search tasks across all projects.
- Upload supporting documents.
- Monitor overall project progress.

### Project Leader Features
- Create tasks under assigned projects.
- Assign tasks to team members with contribution percentages.
- Set priorities and deadlines for tasks.
- Monitor task progress.
- Search and manage project tasks.

### Team Member Features
- Accept or reject assigned tasks.
- Update task completion percentage.
- View task details and deadlines.
- Search assigned tasks.

---

## Technical Implementation

### File Structure
- **PHP Files**: Core application logic and UI.
- **CSS**: Application styling.
- **Includes**: Authentication and database configuration.

### Database Structure
The application uses MySQL with the following key tables:
- **Users**: User information, roles, and credentials.
- **Projects**: Project specifications, budgets, and deadlines.
- **Tasks**: Task descriptions, dates, effort, and priorities.
- **TeamAssignments**: Mapping between tasks and users.
- **ProjectTeamLeaders**: Mapping between projects and their leaders.
- **TaskProgress**: History of task updates.
- **Documents**: Project-related uploaded files.

#### Database Schema Highlights
- Relational design connecting users, projects, tasks, and progress updates.
- Use of foreign keys to maintain data integrity.

### Security Features
- Password hashing for secure authentication.
- Form validation and sanitization.
- PDO prepared statements to prevent SQL injection.
- Role-based feature access control.
- Session-based authentication.

---

## Installation and Setup

1. Clone the repository to your web server.
2. Import the database schema (`web1223166_db.sql`) into MySQL.
3. Configure database connection in `includes/dbconfig.in.php`.
4. Ensure PHP 8.3+ and MySQL 8.0+ are installed and configured.
5. Access the application via your web browser.

### Clone the Repository
```bash
git clone https://github.com/your-username/task-allocator-pro.git
```

---

## Database Configuration

Edit the `includes/dbconfig.in.php` file:
```php
$host = 'localhost';
$port = 3307;
$dbname = 'web1223166_db';
$username = 'root';
$password = '';
```

---

## User Registration

The system provides a three-step registration process:
1. **Personal Information**: Enter user details, role, qualifications, and skills.
2. **Account Creation**: Choose username and password.
3. **Confirmation**: Review and finalize registration.

---

## Task Management Workflow

1. **Manager** creates a project and assigns a **Project Leader**.
2. **Project Leader** breaks down the project into tasks.
3. **Project Leader** assigns tasks to **Team Members**.
4. **Team Members** accept or reject tasks and update progress.
5. **Project Leaders** monitor task completion.
6. **Managers** oversee overall project performance.

---

## Test User Accounts

| Role            | Username | Password     |
|-----------------|----------|--------------|
| Manager         | Abd123   | Abd12301230   |
| Project Leader  | Ali333   | Ali12301230   |
| Team Member     | User11   | U112301230    |

> Additional test accounts are available in the database.

---

## System Requirements

### Server Requirements
- PHP 8.3+
- MySQL 8.0+
- Apache or Nginx web server

---

## Data Structure

The system handles the following entities:
- **Users**: User profiles with roles.
- **Projects**: Project metadata including budgets and deadlines.
- **Tasks**: Task details, priorities, and deadlines.
- **Assignments**: User-task relationships with contribution percentages.
- **Progress Tracking**: History of task updates.

---


