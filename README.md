# Containerized Analytics System

A modern, containerized web application for collecting and analyzing developer data. This system consists of multiple microservices working together to provide a secure, scalable, and user-friendly analytics platform.

## Project Overview

This system is built using a microservices architecture with the following components:

- **Data Entry App** (Port 8080): A PHP-based web application for entering developer data
- **Authentication Service** (Port 8081): Handles user authentication and session management
- **Analytics Service**: Processes and analyzes the collected data
- **Results App** (Port 8082): Displays analytics and insights
- **MySQL Database**: Stores user and developer data
- **MongoDB**: Stores processed analytics data

## Prerequisites

- Docker
- Docker Compose
- Git

## Getting Started

1. Clone the repository:
```bash
git clone <https://github.com/Hamza-Alhalabi-03/Containerized-Analytics-System>
cd Containerized-Analytics-System
```

2. Start the application using Docker Compose:
```bash
docker-compose up -d
```

3. Access the applications:
   - Data Entry App: http://localhost:8080
   - Authentication Service: http://localhost:8081
   - Results App: http://localhost:8082

## System Components

### Data Entry App
- Located in `data-entry-app/`
- Provides a user interface for entering developer information
- Features:
  - Secure form submission
  - Input validation
  - Real-time feedback
  - Modern, responsive design

### Authentication Service
- Located in `auth-service/`
- Handles user authentication and session management
- Features:
  - Secure login system
  - Session management
  - Cookie-based authentication

### Analytics Service
- Processes collected data
- Generates insights and analytics
- Stores processed data in MongoDB

### Results App
- Displays analytics and insights
- Features:
  - Interactive visualizations
  - Data filtering
  - Export capabilities

### Databases
- **MySQL**: Stores structured data (user accounts, developer information)
- **MongoDB**: Stores processed analytics data

## Development

### Project Structure
```
Containerized-Analytics-System/
├── data-entry-app/        # Data entry web application
├── auth-service/          # Authentication service
├── analytics-service/     # Analytics processing service
├── results-app/          # Results visualization app
├── mysql-init/           # MySQL initialization scripts
├── mongodb/              # MongoDB configuration
└── docker-compose.yml    # Docker Compose configuration
```

### Adding New Features
1. Make changes in the appropriate service directory
2. Rebuild the affected container:
```bash
docker-compose build <service-name>
docker-compose up -d <service-name>
```

