<?php
        if( !class_exists( "ezSQLcore" ) )
                require_once "ezsql/shared/ez_sql_core.php";
        if( !class_exists( "ezSQL_mysql" ) )
                require_once "ezsql/mysql/ez_sql_mysql.php";

        $db = new ezSQL_mysql( 'itequip', 'KXDprcYNGJBxeSVS', 'itequipment', 'localhost' );
	$num = 'AssetNumber';
	$out = 'Date loaned';
	$due = 'Date due';
	$sn = 'Serial Number';

	// Note - there's no sense checking - we're presuming that if we want to 
	// do something (like add an asset) we have been passed all the form fields
	// we'll need, called and data formatted, as we need them to be.
	
	// ****************  BE WARNED!!!!! HERE BE DRAGONS!  ******************
	// Don't release this code to public facing servers
	// ****************  BE WARNED!!!!! HERE BE DRAGONS!  ******************
	
	if( $_POST['action'] == "newloan" )
	{
		// Create any new assets we don't already know about
		if( !$assetid = $db->get_var('SELECT `AssetNumber` FROM `equipment` WHERE `AssetNumber` = \''.$db->escape($_POST['assetnum']).'\' LIMIT 1;') )
		{
			$db->query("INSERT INTO `equipment` (`AssetNumber`,`Type`,`Description`) VALUES ('".$db->escape($_POST['assetnum'])."','Unknown','".$db->escape($_POST['descrip'])."');");
			$assetid = $_POST['assetnum'];
		}
		if( $db->query("INSERT INTO `loans` (`AssetNumber`,`borrower`,`from`,`to`) VALUES( '".$db->escape($assetid)."', '".$db->escape($_POST['borrower'])."', '".date('Y-m-d', strtotime($_POST['datefrom']))."', '".date('Y-m-d', strtotime($_POST['datedue']))."');") )
		{
			echo '				<tr class="ui-widget-content'.((strtotime($_POST['datedue'])<time())?' ui-state-error':'').((strtotime($_POST['datefrom'])>time())?' futureloan':'').'">'."\n";
			echo '					<th class="ui-widget-header">#</th>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$assetid.'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['descrip'].'</a></td>'."\n";
			echo '					<td>'.date('Y-m-d', strtotime($_POST['datefrom'])).'</td>'."\n";
			echo '					<td>'.date('Y-m-d', strtotime($_POST['datedue'])).'</td>'."\n";
			echo '					<td><a href="javascript:viewuser(\''.$_POST['borrower'].'\');">'.$_POST['borrower'].'</a></td>'."\n";
			echo '					<td>';
			if( !class_exists( "PHPMailer" ) )
				require_once PT_SHARED_DIRECTORY.DIRECTORY_SEPARATOR."mailer.php";

			$to      = $_POST['borrower'];
			$subject = 'IT equipment loan - '.$_POST['descrip'];
			$message = '
Please note that the IT  equipment that you have borrowed  is due to
be returned on '.date('Y-m-d', strtotime($_POST['datedue'])).'.  Please ensure that this item is returned
promptly so that it can be prepared  for storage or  the next person 
who requires it.

Can you also ensure that all associated items (such as power cables,
chargers, USB adapters, cases, bags, manuals etc.) are also returned
along with it please.

Many thanks,
IT.';
			echo send_email($to, $subject, $message, 'text/plain', 'Helpdesk', 'helpdesk@linguaphonegroup.com');
			echo '</td>'."\n";
			echo '				</tr>'."\n";
			
		}
	} elseif( $_POST['action'] == "newasset" ) {
		if( $db->query("INSERT INTO `equipment` (`AssetNumber`,`Type`,`Description`,`Manufacturer`,`ModelNumber`,`SerialNumber`,`Location`) 
			VALUES( '".$db->escape($_POST['assetnum'])."', '".$db->escape($_POST['type'])."', '".$db->escape($_POST['descrip'])."', '".$db->escape($_POST['manufacturer'])."',
			'".$db->escape($_POST['modelnum'])."','".$db->escape($_POST['serialnum'])."','".$db->escape($_POST['location'])."');") )
		{
			$assetid = $_POST['assetnum'];
			echo '				<tr class="ui-widget-content">'."\n";
			echo '					<th class="ui-widget-header">#</th>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['assetnum'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['descrip'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['type'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['manufacturer'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['modelnum'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['serialnum'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewasset(\''.$assetid.'\');">'.$_POST['location'].'</a></td>'."\n";
			echo '				</tr>'."\n";
		}
	} elseif( $_POST['action'] == "newlicence" ) {
		if( $db->query("INSERT INTO `licences` (`name`,`version`,`licence_number`,`location`,`quantity`) 
			VALUES( '".$db->escape($_POST['name'])."', '".$db->escape($_POST['version'])."', '".$db->escape($_POST['licence_number'])."', '".$db->escape($_POST['location'])."',".$db->escape($_POST['quantity']).");") )
		{
			$licenceid = $db->insert_id;
			echo '				<tr class="ui-widget-content">'."\n";
			echo '					<th class="ui-widget-header">#</th>'."\n";
			echo '					<td><a href="javascript:viewlicence(\''.$licenceid.'\');">'.$_POST['name'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewlicence(\''.$licenceid.'\');">'.$_POST['version'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewlicence(\''.$licenceid.'\');">'.$_POST['licence_number'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewlicence(\''.$licenceid.'\');">'.$_POST['quantity'].'</a></td>'."\n";
			echo '					<td><a href="javascript:viewlicence(\''.$licenceid.'\');">'.$_POST['location'].'</a></td>'."\n";
			echo '				</tr>'."\n";
		}
	} elseif( $_POST['action'] == "updateasset" ) {
		$db->query("UPDATE `equipment`  SET `AssetNumber` = '".$db->escape($_POST['assetnum'])."',
			`Type` = '".$db->escape($_POST['type'])."',
			`Description` = '".$db->escape($_POST['descrip'])."',
			`Manufacturer` = '".$db->escape($_POST['manufacturer'])."',
			`ModelNumber` = '".$db->escape($_POST['modelnum'])."',
			`SerialNumber` = '".$db->escape($_POST['serialnum'])."',
			`Location` = '".$db->escape($_POST['location'])."'
			WHERE `AssetNumber` = '".$db->escape($_POST['origassetnum'])."'
			LIMIT 1;");
	} elseif( $_POST['action'] == "updatelicence" ) {
		$db->query("UPDATE `licences`  SET `name` = '".$db->escape($_POST['name'])."',
			`version` = '".$db->escape($_POST['version'])."',
			`licence_number` = '".$db->escape($_POST['licence_number'])."',
			`location` = '".$db->escape($_POST['location'])."',
			`quantity` = ".$db->escape($_POST['quantity'])."
			WHERE `licence_id` = ".$db->escape($_POST['licence_id'])."
			LIMIT 1;");
	} elseif( $_POST['action'] == "return" ) {
		$db->query('UPDATE `loans` SET `returned` = NOW() WHERE `loan_id` = '.$db->escape($_POST['loanid']).' LIMIT 1;');
	} elseif( $_POST['action'] == "viewasset" ) {
		if( $assetdetails = $db->get_row('SELECT * FROM `equipment` WHERE `AssetNumber` = \''.$db->escape($_POST['asset']).'\' LIMIT 1;') )
		{
			echo '<h1><a href="#">Details</a></h1>'."\n";
			echo '<div>'."\n";
			echo '	<form id="editasset">'."\n";
			echo '		<label>Asset Number</label>'."\n";
			echo '		<input type="text" name="assetnum" id="editassetnum" maxlength="10" value="'.$assetdetails->AssetNumber.'" />'."\n";
			echo '		<label>Type</label>'."\n";
			echo '		<input type="text" name="type" id="edittype" maxlength="255" value="'.$assetdetails->Type.'" />'."\n";
			echo '		<label>Description</label>'."\n";
			echo '		<input type="text" name="descrip" id="editdescription" maxlength="255" value="'.$assetdetails->Description.'" />'."\n";
			echo '		<label>Manufacturer</label>'."\n";
			echo '		<input type="text" name="manufacturer" id="editmanufacturer" maxlength="255" value="'.$assetdetails->Manufacturer.'" />'."\n";
			echo '		<label>Model Number</label>'."\n";
			echo '		<input type="text" name="modelnum" id="editmodelnumber" maxlength="255" value="'.$assetdetails->ModelNumber.'" />'."\n";
			echo '		<label>Serial Number</label>'."\n";
			echo '		<input type="text" name="serialnum" id="editserialnumber" maxlength="255" value="'.$assetdetails->SerialNumber.'" />'."\n";
			echo '		<label>Location</label>'."\n";
			echo '		<input type="text" name="location" id="editlocation" maxlength="255" value="'.$assetdetails->Location.'" />'."\n";
			echo '		<button type="button" id="saveasset">Save Asset Details</button>'."\n";
			echo '		<input type="hidden" name="origassetnum" id="origassetnum" value="'.$assetdetails->AssetNumber.'" />'."\n";
			echo '	</form>'."\n";
			echo '	<script>'."\n";
			echo '		$("#saveasset").button({ icons: { primary: "ui-icon-disk" } }).click(function(){updateasset();});'."\n";
			echo '	</script>'."\n";
			echo '</div>'."\n";
			echo '<h1><a href="#">Loan History</a></h1>'."\n";
			echo '<div>'."\n";
			echo '	<table class="ui-widget ui-widget-content">'."\n";
//			echo '		<caption class="ui-widget-content">'._('Asset list').'</caption>'."\n";
			echo '		<thead>'."\n";
			echo '			<tr class="ui-widget-header">'."\n";
			echo '				<th></th>'."\n";
			echo '				<th>'._('Date loaned').'</th>'."\n";
			echo '				<th>'._('Date due back').'</th>'."\n";
			echo '				<th>'._('Date returned').'</th>'."\n";
			echo '				<th>'._('Issued to').'</th>'."\n";
			echo '			</tr>'."\n";
			echo '		</thead>'."\n";
			echo '		<tbody>'."\n";
			if( $loans = $db->get_results('SELECT * FROM `equipment` LEFT JOIN `loans` ON `equipment`.`AssetNumber` = `loans`.`AssetNumber` WHERE `equipment`.`AssetNumber` = \''.$db->escape($_POST['asset']).'\' ORDER BY `from`,`to` ASC;') )
			{
				$rowcounter=0;
				foreach( $loans as $loan )
				{
					$rowcounter++;
					echo '				<tr class="ui-widget-content'.(((!is_null($loan->returned)&&strtotime($loan->to)<strtotime($loan->returned))||(is_null($loan->returned)&&strtotime($loan->to)<time()))?' ui-state-error':'').'">'."\n";
					echo '					<th class="ui-widget-header">'.$rowcounter.'</th>'."\n";
					echo '					<td>'.$loan->from.'</td>'."\n";
					echo '					<td>'.$loan->to.'</td>'."\n";
					echo '					<td>'.$loan->returned.'</td>'."\n";
					echo '					<td>'.$loan->borrower.'</td>'."\n";
					echo '				</tr>'."\n";
				}
			}
			echo '			</tbody>'."\n";
			echo '		</table>'."\n"; 
			echo '</div>'."\n";
		}
	} elseif( $_POST['action'] == "viewlicence" ) {
		if( $licencedetails = $db->get_row('SELECT * FROM `licences` WHERE `licence_id` = \''.$db->escape($_POST['licence']).'\' LIMIT 1;') )
		{
			echo '<h1><a href="#">Details</a></h1>'."\n";
			echo '<div>'."\n";
			echo '	<form id="editlicence" onsubmit="return false;">'."\n";
			echo '		<label>Name</label>'."\n";
			echo '		<input type="text" name="name" id="editlicencename" maxlength="255" value="'.$licencedetails->name.'" />'."\n";
			echo '		<label>Version</label>'."\n";
			echo '		<input type="text" name="version" id="editlicenceversion" maxlength="255" value="'.$licencedetails->version.'" />'."\n";
			echo '		<label>Licence Number</label>'."\n";
			echo '		<input type="text" name="licence_number" id="editlicence_number" maxlength="255" value="'.$licencedetails->licence_number.'" />'."\n";
			echo '		<label>Location</label>'."\n";
			echo '		<input type="text" name="location" id="editlocation" maxlength="255" value="'.$licencedetails->location.'" />'."\n";
			echo '		<label>Quantity</label>'."\n";
			echo '		<input type="text" name="quantity" id="editquantity" maxlength="10" value="'.$licencedetails->quantity.'" />'."\n";
			echo '		<button type="button" id="savelicence">Save Licence Details</button>'."\n";
			echo '		<input type="hidden" name="licence_id" id="editlicence_id" value="'.$licencedetails->licence_id.'" />'."\n";
			echo '	</form>'."\n";
			echo '	<script>'."\n";
			echo '		$("#savelicence").button({ icons: { primary: "ui-icon-disk" } }).click(function(){updatelicence();});'."\n";
			echo '	</script>'."\n";
			echo '</div>'."\n";
		}
	} elseif( $_POST['action'] == "updatejsvars" ) {

		// We might've made changes to the asset list, or the loan people, so add them to our drop downs.
		echo '				<script type="text/javascript">'."\n";
		echo '					// <![CDATA['."\n";
		if( $borrowers = $db->get_col('SELECT DISTINCT `borrower` FROM `loans` WHERE `borrower` IS NOT NULL AND NOT `borrower` = \'\' ORDER BY LOWER(`borrower`);') )
			echo '						var borrowers = '.json_encode($borrowers).";\n";
		else 	echo '						var borrowers = [];'."\n";

		if( $assetlist = $db->get_results('SELECT DISTINCT `AssetNumber` AS `label`, `Description` AS `desc` FROM `equipment` WHERE `AssetNumber` IS NOT NULL ORDER BY LOWER(`AssetNumber`);') )
			echo '						var assets = '.json_encode($assetlist).";\n";
		else 	echo '						var assets = [];'."\n";

		if( $assetnames = $db->get_results('SELECT DISTINCT `Description` AS `label`, `AssetNumber` AS `desc` FROM `equipment` WHERE `Description` IS NOT NULL AND NOT `Description` = \'\' ORDER BY LOWER(`Description`);') )
			echo '						var assetnames = '.json_encode($assetnames).";\n";
		else 	echo '						var assetnames = [];'."\n";
		if( $manufacturer = $db->get_col('SELECT DISTINCT `Manufacturer` FROM `equipment` WHERE `Manufacturer` IS NOT NULL AND NOT `Manufacturer` = \'\' ORDER BY LOWER(`Manufacturer`);') )
			echo '						var manufacturer = '.json_encode($manufacturer).";\n";
		else 	echo '						var manufacturer = [];'."\n";

		if( $assettype = $db->get_col('SELECT DISTINCT `Type` FROM `equipment` WHERE `Type` IS NOT NULL AND NOT `Type` = \'\' ORDER BY LOWER(`Type`);') )
			echo '						var assettype = '.json_encode($assettype).";\n";
		else 	echo '						var assettype = [];'."\n";

		if( $modelnumber = $db->get_col('SELECT DISTINCT `ModelNumber` FROM `equipment` WHERE `ModelNumber` IS NOT NULL AND NOT `ModelNumber` = \'\' ORDER BY LOWER(`ModelNumber`);') )
			echo '						var modelnumber = '.json_encode($modelnumber).";\n";
		else 	echo '						var modelnumber = [];'."\n";
		if( $assetlocation = $db->get_col('SELECT DISTINCT `Location` FROM `equipment` WHERE `Location` IS NOT NULL AND NOT `Location` = \'\' ORDER BY LOWER(`Location`);') )
			echo '						var assetlocation = '.json_encode($assetlocation).";\n";
		else 	echo '						var assetlocation = [];'."\n";
		if( $assetdescription = $db->get_col('SELECT DISTINCT `Description` FROM `equipment` WHERE `Description` IS NOT NULL AND NOT `Description` = \'\' ORDER BY LOWER(`Description`);') )
			echo '						var assetdescription = '.json_encode($assetdescription).";\n";
		else 	echo '						var assetdescription = [];'."\n";
		echo '					// ]]>'."\n";
		echo '				</script>'."\n";

	}
?>

