{ pkgs ? import <nixpkgs> {} }:
let

  shellDrv =
  {
    mkShell,
    gettext,
    mariadb,
    apacheHttpd,
    ...
  }:
  mkShell {
    nativeBuildInputs = [
      gettext
      mariadb
      apacheHttpd
    ];

    # Thanks https://github.com/JianZcar/LAMP-nix-shell-env/blob/main/default.nix
    shellHook = ''
      #
      # Apache2
      #

      : ''${SERVER_ROOT:=${apacheHttpd}}
      : ''${SERVER_PORT:=8000}
      : ''${ROOT_DIR:=$HOME/.config/apache}
      : ''${REPOSITORY:=${builtins.getEnv "PWD"}}
      export SERVER_ROOT SERVER_PORT ROOT_DIR REPOSITORY

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
      fi

      echo 'Starting httpd...'
      httpd -f $ROOT_DIR/httpd.conf

      #
      # Database
      #

      : ''${MYSQL_HOME:="$HOME/.config/mysql"}
      : ''${MYSQL_DATADIR:="$MYSQL_HOME/data"}
      : ''${MYSQL_PID_FILE:="$MYSQL_HOME/mysql.pid"}
      : ''${MYSQL_UNIX_PORT:="$MYSQL_HOME/mysql.sock"}
      export MYSQL_UNIX_PORT

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
        --socket="$MYSQL_UNIX_PORT"  \
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

      # Start user's shell
      "$(getent passwd $USER | cut -d : -f 7)"

      echo 'Shutting down httpd...'
      httpd -f $ROOT_DIR/httpd.conf -k stop

      # Stop mysql
      echo 'Shutting down mysql...'
      mysqladmin \
        --user="root" \
        --socket="$MYSQL_UNIX_PORT"  \
        shutdown || pkill mysqld

      exit 0
    '';
  };

in
  shellDrv pkgs
