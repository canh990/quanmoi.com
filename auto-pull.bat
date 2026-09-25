@echo off
title Git Auto-Sync Agent (Dong bo Code & CSDL Tu Dong)
echo =======================================================
echo   DANG CHAY CHE DO TU DONG PULL CODE & DU LIEU TEST
echo =======================================================
echo Guard Windows Terminal - Dang theo doi thay doi tu Git...
echo.

:loop
git fetch origin >nul 2>&1
for /f "tokens=*" %%i in ('git rev-parse HEAD') do set LOCAL_HASH=%%i
for /f "tokens=*" %%i in ('git rev-parse @{u}') do set REMOTE_HASH=%%i

if not "%LOCAL_HASH%"=="%REMOTE_HASH%" (
    echo [%time%] Phat hien commit moi! Dang tien hanh force pull va cap nhat CSDL...
    git reset --hard origin/main
    git clean -fd
    php artisan migrate --force
    php artisan db:seed --force
    echo [%time%] DA DONG BO HOAN TOAN CODE VA DU LIEU TEST THANH CONG!
    echo ---------------------------------------------------
)

timeout /t 10 /nobreak >nul
goto loop
