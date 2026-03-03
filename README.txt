NIELIT Salary Slip Generator - Full Project
------------------------------------------

1) Database:
   - Create database named: nielit_salary
   - Import nielit_schema.sql using phpMyAdmin or MySQL CLI.

2) TCPDF:
   - This project uses TCPDF to create PDFs.
   - Download TCPDF from https://tcpdf.org/ or via Composer.
   - Place the TCPDF library folder at: vendor/tcpdf/
   - Ensure vendor/tcpdf/tcpdf.php exists.

   If you don't install TCPDF yet, generating a slip will SAVE the entry in DB and show a message to install TCPDF.

3) Default admin login:
   - Username: admin
   - Password: admin123
   (Change password after first login; production apps should use password_hash())

4) How to run:
   - Place the project folder in your webserver root (e.g., C:/xampp/htdocs/)
   - Start Apache & MySQL
   - Visit: http://localhost/NIELIT_SalarySlip_Full/login.php

5) Files included:
   - db.php, login.php, authenticate.php, dashboard.php, generate_pdf.php
   - edit_slip.php, delete_slip.php, logout.php, nielit_schema.sql
   - assets/nb_logo.jpg
   - vendor/tcpdf/ (empty placeholder — please download TCPDF)

If you want I can also include an employee-importer (Excel to DB) script next.
