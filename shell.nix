{ pkgs ? import <nixpkgs> {} }:
let

  shellDrv =
  {
    mkShell,
    gettext,
    mariadb,
    apacheHttpd,
    php,
    ...
  }:
  mkShell {
    nativeBuildInputs = [
      gettext
      mariadb
      apacheHttpd
      php
    ];

    # Thanks https://github.com/JianZcar/LAMP-nix-shell-env/blob/main/default.nix
    shellHook = ''
      : ''${MYSQL_HOME:="$HOME/.config/mysql"}
      : ''${MYSQL_DATADIR:="$MYSQL_HOME/data"}
      : ''${MYSQL_PID_FILE:="$MYSQL_HOME/mysql.pid"}
      : ''${MYSQL_UNIX_SOCKET:="$MYSQL_HOME/mysql.sock"}
      MYSQL_UNIX_PORT="$MYSQL_UNIX_SOCKET"
      export MYSQL_UNIX_SOCKET MYSQL_UNIX_PORT

      : ''${SERVER_ROOT:=${apacheHttpd}}
      : ''${SERVER_PORT:=8000}
      : ''${ROOT_DIR:=$HOME/.config/apache}
      : ''${REPOSITORY:=${builtins.getEnv "PWD"}}
      : ''${PHP_FPM_SOCKET:=$ROOT_DIR/php-fpm.sock}
      export SERVER_ROOT SERVER_PORT ROOT_DIR REPOSITORY PHP_FPM_SOCKET

      #
      # Apache2
      #

      if [ ! -d "$ROOT_DIR" ]
      then
          echo 'Installing apache config...'
          mkdir -p "$ROOT_DIR"
          for file in $(find ./apache -type f)
          do
            file="''${file#./apache/}"

            dir="''${file%/*}"
            [ "$dir" != "$file" ] && mkdir -p "$ROOT_DIR/$dir"

            if [ "$file" != "''${file%.tpl}" ]
            then
              envsubst < "./apache/$file" > "$ROOT_DIR/''${file%.tpl}"
            else
              cp "./apache/$file" "$ROOT_DIR/$file"
            fi
          done

          cat <<EOF >"$ROOT_DIR/php-fpm.conf"
      [global]
      error_log = $ROOT_DIR/php-fpm.error.log
      pid = $ROOT_DIR/php-fpm.pid

      [www]
      listen = $PHP_FPM_SOCKET
      listen.owner = $USER
      listen.group = users
      listen.mode = 0660

      pm = dynamic
      pm.max_children = 5
      pm.start_servers = 2
      pm.min_spare_servers = 1
      pm.max_spare_servers = 3
      EOF
      fi

      echo 'Starting httpd...'
      httpd -f $ROOT_DIR/httpd.conf

      php-fpm -y $ROOT_DIR/php-fpm.conf -F >/dev/null 2>&1 &
      PHP_FPM_PID=$!

      #
      # Database
      #

      __first_time=""
      if [ ! -d "$MYSQL_HOME" ]
      then
        echo 'Installing mysql database...'
        mkdir -p "$MYSQL_HOME" "$MYSQL_DATADIR"
        mysql_install_db                           \
          --auth-root-authentication-method=normal \
          --basedir="${mariadb}"                  \
          --datadir="$MYSQL_DATADIR"               \
          --pid-file="$MYSQL_PID_FILE" \
          --user="$USER" >/tmp/install_log 2>&1 || {
            echo 'Could not install mysql database! Log:';
            cat /tmp/install_log;
            exit 1;
          }

        __first_time="y"
      fi

      # Start mysql
      echo 'Starting mysql...'
      mysqld                         \
        --no-defaults                \
        --basedir="${mariadb}"      \
        --datadir="$MYSQL_DATADIR"   \
        --pid-file="$MYSQL_PID_FILE" \
        --socket="$MYSQL_UNIX_SOCKET"  \
        --bind-address=0.0.0.0       \
        --log-error="$MYSQL_HOME"/mysql.err \
        --user="$USER" >>"$MYSQL_HOME"/mysql.log &
      MYSQL_PID=$!

      until mysqladmin ping >/dev/null 2>&1
      do
        sleep 0.5
        ps -p $MYSQL_PID >/dev/null 2>&1 || {
          echo "mysql daemon stopped initializing!";
          exit 1;
        }
      done

      if [ -n "$__first_time" ]
      then
        mysql \
          --user="root" \
          -e "CREATE USER '$USER'@'localhost'; GRANT ALL PRIVILEGES ON *.* TO '$USER'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES;"
      fi
      unset __first_time

      if ! mysql -e 'use nwfh;' >/dev/null 2>&1
      then
        echo 'nwfh database not found, applying migrations...'
        for migration in ./migrations/*
        do
            mysql < "$migration" >/dev/null
        done
      fi

      # Start user's shell
      "$(getent passwd $USER | cut -d : -f 7)"

      echo 'Shutting down httpd...'
      httpd -f $ROOT_DIR/httpd.conf -k stop
      kill $PHP_FPM_PID

      # Stop mysql
      echo 'Shutting down mysql...'
      mysqladmin \
        --user="root" \
        --socket="$MYSQL_UNIX_SOCKET"  \
        shutdown || pkill mysqld

      exit 0
    '';
  };

in
  shellDrv pkgs
