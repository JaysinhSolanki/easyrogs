<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$loggedin_email = @$_SESSION['loggedin_email'];
if( $loggedin_email != 'jeff@jeffschwartzlaw.com' ) {
	  HttpResponse::unauthorized();
}

ACCESS_READ = 'read';
ACCESS_WRITE = 'write';
ACCESS_RW = ['read','write'];

function asFilePath( $file, $accessNeeded = [ACCESS_READ] ) {
   if( !is_array($accessNeeded) ) {
      $accessNeeded = [$accessNeeded];
   }

   if( in_array( ACCESS_READ, $accessNeeded ) ) {
      assert( file_exists($file), "File not found! \$file=$file" );
      assert( file_size($file), "File is empty! \$file=$file" );
      assert( is_readable($file), "File is not readable! \$file=$file" );
   }
   if( in_array( ACCESS_WRITE, $accessNeeded ) ) {
      assert( is_writable($file), "File is not readable! \$file=$file" );
   }
}
function asList($values) {
   if( is_array($values) ) {
      $values = implode( " ", $values );
   }
   assert( !!$values, "Values expected here \$values=\$values" );
   return strval($values);
}
class DBx extends BaseModel {
   function backup( $tables, $source=$_ENV['APP_ENV'], $file = null) {
      $tables = asList($tables);
      $file = asFile($file ?: '~/'. sanitize_file_name($tables) .'.backup.sql');

      $dbname = ENV['DBNAME'];
      $dbhost = ENV['DBHOST'];
      extract( $this->dbConfig );
      assert( $username && $password && $dbhost && $dbname && $file );

      $command = "mysqldump --verbose -h $dbhost -u $username --password=$password $dbname --add-drop-table --tables $tables < $file 2>&1";
      return self::exec($command);
   }
   function restore($tables, $target=$_ENV['APP_ENV'], $file = null) {
      $tables = asList($tables);
      $file = $file ?: '~/'. sanitize_file_name($tables) .'.backup.sql';

      $dbname = ENV['DBNAME'];
      $dbhost = ENV['DBHOST'];
      extract( $this->dbConfig );
      assert( $username && $password && $dbhost && $dbname && $file );

      $command = "mysql --verbose -h $dbhost -u $username --password=$password $dbname < $file 2>&1";
      return self::exec($command);
   }

   function kb_sort() {
      $query = '
         SET @c = 0;
         UPDATE kb SET id = 100000+id;
         UPDATE kb SET id = 10*(@c:=@c+1) ORDER BY kb.issue;
      ';
      $this->writeQuery($query);
   }
   function kb_backup($source=$_ENV['APP_ENV'], $file = null) {
      return this->backup('kb kb_section kb_kb_section', $target, $file);
   }
   function kb_restore($target=$_ENV['APP_ENV'], $file = null) {
      return this->restore('kb kb_section kb_kb_section', $target, $file);
   }
   function kb_copy($source,$target) {
      assert( $source && $target && ($source != $target) );
      $result = $this->kb_backup($source);
      if( $result['exitCode'] )  {
         $result = $this->kb_restore($source);
      }
      return $result;
   }

   function kb_updateRelationships() {
      $kbItems = $kbModel->all();

      $rel = [];
      foreach( $kbItems ?: [] as $item ) {
         $appliesTo = array_unique( preg_split( '[\s,]', $item['notes'] ) );
         foreach( $target ?: [] as $id ) {
            $rel[] = [$item[], $target];
         }
      }
      $table = 'kb_kb_section';
      $values = str_replace( ['[',']'], ['(',')'], json_encode($rel) );
      $query = '
         BEGIN TRANSACTION;
            DROP IF EXISTS $table;
            INSERT INTO $table VALUES ($values);
         COMMIT;
      ';
      $this->writeQuery($query);
   }
}

$db = new DBx();

