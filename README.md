# PROXIMA — Project Management and Kanban Application

PROXIMA is a project-tracking application developed as part of the SAE 501 module (BUT MMI – Web Development).
It provides a structured and efficient way to manage projects, sprints, epics and tasks through an interactive Kanban board.

## Overview

The application is designed for small teams, students or individuals who need a simple and accessible tool, lighter than Jira or Monday.
It supports user collaboration, task assignment, deadlines, basic reporting and an onboarding workflow that creates a first project automatically when a new user registers.

## Core Features

### Project Management
- Creation and management of multiple projects
- Private visibility by default
- Shareable tokens for inviting other users
- Automatic generation of a starter project at registration

### Sprints and Epics
- Automatic first sprint and epic
- Manual creation for subsequent sprints and epics
- Linking between sprints and epics using a pivot table

### Tasks
- Full CRUD operations
- Assignment to users
- Deadlines and start dates
- Description fields and optional attachments
- Statuses: todo, in_progress, done
- Drag-and-drop task movement across columns in both the Kanban and Project Board views
- Additional rules applied when moving tasks to keep the workflow consistent

### Roadmap

- Chronological display of tasks based on their start dates
- Grouping of tasks by sprints
- Visual overview of the project timeline

### Reports

- Overview of project statistics
- Counts of tasks by status
- Summary of overdue deadlines

### Search and Filtering

- Task search by title
- Filters by status, assignee and deadline
- Combined filters for precise task selection
- On the Projects, Kanban, Roadmap, and Reports pages: filtering restricted to the currently selected project. On the Project Board: additional filtering by sprint

### Notifications
- In-app notifications for task updates
- Optional email notifications

### Collaboration
- Projects may include multiple users
- Task assignment within a project
- Invitation via project share token

### Authentication and Security
- User registration and login
- Password hashing
- CSRF protection
- Access filtered by user roles and ownership rules

## Technology Stack

- Laravel 12.31.1
- Livewire 3.6
- TailwindCSS 3.1.0
- Alpine.js 3.4.2
- MySQL

## Automated Onboarding Workflow

When a new user registers, the system automatically:
1. Creates a first project
2. Generates an initial sprint
3. Creates the first epic
4. Links the sprint and the epic
5. Adds an initial task guiding the user through the interface

This behaviour is implemented in the `Utilisateur` model using model events and factories.

## Installation

### 1. Clone the repository
```
git clone git@github.com:WhoLivesOnMars/proxima.git
cd proxima
```

### 2. Install dependencies
```
composer install
npm install
npm run build
```

### 3. Environment configuration
```
cp .env.example .env
php artisan key:generate
```

Update the database connection fields:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proxima
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### 4. Start the development server
```
php artisan serve
```

### 5. Start the Vite development server
```
npm run dev
```

## Objective of the Project

The goal of PROXIMA is to offer a structured yet simple project-tracking environment.
It demonstrates:

- use of Laravel model events
- interaction with Livewire components
- a modular approach to project management features
- clean separation between models, factories, policies and components

## Live Demo

[Open PROXIMA](https://proxima-production.up.railway.app)

## Author

Daria Khanina  
BUT MMI – SAE 501  
Université de Strasbourg  
2025
