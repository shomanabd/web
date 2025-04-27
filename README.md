
# Task Allocator Pro

**Task Allocator Pro** is a web-based task management system that streamlines task allocation, assignment, and tracking within teams. It implements a role-based access control model, ensuring a clear separation of responsibilities among managers, project leaders, and team members.

## Table of Contents

- [Features](#features)
- [System Roles](#system-roles)
- [Technical Overview](#technical-overview)
- [Installation](#installation)
- [Database Configuration](#database-configuration)
- [Usage Workflow](#usage-workflow)
- [Test Accounts](#test-accounts)


---

## Features

### Manager
- Create and manage projects
- Assign project leaders
- Upload project-related documents
- Search and monitor all tasks
- Track overall project progress

### Project Leader
- Create tasks under assigned projects
- Assign tasks to team members with contribution percentages
- Set task priorities and deadlines
- Monitor task progress
- Search tasks within projects

### Team Member
- Accept or reject assigned tasks
- Update task progress percentage
- View task details and deadlines
- Search for assigned tasks

---

## System Roles

Task Allocator Pro defines three main user roles:

- **Manager**: Oversees projects and assigns project leaders
- **Project Leader**: Manages tasks and allocates them to team members
- **Team Member**: Executes tasks and updates progress

---

## Technical Overview

### Technology Stack
- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL

### Project Structure
- `/includes` — Database configuration and authentication files
- `/css` — Application stylesheets
- PHP files — Core logic and interface components

### Database Tables
- `Users` — Stores user credentials and role information
- `Projects` — Stores project details
- `Tasks` — Stores task descriptions and deadlines
- `TeamAssignments` — Maps tasks to team members
- `ProjectTeamLeaders` — Maps project leaders to projects
- `Documents` — Stores uploaded files
- `TaskProgress` — Tracks progress updates

### Security
- Password hashing
- Input validation and sanitization
- PDO prepared statements to prevent SQL injection
- Session management for authentication
- Role-based feature access

---

## Installation

1. Clone the repository to your web server directory.
2. Import the provided database schema into your MySQL server.
3. Update the database configuration file located at `includes/dbconfig.in.php`.
4. Make sure PHP, MySQL, and a web server (e.g., Apache) are installed and running.
5. Access the application via your browser.

### Clone the Repository
```bash
git clone https://github.com/your-username/task-allocator-pro.git
```

---

## Database Configuration

Edit the `includes/dbconfig.in.php` file to match your server settings:

```php
$host = 'localhost';
$port = 3307;
$dbname = 'web1223166_db';
$username = 'root';
$password = '';
```

---

## Usage Workflow

1. **Manager** creates a project and assigns a **Project Leader**.
2. **Project Leader** breaks down the project into tasks and assigns them to **Team Members**.
3. **Team Members** accept or reject assigned tasks.
4. **Team Members** update their task progress.
5. **Project Leaders** monitor task progress and report back.
6. **Managers** oversee project-wide performance.

---

## Test Accounts

You can log in using the following test credentials:

| Role             | Username | Password     |
|------------------|----------|--------------|
| Manager          | Abd123   | Abd12301230   |
| Project Leader   | Ali333   | Ali12301230   |
| Team Member      | User11   | U112301230    |

---

