#!/bin/bash

echo "========================================"
echo "    Video Upload Log Monitor"
echo "========================================"
echo ""
echo "Monitoring Laravel logs for video upload issues..."
echo "Press Ctrl+C to stop monitoring"
echo ""

# Check if log file exists
if [ ! -f "storage/logs/laravel.log" ]; then
    echo "ERROR: Laravel log file not found at storage/logs/laravel.log"
    echo "Make sure you're running this from the Laravel project root directory"
    exit 1
fi

# Monitor the log file for video upload related entries
tail -f storage/logs/laravel.log | grep --line-buffered -E "(Signed URL|Error generating|S3 Configuration|Video|Upload|temporaryUrl|Storage)"
