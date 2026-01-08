#!/bin/bash

# Deployment Script for Akaunting

echo "Starting Akaunting Deployment..."
echo "Date: $(date)"

# Requirements check
command -v docker >/dev/null 2>&1 || { echo "ERROR: Docker not installed"; exit 1; }
command -v docker-compose >/dev/null 2>&1 || { echo "ERROR: Docker Compose not installed"; exit 1; }

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo "WARNING: .env file not found"
fi

# Pull latest image
echo "Pulling latest image..."
docker pull akaunting-app:latest

# Stop old containers
echo "Stopping old containers..."
docker-compose -f docker-compose.prod.yml down

# Start new containers
echo "Starting new containers..."
docker-compose -f docker-compose.prod.yml up -d

# Health check
echo "Performing health check..."
sleep 10

if curl -f http://localhost > /dev/null 2>&1; then
    echo "SUCCESS: Application is running"
    echo "URL: http://localhost"
else
    echo "WARNING: Application might not be accessible"
fi

echo "Deployment complete!"