@echo off

cd "%USERPROFILE%\Documents"

if not exist "%USERPROFILE%\Documents\PetAlliance" (
    git clone https://github.com/ThurStelzner/PetAlliance.git
)

cd C:\xampp\mysql\bin

start /b mysqld

timeout 5 /nobreak >nul 

set banco="%USERPROFILE%\Documents\PetAlliance\docs\banco.sql"

mysql.exe -u root -P 3306 < %banco%

cd "%USERPROFILE%\Documents\PetAlliance\" 

(
    echo DB_SENHA= 
    echo DB_NAME=pet_alliance_db 
    echo DB_USER=root 
    echo DB_HOST=127.0.0.1 

    echo ABACATEPAY_API_KEY= 
    echo ABACATEPAY_URL= 
    echo ABACATEPAY_PRODUCT_ID= 
    echo ABACATEPAY_PRODUCT2_ID= 
    echo ABACATEPAY_PRODUCT3_ID= 
    echo ABACATEPAY_PRODUCT4_ID= 

    echo SMTP_HOST= 
    echo SMTP_PORT= 
    echo SMTP_USER= 
    echo SMTP_PASS= 
    echo SMTP_FROM_EMAIL= 
    echo SMTP_FROM_NAME= 
) > .env

cd "%USERPROFILE%\Documents\PetAlliance\"

echo Iniciando Local Host
start "" /MAX http:localhost:9090
echo Importacao finalizada.
C:\xampp\php\php.exe -S localhost:9090
