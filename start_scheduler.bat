@echo off
title H Tech News Portal - Auto Scheduler
color 0a
echo ========================================================
echo   H Tech Provision IT - Automatic News Scheduler
echo ========================================================
echo.
echo [INFO] News Feed Import Scheduler is running...
echo [INFO] New articles will be collected automatically every 5 minutes.
echo [NOTE] Keep this window minimized in the background.
echo.
cd /d "%~dp0"
php artisan schedule:work
pause
