#!/bin/bash
# POSIX

EXCLUDED_TABLES=(
DATABASECHANGELOG
DATABASECHANGELOGLOCK
DATACHANGELOG
)

IGNORED_TABLES_STRING=''
IGNORED_TEMP_TABLES_STRING=''
for TABLE in "${EXCLUDED_TABLES[@]}"
do :
   IGNORED_TABLES_STRING+=" --ignore-table=${DB_NAME}.${TABLE} "
   IGNORED_TEMP_TABLES_STRING+=" --ignore-table=fosstemp.${TABLE} "
done

configfile="../db-config.php"
DB_NAME=$(grep -oP "(?<=[']DB_NAME['], ['])[^.]*(?=['])" $configfile)
DB_USER=$(grep -oP "(?<=[']DB_USER['], ['])[^.]*(?=['])" $configfile)
DB_PASSWORD=$(grep -oP "(?<=[']DB_PASSWORD['], ['])[^.]*(?=['])" $configfile)

PEDIA_DB_NAME=$(grep -oP "(?<=[']PEDIA_DB_NAME['], ['])[^.]*(?=['])" $configfile)
PEDIA_DB_USER=$(grep -oP "(?<=[']PEDIA_DB_USER['], ['])[^.]*(?=['])" $configfile)
PEDIA_DB_PASSWORD=$(grep -oP "(?<=[']PEDIA_DB_PASSWORD['], ['])[^.]*(?=['])" $configfile)

function seed() {
  DATABASE=$1
  echo " =================================== ${DATABASE} ======================================== "
  if [[ "${DATABASE}" == *"_pedia"* ]] || [[ "${DATABASE}" == *"_pedia_temp"* ]] || [[ "${DATABASE}" == *"_pedia_test"* ]]
  then
    MIGRATION_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/data/fosspedia/*.sql"
    USER=$PEDIA_DB_USER
    PASSWORD=$PEDIA_DB_PASSWORD
  else
    MIGRATION_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/data/*.sql"
    USER=$DB_USER
    PASSWORD=$DB_PASSWORD
  fi

  for file in $MIGRATION_DIR
  do
    FILENAME=`basename "$file" | cut -d'_' -f1`
    AUTHOR=`basename "$file" | cut -d'_' -f2`
    PENDING=$(mysql -D${DATABASE} -u${USER} --password="${PASSWORD}" -se "SELECT ID FROM DATACHANGELOG WHERE FILENAME = $FILENAME")
    if [ -z "$PENDING" ]
      then
      echo "Processing $file file..."
      mysql -u ${USER} --password="${DB_PASSWORD}" ${DATABASE} < $file
      DATE=$(date +"%Y-%m-%d %H:%M:%S")
      mysql -D${DATABASE} -u${USER} --password="${PASSWORD}" -se "INSERT INTO ${DATABASE}.DATACHANGELOG (AUTHOR, FILENAME, DATEEXECUTED) VALUES ('$AUTHOR', '$FILENAME', '$DATE');"
    fi
  done
}

while :; do
  case $1 in
      -s | --setup)
				if [ -n "$3" ]; then
					databaseName=$3
				else
					databaseName="${DB_NAME}"
				fi
				if [ -n "$2" ]; then
					rootpwd=$2
				else
					read -p "Enter your database root password : " rootpwd
				fi
        mysql -u root -p ${rootpwd} -se "CREATE DATABASE IF NOT EXISTS ${databaseName} CHARACTER SET utf8 COLLATE utf8_general_ci ;"
        mysql -u root -p ${rootpwd} -se "GRANT ALL ON ${databaseName}.* TO ${DB_USER}@localhost IDENTIFIED BY '${DB_PASSWORD}';"
        java -jar liquibase.jar --url=jdbc:mysql://localhost/${databaseName} --username="${DB_USER}" --password="${DB_PASSWORD}" --referenceUsername="${DB_USER}" --referencePassword="${DB_PASSWORD}" update
        mysql -u ${USER} --password="${DB_PASSWORD}" -se "CREATE TABLE IF NOT EXISTS ${databaseName}.DATACHANGELOG (ID int(11) NOT NULL AUTO_INCREMENT, AUTHOR varchar(255) NOT NULL, FILENAME varchar(255) NOT NULL, DATEEXECUTED datetime NOT NULL, PRIMARY KEY (ID), UNIQUE KEY TIMESTAMP (FILENAME)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
        seed "${databaseName}"
        mysql -u root -p ${rootpwd} -se "CREATE DATABASE IF NOT EXISTS ${databaseName}_pedia CHARACTER SET utf8 COLLATE utf8_general_ci ;"
        mysql -u root -p ${rootpwd} -se "GRANT ALL ON ${databaseName}_pedia.* TO ${DB_USER}@localhost IDENTIFIED BY '${DB_PASSWORD}';"
        mysql -u ${USER} --password="${DB_PASSWORD}" -se "CREATE TABLE IF NOT EXISTS ${databaseName}_pedia.DATACHANGELOG (ID int(11) NOT NULL AUTO_INCREMENT, AUTHOR varchar(255) NOT NULL, FILENAME varchar(255) NOT NULL, DATEEXECUTED datetime NOT NULL, PRIMARY KEY (ID), UNIQUE KEY TIMESTAMP (FILENAME)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
        seed "${databaseName}_pedia"
        exit
        ;;
      -v | --versioning)
				if [ -n "$2" ]; then
					rootpwd=$2
				else
					read -p "Enter your database root password : " rootpwd
				fi
        mysql -u root -p ${rootpwd} -se "CREATE DATABASE IF NOT EXISTS fosstemp CHARACTER SET utf8 COLLATE utf8_general_ci ;"
        mysql -u root -p ${rootpwd} -se "GRANT ALL ON fosstemp.* TO ${DB_USER}@localhost IDENTIFIED BY '${DB_PASSWORD}';"
        java -jar liquibase.jar --url=jdbc:mysql://localhost/fosstemp --username="${DB_USER}" --password="${DB_PASSWORD}" --referenceUsername="${DB_USER}" --referencePassword="${DB_PASSWORD}" update
        seed "fosstemp"
        mysql -u root -p ${rootpwd} -se "CREATE DATABASE IF NOT EXISTS foss_pedia_temp CHARACTER SET utf8 COLLATE utf8_general_ci ;"
        mysql -u root -p ${rootpwd} -se "GRANT ALL ON foss_pedia_temp.* TO ${DB_USER}@localhost IDENTIFIED BY '${DB_PASSWORD}';"
        mysql -u ${USER} --password="${DB_PASSWORD}" -se "CREATE TABLE IF NOT EXISTS foss_pedia_temp.DATACHANGELOG (ID int(11) NOT NULL AUTO_INCREMENT, AUTHOR varchar(255) NOT NULL, FILENAME varchar(255) NOT NULL, DATEEXECUTED datetime NOT NULL, PRIMARY KEY (ID), UNIQUE KEY TIMESTAMP (FILENAME)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
        seed "foss_pedia_temp"
        exit
        ;;
      -t | --test)
				if [ -n "$2" ]; then
					rootpwd=$2
				else
					read -p "Enter your database root password : " rootpwd
				fi
        mysql -u root -p ${rootpwd} -se "DROP DATABASE IF EXISTS fosstest;"
        mysql -u root -p ${rootpwd} -se "CREATE DATABASE fosstest CHARACTER SET utf8 COLLATE utf8_general_ci ;"
        mysql -u root -p ${rootpwd} -se "GRANT ALL ON fosstest.* TO ${DB_NAME}@localhost IDENTIFIED BY '${DB_PASSWORD}';"
        java -jar liquibase.jar --url=jdbc:mysql://localhost/fosstest --username="${DB_USER}" --password="${DB_PASSWORD}" --referenceUsername="${DB_USER}" --referencePassword="${DB_PASSWORD}" update
        mysql -u ${USER} --password="${DB_PASSWORD}" -se "CREATE TABLE IF NOT EXISTS fosstest.DATACHANGELOG (ID int(11) NOT NULL AUTO_INCREMENT, AUTHOR varchar(255) NOT NULL, FILENAME varchar(255) NOT NULL, DATEEXECUTED datetime NOT NULL, PRIMARY KEY (ID), UNIQUE KEY TIMESTAMP (FILENAME)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
        seed "fosstest"
	mysql -u root -p ${rootpwd} -se "CREATE DATABASE IF NOT EXISTS foss_pedia_test CHARACTER SET utf8 COLLATE utf8_general_ci ;"
	mysql -u root -p ${rootpwd} -se "GRANT ALL ON foss_pedia_test.* TO foss@localhost IDENTIFIED BY '${DB_PASSWORD}';"
	mysql -u ${USER} --password="${DB_PASSWORD}" -se "CREATE TABLE IF NOT EXISTS foss_pedia_test.DATACHANGELOG (ID int(11) NOT NULL AUTO_INCREMENT, AUTHOR varchar(255) NOT NULL, FILENAME varchar(255) NOT NULL, DATEEXECUTED datetime NOT NULL, PRIMARY KEY (ID), UNIQUE KEY TIMESTAMP (FILENAME)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
	seed "foss_pedia_test"
        exit
        exit
        ;;
      -d | --diff)
	if [ -n "$2" ]; then
	  java -jar liquibase.jar --url=jdbc:mysql://localhost/fosstemp --referenceUrl=jdbc:mysql://localhost/${DB_NAME} --username="${DB_USER}" --password="${DB_PASSWORD}" --referenceUsername="${DB_USER}" --referencePassword="${DB_PASSWORD}" diffChangeLog
	  cd DBDiff
	  ./dbdiff server1.foss:server2.fosstemp
	  cd ..
          author=$2
          timestamp=$(date +"%s")
          mv DBDiff/migration.sql "data/${timestamp}_${author}.sql"
          DATE=$(date +"%Y-%m-%d %H:%M:%S")
          mysql -D ${DB_NAME} -u ${USER} --password="${DB_PASSWORD}" -se "INSERT INTO ${DATABASE}.DATACHANGELOG (AUTHOR, FILENAME, DATEEXECUTED) VALUES ('$author', '$timestamp', '$DATE');"
        else
          printf 'ERROR: "--diff" requires a non-empty author name.\n' >&2
          exit 1
        fi
        exit
        ;;
      -u | --update)
        java -jar liquibase.jar --url=jdbc:mysql://localhost/${DB_NAME} --username="${DB_USER}" --password="${DB_PASSWORD}" --referenceUsername="${DB_USER}" --referencePassword="${DB_PASSWORD}" update
        java -jar liquibase.jar --url=jdbc:mysql://localhost/fosstemp --username="${DB_USER}" --password="${DB_PASSWORD}" --referenceUsername="${DB_USER}" --referencePassword="${DB_PASSWORD}" update
        seed "${DB_NAME}"
        seed "${PEDIA_DB_NAME}"
        seed "fosstemp"
        exit
        ;;
      --)              # End of all options.
        shift
        break
        ;;
      -?*)
        printf 'WARN: Unknown option (ignored): %s\n' "$1" >&2
        ;;
      *)               # Default case: If no more options then break out of the loop.
        break
  esac

  shift
done

# Rest of the program here.
# If there are input files (for example) that follow the options, they
# will remain in the "$@" positional parameters.
