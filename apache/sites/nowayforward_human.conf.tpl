<VirtualHost *:8000>
  ServerName localhost
  DocumentRoot "${REPOSITORY}/views"
  Alias /archives "${ARCHIVES_DIR}"

  <FilesMatch \.php$>
    SetHandler "proxy:unix:${PHP_FPM_SOCKET}|fcgi://localhost/"
  </FilesMatch>

  RedirectMatch "^/$" /home/index.php
  RedirectMatch "^/index.html$" /home/index.php
  RedirectMatch "^/index.php$" /home/index.php
</VirtualHost>
