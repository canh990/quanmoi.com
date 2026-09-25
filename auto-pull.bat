@echo off
title Git Auto-Pull Background Agent
echo ===================================================
echo   DANG CHAY CHE DO TU DONG PULL CODE TU GITHUB/GITLAB
echo ===================================================
echo Ngung chay bang cach dong cua so nay.
echo.

:loop
git fetch origin >nul 2>&1
for /f "tokens=*" %%i in ('git rev-parse HEAD') do set LOCAL_HASH=%%i
for /f "tokens=*" %%i in ('git rev-parse @{u}') do set REMOTE_HASH=%%i

if not "%LOCAL_HASH%"=="%REMOTE_HASH%" (
    echo [%time%] Phat hien code moi tren Server! Dang tien hanh pull...
    git pull
    echo [%time%] Da cap nhat code moi nhat thanh cong!
    echo ---------------------------------------------------
)

:: Cho 10 giay roi kiem tra lai
timeout /t 10 /nobreak >nul
goto loop
