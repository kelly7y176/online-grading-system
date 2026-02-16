#!/bin/bash
# =====================================================
# CS425 Assignment Grading System - XAMPP Setup Script
# =====================================================
# This script sets up the application for XAMPP users.
# Run this if you prefer XAMPP over Docker.
# =====================================================

set -e

echo "=========================================="
echo "CS425 Assignment Grading System"
echo "XAMPP Setup"
echo "=========================================="

# Detect XAMPP installation
XAMPP_PATH=""
if [ -d "/opt/lampp" ]; then
    XAMPP_PATH="/opt/lampp"
elif [ -d "/Applications/XAMPP" ]; then
    XAMPP_PATH="/Applications/XAMPP/xamppfiles"
elif [ -d "C:/xampp" ]; then
    XAMPP_PATH="C:/xampp"
fi

if [ -z "$XAMPP_PATH" ]; then
    echo "XAMPP installation not found in default locations."
    echo "Please enter your XAMPP htdocs path:"
    read HTDOCS_PATH
else
    HTDOCS_PATH="$XAMPP_PATH/htdocs"
fi

# Get the project directory name
PROJECT_NAME="grading-system"
TARGET_PATH="$HTDOCS_PATH/$PROJECT_NAME"

echo "Installing to: $TARGET_PATH"

# Create target directory
mkdir -p "$TARGET_PATH"

# Copy application files
echo "Copying application files..."
cp -r app/* "$TARGET_PATH/"

# Set permissions
echo "Setting permissions..."
chmod -R 755 "$TARGET_PATH"
mkdir -p "$TARGET_PATH/uploads"
chmod 777 "$TARGET_PATH/uploads"

echo ""
echo "=========================================="
echo "Manual Steps Required"
echo "=========================================="
echo ""
echo "1. Start XAMPP (Apache and MySQL)"
echo ""
echo "2. Open phpMyAdmin: http://localhost/phpmyadmin"
echo ""
echo "3. Create database:"
echo "   - Click 'New' in the left sidebar"
echo "   - Enter database name: grading_system"
echo "   - Click 'Create'"
echo ""
echo "4. Import database schema:"
echo "   - Select 'grading_system' database"
echo "   - Click 'Import' tab"
echo "   - Choose file: database/schema.sql"
echo "   - Click 'Go'"
echo ""
echo "5. Import seed data (optional):"
echo "   - Click 'Import' tab"
echo "   - Choose file: database/seed.sql"
echo "   - Click 'Go'"
echo ""
echo "6. Access the application:"
echo "   http://localhost/$PROJECT_NAME"
echo ""
echo "Default credentials:"
echo "  Instructor: instructor@example.com / password123"
echo "  Student: alice@example.com / password123"
echo ""
