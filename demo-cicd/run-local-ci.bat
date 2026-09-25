@echo off
chcp 65001 > nul
REM ==============================================================================
REM Script chay Local CI Check cho moi truong Windows (Command Prompt / PowerShell)
REM ==============================================================================

echo ========================================================
echo [LOCAL CI] Dang tu dong kiem tra du an Quan Moi...
echo ========================================================

echo.
echo [1/3] Kiem tra dinh dang Code (Laravel Pint)...
call vendor\bin\pint --test
if errorlevel 1 goto error_pint

echo.
echo [2/3] Dang chay Automated Tests...
call php artisan test
if errorlevel 1 goto error_test

echo.
echo [3/3] Dang bien dich thu Frontend (Vite)...
call npm run build
if errorlevel 1 goto error_build

echo.
echo ========================================================
echo [THANH CONG] Tat ca kiem tra Local CI deu DAT!
echo ========================================================
goto end

:error_pint
echo.
echo --------------------------------------------------------
echo [LOI] Code chua dung dinh dang Laravel Pint!
echo GO Y: Hay chay lenh "vendor\bin\pint" de tu dong format code.
echo --------------------------------------------------------
goto error

:error_test
echo.
echo --------------------------------------------------------
echo [LOI] Automated Tests bi loi!
echo GO Y: Vui long kiem tra va sua lai cac test trong thu muc tests/.
echo --------------------------------------------------------
goto error

:error_build
echo.
echo --------------------------------------------------------
echo [LOI] Bien dich Frontend (Vite) bi loi!
echo --------------------------------------------------------
goto error

:error
echo ========================================================
echo [THAT BAI] Kiem tra Local CI me broken/that bai. Da dung lai!
echo ========================================================
exit /b 1

:end
exit /b 0
