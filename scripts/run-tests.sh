#!/bin/bash
# =====================================================
# CS425 Assignment Grading System - Test Runner
# =====================================================
# This script runs all tests for the application.
# =====================================================

set -e

echo "=========================================="
echo "Running Tests"
echo "=========================================="

# Check if running in Docker
if [ -f /.dockerenv ]; then
    echo "Running in Docker container..."
    cd /var/www/html
else
    echo "Running locally..."
    cd "$(dirname "$0")/.."
fi

# Run PHP syntax check
echo ""
echo "Checking PHP syntax..."
find app -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"

# Run API tests if curl is available
if command -v curl &> /dev/null; then
    echo ""
    echo "Running API health check..."
    
    # Wait for server to be ready
    sleep 2
    
    # Test health endpoint
    HEALTH_RESPONSE=$(curl -s http://localhost:8080/api/health 2>/dev/null || echo "failed")
    
    if echo "$HEALTH_RESPONSE" | grep -q "ok"; then
        echo "✓ API health check passed"
    else
        echo "✗ API health check failed"
        echo "Response: $HEALTH_RESPONSE"
    fi
fi

echo ""
echo "=========================================="
echo "Test Summary"
echo "=========================================="
echo "PHP syntax check: Complete"
echo "API health check: Complete"
echo ""
echo "For more comprehensive testing, see:"
echo "  - tests/api_tests.md for API test cases"
echo "  - tests/manual_tests.md for manual test procedures"
echo ""
