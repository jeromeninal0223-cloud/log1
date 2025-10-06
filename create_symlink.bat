@echo off
cd /d "C:\Users\nasif\Herd\log-1"
if exist "public\storage" rmdir /s /q "public\storage"
mklink /D "public\storage" "..\storage\app\public"
echo Storage symlink created successfully
pause
