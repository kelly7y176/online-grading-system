#!/bin/bash
# =====================================================
# CS425 Assignment Grading System - Setup Script
# =====================================================
# This script sets up the development environment.
# Run this script after cloning the repository.
# =====================================================

set -e

echo "=========================================="
echo "CS425 Assignment Grading System Setup"
echo "=========================================="

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed."
    echo "Please install Docker first: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Error: Docker Compose is not installed."
    echo "Please install Docker Compose first: https://docs.docker.com/compose/install/"
    exit 1
fi

echo "✓ Docker and Docker Compose are installed"

# Create necessary directories
echo "Creating directories..."
mkdir -p app/uploads
chmod 777 app/uploads

# Copy environment file if not exists
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✓ Created .env file from .env.example"
    fi
fi

# Build and start containers
echo "Building Docker containers..."
docker-compose build

echo "Starting containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 10

# Check if containers are running
if docker-compose ps | grep -q "Up"; then
    echo ""
    echo "=========================================="
    echo "Setup Complete!"
    echo "=========================================="
    echo ""
    echo "Access the application at:"
    echo "  - Web App: http://localhost:8080"
    echo "  - phpMyAdmin: http://localhost:8081"
    echo ""
    echo "Default credentials:"
    echo "  Instructor: instructor@example.com / password123"
    echo "  Student: alice@example.com / password123"
    echo ""
    echo "Useful commands:"
    echo "  docker-compose logs -f     # View logs"
    echo "  docker-compose down        # Stop containers"
    echo "  docker-compose restart     # Restart containers"
    echo ""
else
    echo "Error: Containers failed to start."
    echo "Run 'docker-compose logs' to see error details."
    exit 1
fi
