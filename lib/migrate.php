<?

function ensure_self_migrated()
{
  if (!db_table_exists("migrations"))
  {
    migrate_from("migrate", dirname(__FILE__)."/../migrations", 0);
  }
  ensure_migration("migrate");
}



function ensure_migration($module_name)
{
  global $manifests;
  $manifest = $manifests[$module_name];
  $path = $manifest['path']."/migrations";
  if (!file_exists($path)) return;

  $res = query_assoc("select * from migrations where name='$module_name'");
  if (count($res)==0)
  {
    query("insert into migrations (name,last_migration) values ('$module_name', 0)");
    $res = query_assoc("select * from migrations where name='$module_name'");
  }  
  migrate_from($module_name, $path, $res[0]['last_migration']);
}

function migrate_from($module_name, $path, $version)
{
  $is_migrated=false;
  foreach( glob($path."/*.sql") as $migration_path)
  {
    $matches = array();
    preg_match("/(\d+).*/", basename($migration_path), $matches);
    $migration_number = (int)$matches[1];
    if ($migration_number>$version)
    {
      $is_migrated=true;
      query_file($migration_path);
      $res = query_assoc("select * from migrations where name='$module_name'");
      if (count($res)==0)
      {
        query("insert into migrations (name,last_migration) values ('$module_name', $migration_number)");
      } else 
      {
        query("update migrations set last_migration=$migration_number where name='$module_name'");      
      }      
    }
  }
}

function ensure_migrations()
{
  global $manifests;
  foreach($manifests as $module_name=>$manifest)
  {
    if ($manifest['enabled']) ensure_migration($module_name);
  }
}