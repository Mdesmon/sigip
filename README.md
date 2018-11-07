# sigip

Interface de saisie pour la generation d'un annuaire 


# Installation

Ouvrir l'invite de commande dans le dossier de votre choix

Cloner le repo sigip (Ne pas oublier le point)

```console
git clone https://github.com/Mdesmon/sigip.git .
```

Installer les packets npm

```console
npm install
```

Installer les packets composer (Installer composer si besoin)

```console
composer install 
```

Configurer la section DATABASE du fichier includes/config.php :

```php
/* DATABASE */
define('DB_DSN', 'mysql:host=localhost;dbname=si_gip;charset=utf8');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'password');
```

La section MAIL du fichier de configuration n'a pas besoin d'être configuré si MAIL_ENABLED n'est pas TRUE

```php
/* MAIL */
define('MAIL_ENABLED', FALSE);
define('MAIL_HOST', 'X');
define('MAIL_PORT', 465);
define('MAIL_CHARSET', "UTF-8");
define('MAIL_USERNAME', 'X');
define('MAIL_PASSWORD', 'X');
define('MAIL_FROM', 'X');
define('MAIL_REPLYTO', 'X');
define('MAIL_NAME', 'X');
```

Créer une base de donnée vide nommé si_gip

Ouvrir le navigateur et aller sur l'url suivante : localhost/Monsite/install/installer.php

Créer le compte super-admin

L'installation du site est terminé. Vous pouvez vous loger depuis la page index.php