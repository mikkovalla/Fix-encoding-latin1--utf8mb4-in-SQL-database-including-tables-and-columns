# Fix latin1 -> UTF-8mb4
2 php scripts to change sql database tables and columns encodings from latin1 to utf8mb4. First script is from <code>https://gist.github.com/hollodotme/fe24b961680e08473072</code>, second is my solution. Together these two php scripts made the transition of hundreds of Latin1 encoded SQL tables to modern UTF-8mb4 encoding. Certain columns did not pass the modification due to larger than accepted byte size so these had to be altered manually.

## First

To modify the database encoding from latin1 to utf8mb4. Run this SQL command in datbase Cli on the correct table.

<code>ALTER DATABASE database_name CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;</code>

## Second

The following is to modify the encoding of your database tables from latin1 to UTF8mb4. THIS WILL NOT modify columns data.

1. Open <code>convertlat1_utf8.php</code> in editor
2. change <code>$dsn = 'mysql:host=localhost;port=3306;charset=latin1'; $user = 'root'; $password = '';</code> to match your server details
3. If needed, change the encoding on line 64 <code>return "`{$column}` = CONVERT(CAST(CONVERT(`{$column}` USING latin1) AS binary) USING utf8mb4)";</code>
4. In Cli run command <code>php convertlat1_utf8.php</code> 

## Third

The following is to modify the encoding of your columns and data stored in them from latin1 to UTF8mb4.

1. Open <code>converttableslat1_utf8uni.php</code> in editor
2. change <code>$host = "localhost"; $db_name = 'tööt'; $db_username = "root"; $db_password = ""; $convert_to = "utf8mb4_unicode_ci";</code> to match your server details
3. If needed, change the encoding in <code>$convert_to = "utf8mb4_unicode_ci";</code>
4. In Cli run command <code>php converttableslat1_utf8uni.php</code> 

### Possible problem

Changing to UTF-8mb4 encoding uses more bytes, therefor your columns cannot store as much data as eg. with Latin1 encoding, and it is possible that as I had to do, you will need to run the following SQL command on certain tables explicitly -> 
<code>ALTER TABLE tableName CHANGE columnName columnName VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code> 
However, the column type depends on your column requirements, and the above example is only to change column of type VARCHAR(255) to type VARCHAR(191) 