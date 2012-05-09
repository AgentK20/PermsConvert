<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
	<title>WindPerms Converter</title>
        <link rel="stylesheet" type="text/css" href="index.css" />
        <script>
            function setbg(color)
            {
                document.getElementById("zone").style.background=color
            }
        </script>
    </head>
    <body>
        <table width="1024" align="center" id="maintable">
        <tr><td id="headcell" colspan="3" align="center">
            <h1>WindPerms Converter</h1>
            By <a href="http://araeosia.com/">Agent Kid</a>
            <hr width="100%" />
        </td></tr>
        <tr><td id="maincell" valign="top">
    <p>This is a converter to convert your old permissions files to <a href='https://github.com/W1ndwaker/Permissions'>WindPerms</a> for <a href='http://spout.org/'>Spout.</a></p>
    <div id="gwarn"><br><a href='#' onclick="document.getElementById('gwarn').style.display = 'none'">Hide this message</a><br><p><b>WARNING:</b> WindPerms does not yet support multiple groups assigned to one user. The converter will set the first group that is set for the player as it's WindPerm group. It will then disregard all other groups. Example:<br>
            <pre><code>users:<br>    MyUserName:<br>        groups:<br>        - Admin<br>        - Moderator    <br>    AnotherPlayer:<br>        groups:<br>        - Moderator<br>        - Admin</code></pre><br>...will become...<pre><code>users:<br>    MyUserName:<br>        group: Admin<br>    AnotherPlayer:<br>        group: Moderator</code></pre></div>
<h2>bPermissions Converter</h2>
<p>Convert your bPermissions users.yml and groups.yml to WindPerms users.yml and groups.yml.
<form action="converter.php" method="post">
<textarea name="bpermgroups" id="zone0" onfocus="this.value=''; document.getElementById('zone0').style.background='#e5fff3'" onblur="document.getElementById('zone0').style.background='white'">
Paste your groups.yml here</textarea><br />
Or upload it directly: <input type='file' name='bpermgroupsfile'><br />
<textarea name="bpermusers" id='zone1' onfocus="this.value=''; document.getElementById('zone1').style.background='#e5fff3'" onblur="document.getElementById('zone1').style.background='white'">
Paste your users.yml here</textarea><br />
Or upload it directly: <input type='file' name='bpermusersfile'><br />
<input type='hidden' name='type' value='bperms'>
<input type="Submit" value="Submit" />
</form></p>
    
<h2>PermissionsEx Converter</h2>
<p>Convert your PermisisonsEx permissions.yml to WindPerms users.yml and groups.yml.
<form action="converter.php" method="post">
<textarea name="pextext" id='zone2' onfocus="this.value=''; document.getElementById('zone2').style.background='#e5fff3'" onblur="document.getElementById('zone2').style.background='white'">
Paste your permissions.yml here</textarea><br />
Or upload it directly: <input type='file' name='pexyml'><br />
<input type='hidden' name='type' value='pex'>
<input type="Submit" value="Submit" />
</form></p>

<h2>GroupManager Converter</h2>
<p>Convert your GroupManager users.yml and groups.yml to WindPerms users.yml and groups.yml.
<form action="converter.php" method="post">
<textarea name="gmusers" id='zone3' onfocus="this.value=''; document.getElementById('zone3').style.background='#e5fff3'" onblur="document.getElementById('zone3').style.background='white'">
Paste your users.yml here</textarea><br />
Or upload it directly: <input type='file' name='usersyml'><br />
<textarea name="gmgroups" id='zone4' onfocus="this.value=''; document.getElementById('zone4').style.background='#e5fff3'" onblur="document.getElementById('zone4').style.background='white'">
Paste your groups.yml here</textarea><br />
Or upload it directly: <input type='file' name='groupsyml'><br />
<input type='hidden' name='type' value='groupmanager'>
<input type="Submit" value="Submit" />
</form></p>

<h2>PermissionsBukkit Converter</h2>
<p>Convert your PermissionsBukkit config.yml to WindPerms users.yml and groups.yml
<form action="converter.php" method="post">
<textarea name="permbukkit" id='zone5' onfocus="this.value=''; document.getElementById('zone5').style.background='#e5fff3'" onblur="document.getElementById('zone5').style.background='white'">
Paste your config.yml here</textarea><br />
Or upload it directly: <input type='file' name='permsyml'><br />
<input type='hidden' name='type' value='permbukkit'>
<input type="Submit" value="Submit" />
</form></p>
<tr><td id="footcell" colspan="3" align="center">
			<hr width="100%" />
			&copy; AgentKid 2012<br />
                        Frontend design based on <a href='http://wombat.platymuus.com/minecraft/permissions/'>Tad Hardesty's PermissionsBukkit converter</a><br />
                        <a href='http://araeosia.com/windperms/legal.txt'>Legal Stuff</a>
		</td></tr>
	</table>
</body></html>