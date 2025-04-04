# Geydrop

**Geydrop** is a Keydrop clone made in PHP. It's created for educational purposes and fun, and it does not collect any money from you.

## Instructions

To start the project, follow these steps:

1. Start your Apache and MySQL server.
2. Create an empty database in your MySQL server named `keydrop` (you can change the name, but if you do, you also need to change it in the `.env` file).
3. Import the database from `database/keydrop.sql` into your MySQL server.
4. Move everything from the repository's `htdocs` folder to your server's `htdocs` folder.
5. Set your MySQL host, user, password, and database name in the `.env` file.
6. If you have your own email address you can change in `.env` file email values to connect your gmail account to send emails. If you want to use other emails than gmail then you have to configure SMTP in `htdocs/keydrop/email_sender.php` file.
7. If you want to enable reCaptcha (note: reCaptcha doesn't work on `localhost`), change four lines of code in the project.
8. Go to `localhost/keydrop` (or the configured host) in your browser and enjoy!

## Enabling reCaptcha

To use reCaptcha, you need to create your own reCaptcha keys on [Google reCaptcha Admin](https://www.google.com/recaptcha/admin/create) and paste them into your `.env` file.

> **Note:**  
> reCaptcha doesn't work on `localhost`, so you won't be able to use it there.

## Original Project

My original Geydrop project is available here:  
[https://lopeklol.fanth.pl/projects/keydrop/disclaimer.html](https://lopeklol.fanth.pl/projects/keydrop/disclaimer.html)

> **Caution:**  
> This website is entirely fictional and meant as a joke. It is not the real KeyDrop site and does not serve any profit-making purposes. No deposits or withdrawals can be made here. Everything on this site, including the terms and conditions, is fictional.