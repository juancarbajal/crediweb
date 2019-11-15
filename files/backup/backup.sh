#!/bin/bash
app_dir=$1
db_dir=$2
db_name=$3
backup_dir=$4
backup_name=$5
bk_db=bk_db.tar.bz2
bk_app=bk_app.tar.bz2
#Copia de base de datos
gbak -t -user sysdba -pass masterkey $db_dir/$db_name $backup_dir/db_quipu.fbk
tar -jcvf $backup_dir/$bk_db $backup_dir/db_quipu.fbk
#Copia de Sistema
cd $app_dir
tar -jcvf $backup_dir/$bk_app ./.htaccess ./index.php ./app ./www ./lib/Quipu ./files/backup/backup.sh ./files/infocorp
#Compresion General
cd $backup_dir
tar -zcvf $backup_name $bk_app $bk_db
rm $backup_dir/db_quipu.fbk
rm $bk_app $bk_db
