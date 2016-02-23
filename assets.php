<?php
        if( !class_exists( "ezSQLcore" ) )
                require_once "ezsql/shared/ez_sql_core.php";
        if( !class_exists( "ezSQL_mysql" ) )
                require_once "ezsql/mysql/ez_sql_mysql.php";

        $db = new ezSQL_mysql( 'itequip', 'KXDprcYNGJBxeSVS', 'itequipment', 'localhost' );
	$num = 'AssetNumber';
	$out = 'Date loaned';
	$due = 'Date due';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>IT Loan Equipment</title>
	</head>
	<body>
		<?php
			echo '		<form id="newasset" onsubmit="return false;">'."\n";
			echo '			<table class="ui-widget ui-widget-content">'."\n";
			echo '				<caption class="ui-widget-content ui-state-highlight">'._('Asset list').'</caption>'."\n";
			echo '				<thead>'."\n";
			echo '					<tr class="ui-widget-header">'."\n";
			echo '						<th></th>'."\n";
			echo '						<th>'._('Asset Number').'</th>'."\n";
			echo '						<th>'._('Description').'</th>'."\n";
			echo '						<th>'._('Type').'</th>'."\n";
			echo '						<th>'._('Manufacturer').'</th>'."\n";
			echo '						<th>'._('Model Number').'</th>'."\n";
			echo '						<th>'._('Serial Number').'</th>'."\n";
			echo '						<th>'._('Location').'</th>'."\n";
			echo '					</tr>'."\n";
			echo '				</thead>'."\n";
			echo '				<tfoot>'."\n";
			echo '					<tr>'."\n";
			echo '						<th class="ui-widget-header">*</th>'."\n";
			echo '						<td><input type="text" id="assetsassetnum" name="assetnum" maxlength="10" /></td>'."\n";
			echo '						<td><input type="text" id="assetsnewdescrip"  name="descrip" /></td>'."\n";
			echo '						<td><input type="text" id="assetstype" name="type" /></td>'."\n";
			echo '						<td><input type="text" id="assetsmanufacturer"  name="manufacturer" /></td>'."\n";
			echo '						<td><input type="text" id="assetsmodelnum" name="modelnum" /></td>'."\n";
			echo '						<td><input type="text" id="assetsserialnum" name="serialnum" /></td>'."\n";
			echo '						<td><input type="text" id="assetslocation" name="location" /></td>'."\n";
			echo '						<td>'."\n";
			echo '							<button id="assetsaddasset" role="button" aria-disabled="false">'._('Go').'</button>'."\n";
			echo '						</td>'."\n";
			echo '					</tr>'."\n";
			echo '				</tfoot>'."\n";
			echo '				<tbody>'."\n";
			$sql = "SELECT * FROM `equipment` ";
			if( isset( $_COOKIE['search'] ) && strlen($_COOKIE['search']) )
			{
				$sql .= "WHERE 1=0 ";
				foreach( array('AssetNumber','Type','Description','Manufacturer','ModelNumber','SerialNumber','Location') as $field )
					$sql .= " OR LOWER(`".$field."`) LIKE '%".$db->escape(strtolower($_COOKIE['search']))."%' ";
			}
			$sql .= "ORDER BY `AssetNumber` ASC;";
			if( $assets = $db->get_results($sql) )
			{
				$rowcounter=0;
				foreach( $assets as $asset )
				{
					$rowcounter++;
					echo '					<tr class="ui-widget-content">'."\n";
					echo '						<th class="ui-widget-header">'.$rowcounter.'</th>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->AssetNumber.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->Description.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->Type.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->Manufacturer.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->ModelNumber.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->SerialNumber.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->AssetNumber.'\');">'.$asset->Location.'</a></td>'."\n";
					echo '					</tr>'."\n";
				}
				
			}
			echo '				</tbody>'."\n";
			echo '			</table>'."\n";
			echo '		</form>'."\n";
		?>
	</body>
</html>
