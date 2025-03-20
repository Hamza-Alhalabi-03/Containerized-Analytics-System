#!/bin/bash

echo "Setting up the results-app service..."

# Create directory if it doesn't exist
mkdir -p results-app

# Copy files
cp Dockerfile composer.json index.php test.php results-app/

# Navigate to results-app directory
cd results-app

# Create .dockerignore
echo "vendor/" > .dockerignore

# Build and run
cd ..
docker-compose up -d --build results-app

echo "Waiting for service to start..."
sleep 5

echo "You can now access the dashboard at http://localhost:8080"
echo "To verify MongoDB is working properly, visit http://localhost:8080/test.php"
echo "To see PHP configuration, visit http://localhost:8080/phpinfo.php"