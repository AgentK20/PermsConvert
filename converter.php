<?php
// Add functions
function array_merge_recursive_distinct () {
  $arrays = func_get_args();
  $base = array_shift($arrays);
  if(!is_array($base)) $base = empty($base) ? array() : array($base);
  foreach($arrays as $append) {
    if(!is_array($append)) $append = array($append);
    foreach($append as $key => $value) {
      if(!array_key_exists($key, $base) and !is_numeric($key)) {
        $base[$key] = $append[$key];
        continue;
      }
      if(is_array($value) or is_array($base[$key])) {
        $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
      } else if(is_numeric($key)) {
        if(!in_array($value, $base)) $base[] = $value;
      } else {
        $base[$key] = $value;
      }
    }
  }
  return $base;
}

// Initialize arrays
$finalgroups = array();
$finalusers = array();
if($_POST['type']=='bperms'){
    if(isset($_POST['bpermgroups']) && isset($_POST['bpermusers']) && $_POST['bpermgroups']!='Paste your groups.yml here' && $_POST['bpermusers']!='Paste your users.yml here'){
        $usersyml = yaml_parse($_POST['bpermusers']);
        $groupsyml = yaml_parse($_POST['bpermgroups']);
    } elseif(isset($_FILES['bpermgroupsfile']) && isset($_FILES['bpermusersfile'])){
        $usersyml = yaml_parse_file($_FILES['bpermusersfile']['tmp_name']);
        $groupsyml = yaml_parse_file($_FILES['bpermgroupsfile']['tmp_name']);
    }
    if ($groupsyml == false){
        echo "Groups parsing failed!";
        exit;
    }
    if ($usersyml == false){
        echo "User parsing failed!";
        exit;
    }
    // Users.yml conversion
    $userkeys = array_keys($usersyml['users']);
    foreach($userkeys as $user){
        $push = array(
            "users" => array(
                $user => array(
                    "group" => $usersyml["users"][$user]["group"][0],
                    "metadata" => array(
                        "build" => true
                    )
                )
            )
        );
        $finalusers = array_merge_recursive_distinct($push, $finalusers);
        foreach($usersyml["users"][$user]["permissions"] as $perm){
            if(substr($perm, 0, 1)=='^'){
                // Negated permission!
                $perm = substr($perm, 1);
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => false
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
            } else {
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
            }
        }
        if(isset($usersyml["users"][$user]["meta"]["prefix"])){
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "prefix" => $usersyml["users"][$user]["meta"]["prefix"]
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        } else {
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "prefix" => "&f"
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
        if(isset($usersyml["users"][$user]["meta"]["suffix"])){
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "suffix" => $usersyml["users"][$user]["meta"]["suffix"]
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        } else {
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "suffix" => "&f"
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
    }
    // Groups.yml conversion
    $groupkeys = array_keys($groupsyml['groups']);
    foreach($groupkeys as $group){
        $push = array(
            "groups" => array(
                $group => array(
                    "universal" => true,
                    "metadata" => array(
                        "build" => true
                    )
                )
            )
        );
        $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        foreach($groupsyml['groups'][$group]['permissions'] as $perm){
            if(substr($perm, 0, 1)=='^'){
                // Negated permission!
                $perm = substr($perm, 1);
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => false
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            } else {
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            }
        }
        if(isset($groupsyml['groups'][$group]['groups']) && is_array($groupsyml['groups'][$group]['groups'])){
            foreach($groupsyml['groups'][$group]['groups'] as $inherit){
                $push = array(
                    "groups" => array(
                        $group => array(
                            "inheritance" => array(
                                $inherit => true
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            }
        }
        if(isset($groupsyml['groups'][$group]['meta']['prefix'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "prefix" => $groupsyml['groups'][$group]['meta']['prefix']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        if(isset($groupsyml['groups'][$group]['meta']['suffix'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "suffix" => $groupsyml['groups'][$group]['meta']['suffix']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        if($groupsyml['default']==$group){
            $push = array(
                "groups" => array(
                    $group => array(
                        "default" => true
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        } else {
            $push = array(
                "groups" => array(
                    $group => array(
                        "default" => false
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
    }
} elseif ($_POST['type']=='pex'){
    if(isset($_POST["pextext"])){
        $permsyml = yaml_parse($_POST['pextext']);
    } elseif(isset($_FILES['pexyml'])){
        $permsyml = yaml_parse_file($_FILES['pexyml']['tmp_name']);
    }
    if($permsyml == false){
        echo "YAML parsing failed!";
        exit;
    }
    $userkeys = array_keys($permsyml["users"]);
    foreach($userkeys as $user){
        if(count($permsyml["users"][$user])!=0){
        $push = array(
            "users" => array(
                $user => array(
                    "group" => $permsyml['users'][$user]['group'][0],
                    "metadata" => array(
                        "build" => true
                    )
                )
            )
        );
        $finalusers = array_merge_recursive_distinct($push, $finalusers);
        if(isset($permsyml['users'][$user]['prefix'])){
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "prefix" => $permsyml['users'][$user]['prefix']
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        } else {
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "prefix" => "&f"
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
        if(isset($permsyml['users'][$user]['suffix'])){
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "suffix" => $permsyml['users'][$user]['suffix']
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        } else {
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "suffix" => "&f"
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
        foreach($permsyml['users'][$user]['permissions'] as $perm){
            if(substr($perm, 0, 1)=='-'){
                // Negated permission!
                $perm = substr($perm, 1);
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => false
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
                } else {
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
            }
        }
    } else {
        $skippedusers = $skippedusers+1;
    }
    }
    $groupkeys = array_keys($permsyml['groups']);
    foreach($groupkeys as $group){
        $push = array(
            "groups" => array(
                $group => array(
                    "universal" => true,
                    "metadata" => array(
                        "build" => true
                    )
                )
            )
        );
        $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        if(isset($permsyml['groups'][$group]['default'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "default" => $permsyml['groups'][$group]['default']
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        foreach($permsyml["groups"][$group]["permissions"] as $perm){
            if(substr($perm, 0, 1)=='-'){
                // Negated permission!
                $perm = substr($perm, 1);
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => false
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
                } else {
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            }
        }
        if(isset($permsyml['groups'][$group]['prefix'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "prefix" => $permsyml['groups'][$group]['prefix']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        if(isset($permsyml['groups'][$group]['suffix'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "suffix" => $permsyml['groups'][$group]['suffix']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        foreach($permsyml['groups'][$group]['inheritance'] as $inherit){
            $push = array(
                "groups" => array(
                    $group => array(
                        "inheritance" => array(
                            $inherit => true
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
    }
} elseif ($_POST['type']=='groupmanager'){
    if(isset($_POST['gmusers']) && isset($_POST['gmgroups'])){
        $usersyml = yaml_parse($_POST['gmusers']);
        $groupsyml = yaml_parse($_POST['gmgroups']);
    } elseif(isset($_FILES['usersyml']) && isset($_FILES['groupsyml'])){
        $usersyml = yaml_parse_file($_FILES['usersyml']['tmp_name']);
        $groupsyml = yaml_parse_file($_FILES['groupsyml']['tmp_name']);
    }
    if($usersyml == false){
        echo "Users.yml parsing failed.";
        exit;
    }
    if($groupsyml == false){
        echo "Groups.yml parsing failed.";
        exit;
    }
    $userkeys = array_keys($usersyml['users']);
    foreach($userkeys as $user){
        $push = array(
            "users" => array(
                $user => array(
                    "group" => $usersyml['users'][$user]["group"],
                    "metadata" => array(
                        "build" => true
                    )
                )
            )
        );
        $finalusers = array_merge_recursive_distinct($push, $finalusers);
        foreach($usersyml['users'][$user]['permissions'] as $perm){
            if(substr($perm, 0, 1)=='-'){
                // Negated permission!
                $perm = substr($perm, 1);
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => false
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
            } elseif(substr($perm, 0, 1)=='+'){
                // Added permission!
                $perm = substr($perm, 1);
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
            } else {
                $push = array(
                    "users" => array(
                        $user => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalusers = array_merge_recursive_distinct($push, $finalusers);
            }
        }
        if(isset($usersyml['users'][$user]['info']['prefix'])){
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "prefix" => $usersyml['users'][$user]['info']['prefix']
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        } else {
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "prefix" => "&f"
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
        if(isset($usersyml['users'][$user]['info']['suffix'])){
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "suffix" => $usersyml['users'][$user]['info']['suffix']
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        } else {
            $push = array(
                "users" => array(
                    $user => array(
                        "metadata" => array(
                            "suffix" => "&f"
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
    }
    $groups = array_keys($groupsyml['groups']);
    foreach($groups as $group){
        $push = array(
            "groups" => array(
                $group => array(
                    "universal" => true
                )
            )
        );
        $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        foreach($groupsyml['groups'][$group]['permissions'] as $perm){
            if(substr($perm, 0, 1)=='-'){
                // Negated permission!
                $perm = substr($perm, 1);
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => false
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            } elseif(substr($perm, 0, 1)=='+'){
                // Added permission!
                $perm = substr($perm, 1);
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            } else {
                $push = array(
                    "groups" => array(
                        $group => array(
                            "permissions" => array(
                                $perm => true
                            )
                        )
                    )
                );
                $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
            }
        }
        if(isset($groupsyml['groups'][$group]['info']['prefix'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "prefix" => $groupsyml['groups'][$group]['info']['prefix']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        if(isset($groupsyml['groups'][$group]['info']['suffix'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "suffix" => $groupsyml['groups'][$group]['info']['suffix']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        if(isset($groupsyml['groups'][$group]['info']['build'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "build" => $groupsyml['groups'][$group]['info']['build']
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        } else {
            $push = array(
                "groups" => array(
                    $group => array(
                        "metadata" => array(
                            "build" => true
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        foreach($groupsyml['groups'][$group]['inheritance'] as $inherit){
            $push = array(
                "groups" => array(
                    $group => array(
                        "inheritance" => array(
                            $inherit => true
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        if(isset($groupsyml['groups'][$group]['default'])){
            $push = array(
                "groups" => array(
                    $group => array(
                        "default" => $groupsyml['groups'][$group]['default']
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        } else {
            $push = array(
                "groups" => array(
                    $group => array(
                        "default" => false
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
    }
} elseif ($_POST['type']=='permbukkit'){
    if(isset($_POST['permbukkit'])){
        $permbukkit = yaml_parse($_POST['permbukkit']);
    } elseif (isset($_FILES['permsyml'])){
        $permbukkit = yaml_parse_file($_FILES['permsyml']['tmp_name']);
    }
    if($permbukkit == false){
        echo "Permissions.yml parsing failed!";
        exit;
    }
    $userkeys = array_keys($permbukkit['users']);
    foreach($userkeys as $user){
        $push = array(
            "users" => array(
                $user => array(
                    "group" => $permbukkit['users'][$user]['groups'][0],
                    "metadata" => array(
                        "build" => true,
                        "prefix" => '&f',
                        "suffix" => '&f'
                    )
                )
            )
        );
        $finalusers = array_merge_recursive_distinct($push, $finalusers);
        $perms = array_keys($permbukkit['users'][$user]['permissions']);
        foreach($perms as $perm){
            $push = array(
                "users" => array(
                    $user => array(
                        "permissions" => array(
                            $perm => $permbukkit['users'][$user]['permissions'][$perm]
                        )
                    )
                )
            );
            $finalusers = array_merge_recursive_distinct($push, $finalusers);
        }
    }
    $groups = array_keys($permbukkit['groups']);
    foreach($groups as $group){
        $push = array(
            "groups" => array(
                $group => array(
                    "universal" => true,
                    "metadata" => array(
                        "build" => true
                    )
                )
            )
        );
        $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        $perms = array_keys($permbukkit['groups'][$group]['permissions']);
        foreach($perms as $perm){
            $push = array(
                "groups" => array(
                    $group => array(
                        "permissions" => array(
                            $perm => $permbukkit['groups'][$group]['permissions'][$perm]
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
        foreach($permbukkit['groups'][$group]['inheritance'] as $inherit){
            $push = array(
                "groups" => array(
                    $group => array(
                        "inheritance" => array(
                            $inherit => true
                        )
                    )
                )
            );
            $finalgroups = array_merge_recursive_distinct($push, $finalgroups);
        }
    }
} else {
    die("You must set a type to convert from.");
}
$groupsyml = yaml_emit($finalgroups, YAML_ANY_ENCODING, YAML_CRLN_BREAK);
$id = rand(10000000000,99999999999);
$gfilename = $id . ".groups.yml";
$gfileloc = "/var/www/araeosia/windperms/output/" . $gfilename;
$gwebloc = "http://araeosia.com/windperms/output/" . $gfilename;
$tommorow = time() + (24 * 60 * 60);
$datedel = date('jS \of F Y h:i A', $tommorow);
// Clean up the groups file
$groupsyml = str_replace('---', "", $groupsyml);
$groupsyml = str_replace('...', "", $groupsyml);
$groupsyml = str_replace("\\xA7", "§", $groupsyml);
$groupsyml = str_replace('~', "''", $groupsyml);
$groupsyml = trim($groupsyml);
$gwarn = "# This file was generated by the WindPerms converter.\n# It will be accessible from " . $gwebloc . " until the " . $datedel . ".\n# Download this file to your server and place it in the ./plugins/Permissions folder and name it 'groups.yml'.\n# Converter by Agent Kid. http://araeosia.com/\n";
$groupsyml = $gwarn . $groupsyml;
// Write the groups file
$FileHandle = fopen($gfileloc, 'w') or die("Can't open file location!");
fwrite($FileHandle,$groupsyml);
fclose($FileHandle);
$usersyml = yaml_emit($finalusers, YAML_ANY_ENCODING, YAML_CRLN_BREAK);
$ufilename = $id . ".users.yml";
$ufileloc = "/var/www/araeosia/windperms/output/" . $ufilename;
$uwebloc = "http://araeosia.com/windperms/output/" . $ufilename;
// Clean up the users file
$usersyml = str_replace('---', "", $usersyml);
$usersyml = str_replace('...', "", $usersyml);
$usersyml = str_replace('\\xA7', "§", $usersyml);
$usersyml = str_replace('~', "''", $usersyml);
$usersyml = trim($usersyml);
$uwarn = "# This file was generated by the WindPerms converter.\n# It will be accessible from " . $uwebloc . " until the " . $datedel . ".\n# Download this file to your server and place it in the ./plugins/Permissions folder and name it 'users.yml'.\n# Converter by Agent Kid. http://araeosia.com/\n";
$usersyml = $uwarn . $usersyml;
// Write the users file
$UFileHandle = fopen($ufileloc, 'w') or die("Can't open file location!");
fwrite($UFileHandle, $usersyml);
fclose($UFileHandle);
$currenttime = time();
#mysql_query("INSERT INTO Converter (id, ip, timestamp, loc) VALUES ('NULL', '$_SERVER[REMOTE_HOST]', '$currenttime', '$fileloc')") or die(mysql_error());
echo '<html><head><title>Conversion Complete!</title></head><body><h2>Groups Converted!</h2><br />' . count($finalgroups['groups']) . ' groups have been converted. The new file is accessible at <a href="http://araeosia.com/windperms/dl.php?id=' . $id . '&t=g"> ' . $gwebloc . '</a><br \>It will be available until the ' . $datedel . '.<br \>Click the link to download it, or wget the file to your server\'s ./plugins/Permissions folder with the name \'groups.yml\'.<br><br>groups.yml:<br><textarea style="width:800px; height:150px" readonly=readonly>' . $groupsyml . '</textarea><br /><h2>Users Converted!</h2><br />' . count($finalusers['users']) . ' users have been converted. The new file is accessible at <a href="http://araeosia.com/windperms/dl.php?id=' . $id . '&t=u"> ' . $uwebloc . '</a><br \>It will be available until the ' . $datedel . '.<br \>Click the link to download it, or wget the file to your server\'s ./plugins/Permissions folder with the name \'users.yml\'.<br /><br>users.yml:<br><textarea style="width:800px; height:150px" readonly=readonly>' . $usersyml . '</textarea><br><br><br><a href="http://araeosia.com/windperms/">Back to main page</a></body></html>';
?>