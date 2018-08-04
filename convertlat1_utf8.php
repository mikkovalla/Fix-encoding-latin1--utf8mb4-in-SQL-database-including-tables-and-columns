<?php
/**
 * Requires php >= 5.5
 * 
 * Use this script to convert utf-8 data in utf-8 mysql tables stored via latin1 connection
 * This is a PHP port from: https://gist.github.com/njvack/6113127
 *
 * @link   : http://www.ridesidecar.com/2013/07/30/of-databases-and-character-encodings/
 *
 * BACKUP YOUR DATABASE BEFORE YOU RUN THIS SCRIPT!
 *
 * Once the script ran over your databases, change your database connection charset to utf8:
 *
 * $dsn = 'mysql:host=localhost;port=3306;charset=utf8';
 * 
 * DON'T RUN THIS SCRIPT MORE THAN ONCE!
 *
 * @author hollodotme
 */
header('Content-Type: text/plain; charset=utf-8');
$dsn      = 'mysql:host=localhost;port=3306;charset=latin1';
$user     = 'root';
$password = '';
$options  = [
	\PDO::ATTR_CURSOR                   => \PDO::CURSOR_FWDONLY,
	\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
	\PDO::MYSQL_ATTR_INIT_COMMAND       => "SET CHARACTER SET latin1",
];
$dbManager = new \PDO( $dsn, $user, $password, $options );
$databasesToConvert = [ 'poo' /** database3, ... */ ];
$typesToConvert     = [ 'char', 'varchar', 'tinytext', 'mediumtext', 'text', 'longtext' ];
foreach ( $databasesToConvert as $database )
{
	echo $database, ":\n";
	echo str_repeat( '=', strlen( $database ) + 1 ), "\n";
	$dbManager->exec( "USE `{$database}`" );
	$tablesStatement = $dbManager->query( "SHOW TABLES" );
	while ( ($table = $tablesStatement->fetchColumn()) )
	{
		echo "Table: {$table}:\n";
		echo str_repeat( '-', strlen( $table ) + 8 ), "\n";
		$columnsToConvert = [ ];
		$columsStatement = $dbManager->query( "DESCRIBE `{$table}`" );
		while ( ($tableInfo = $columsStatement->fetch( \PDO::FETCH_ASSOC )) )
		{
			$column = $tableInfo['Field'];
			echo ' * ' . $column . ': ' . $tableInfo['Type'];
			$type = preg_replace( "#\(\d+\)#", '', $tableInfo['Type'] );
			if ( in_array( $type, $typesToConvert ) )
			{
				echo " => must be converted\n";
				$columnsToConvert[] = $column;
			}
			else
			{
				echo " => not relevant\n";
			}
		}
		if ( !empty($columnsToConvert) )
		{
			$converts = array_map(
				function ( $column )
				{
					return "`{$column}` = CONVERT(CAST(CONVERT(`{$column}` USING latin1) AS binary) USING utf8mb4)";
				},
				$columnsToConvert
			);
			$query = "UPDATE `{$table}` SET " . join( ', ', $converts );
			echo "\n", $query, "\n";
			$dbManager->exec( $query );
		}
		echo "\n--\n";
	}
	echo "\n";
}