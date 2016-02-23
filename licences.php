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
			echo '		<form id="newlicence" onsubmit="return false;">'."\n";
			echo '			<table class="ui-widget ui-widget-content">'."\n";
			echo '				<caption class="ui-widget-content ui-state-highlight">'._('Licences list').'</caption>'."\n";
			echo '				<thead>'."\n";
			echo '					<tr class="ui-widget-header">'."\n";
			echo '						<th></th>'."\n";
			echo '						<th>'._('Name').'</th>'."\n";
			echo '						<th>'._('Version').'</th>'."\n";
			echo '						<th>'._('Licence Number').'</th>'."\n";
			echo '						<th>'._('Quantity').'</th>'."\n";
			echo '						<th>'._('Physical Location').'</th>'."\n";
			echo '					</tr>'."\n";
			echo '				</thead>'."\n";
			echo '				<tfoot>'."\n";
			echo '					<tr>'."\n";
			echo '						<th class="ui-widget-header">*</th>'."\n";
			echo '						<td><input type="text" id="licencename" name="name" /></td>'."\n";
			echo '						<td><input type="text" id="licenceversion"  name="version" /></td>'."\n";
			echo '						<td><input type="text" id="licencenum" name="licence_number" /></td>'."\n";
			echo '						<td><input type="text" id="licenceqty" name="quantity" value="1"/></td>'."\n";
			echo '						<td><input type="text" id="licencelocation"  name="location" /></td>'."\n";
			echo '						<td>'."\n";
			echo '							<button id="addlicence" role="button" aria-disabled="false">'._('Go').'</button>'."\n";
			echo '						</td>'."\n";
			echo '					</tr>'."\n";
			echo '				</tfoot>'."\n";
			echo '				<tbody>'."\n";
			$sql = "SELECT * FROM `licences` ";
			if( isset( $_COOKIE['search'] ) && strlen($_COOKIE['search']) )
			{
				$sql .= "WHERE 1=0 ";
				foreach( array('name','version','licence_number','location') as $field )
					$sql .= " OR LOWER(`".$field."`) LIKE '%".$db->escape(strtolower($_COOKIE['search']))."%' ";
			}
			$sql .= "ORDER BY `licence_id` ASC;";
			if( $licences = $db->get_results($sql) )
			{
				$rowcounter=0;
				foreach( $licences as $licence )
				{
					$rowcounter++;
					echo '					<tr class="ui-widget-content">'."\n";
					echo '						<th class="ui-widget-header">'.$rowcounter.'</th>'."\n";
					echo '						<td><a href="javascript:viewlicence(\''.$licence->licence_id.'\');">'.$licence->name.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewlicence(\''.$licence->licence_id.'\');">'.$licence->version.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewlicence(\''.$licence->licence_id.'\');">'.$licence->licence_number.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewlicence(\''.$licence->licence_id.'\');">'.$licence->quantity.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewlicence(\''.$licence->licence_id.'\');">'.$licence->location.'</a></td>'."\n";
					echo '					</tr>'."\n";
				}
				
			}
			echo '				</tbody>'."\n";
			echo '			</table>'."\n";
			echo '		</form>'."\n";
		?>
	</body>
</html>
