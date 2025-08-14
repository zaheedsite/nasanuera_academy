@echo off
echo ========================================
echo    Video Upload Log Monitor
echo ========================================
echo.
echo Monitoring Laravel logs for video upload issues...
echo Press Ctrl+C to stop monitoring
echo.

REM Check if log file exists
if not exist "storage\logs\laravel.log" (
    echo ERROR: Laravel log file not found at storage\logs\laravel.log
    echo Make sure you're running this from the Laravel project root directory
    pause
    exit /b 1
)

REM Monitor the log file for video upload related entries
powershell -Command "Get-Content 'storage\logs\laravel.log' -Wait -Tail 10 | Where-Object { $_ -match 'Signed URL|Error generating|S3 Configuration|Video|Upload' }"
