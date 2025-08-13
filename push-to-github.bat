@echo off
echo Pushing E-Commerce Platform to GitHub...
echo.

REM Initialize git if not already done
if not exist ".git" (
    echo Initializing Git repository...
    git init
    git branch -M main
)

REM Add remote origin
git remote remove origin 2>nul
git remote add origin https://github.com/yogeshdhamke1/ecommerce-platform-3.git

REM Add all files
echo Adding files to Git...
git add .

REM Commit changes
echo Committing changes...
git commit -m "Add complete PHP e-commerce platform with OTP auth, AI recommendations, and admin enhancements"

REM Push to GitHub
echo Pushing to GitHub...
git push -u origin main --force

echo.
echo Successfully pushed to: https://github.com/yogeshdhamke1/ecommerce-platform-3.git
echo.
pause