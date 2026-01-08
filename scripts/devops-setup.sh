#!/bin/bash

# DevOps Local Setup Script for Akaunting

echo "Starting Akaunting DevOps Setup..."

# Check requirements
echo "Checking requirements..."
docker --version || { echo "ERROR: Docker not installed"; exit 1; }
docker-compose --version || { echo "ERROR: Docker Compose not installed"; exit 1; }

# Build Docker image
echo "Building Docker image..."
docker build -t akaunting-local .

# Start containers
echo "Starting containers..."
docker-compose -f docker-compose.local.yml up -d

# Wait for services
echo "Waiting for services to start..."
sleep 15

# Check status
echo "Service status:"
docker-compose -f docker-compose.local.yml ps

# Display URLs
echo ""
echo "Application URLs:"
echo "  App: http://localhost:8000"
echo "  phpMyAdmin: http://localhost:8080"
echo "  Database: localhost:3306"
echo ""
echo "Database credentials:"
echo "  Host: db"
echo "  Database: akaunting"
echo "  Username: akaunting_user"
echo "  Password: secret"
echo ""
echo "Setup complete!"