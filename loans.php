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
			echo '		<form id="newloan" onsubmit="return false;">'."\n";
			echo '			<table class="ui-widget ui-widget-content">'."\n";
			echo '				<caption class="ui-widget-content ui-state-highlight">'._('Current loans').'</caption>'."\n";
			echo '				<thead>'."\n";
			echo '					<tr class="ui-widget-header">'."\n";
			echo '						<th></th>'."\n";
			echo '						<th>'._('Asset Number').'</th>'."\n";
			echo '						<th>'._('Description').'</th>'."\n";
			echo '						<th>'._('Date loaned').'</th>'."\n";
			echo '						<th>'._('Date due back').'</th>'."\n";
			echo '						<th>'._('Issued to').'</th>'."\n";
			echo '					</tr>'."\n";
			echo '				</thead>'."\n";
			echo '				<tfoot>'."\n";
			echo '					<tr>'."\n";
			echo '						<th class="ui-widget-header">*</th>'."\n";
			echo '						<td><input type="text" id="loansassetnum" name="assetnum" maxlength="10" /></td>'."\n";
			echo '						<td><input type="text" id="loansdescrip"  name="descrip" /></td>'."\n";
			echo '						<td><input type="text" id="loansdatefrom" name="datefrom" class="datefield" /></td>'."\n";
			echo '						<td><input type="text" id="loansdatedue"  name="datedue"  class="datefield" /></td>'."\n";
			echo '						<td><input type="text" id="loansborrower" name="borrower" /></td>'."\n";
			echo '						<td>'."\n";
			echo '							<button id="loansgobutton" role="button" aria-disabled="false">'._('Go').'</button>'."\n";
			echo '						</td>'."\n";
			echo '					</tr>'."\n";
			echo '				</tfoot>'."\n";
			echo '				<tbody>'."\n";
			$sql = "SELECT * FROM `equipment` INNER JOIN `loans` ON `equipment`.`AssetNumber` = `loans`.`AssetNumber` WHERE `loans`.`returned` IS NULL ";
			if( isset( $_COOKIE['search'] ) && strlen($_COOKIE['search']) )
			{
				$sql .= "AND ( 1=0 ";
				foreach( array('loans`.`AssetNumber','borrower','Description','Manufacturer','ModelNumber','SerialNumber','Location','Type') as $field )
					$sql .= "OR LOWER(`".$field."`) LIKE '%".$db->escape(strtolower($_COOKIE['search']))."%' ";
				$sql .= ") ";
			}
			$sql .= "ORDER BY `loans`.`to`,`loans`.`from`,`loans`.`borrower` ASC;";
			if( $assets = $db->get_results($sql) )
			{
				$rowcounter=0;
				foreach( $assets as $asset )
				{
					$rowcounter++;
					echo '					<tr class="ui-widget-content'.((strtotime($asset->to)<time())?' ui-state-error':'').((strtotime($asset->from)>strtotime('+7 days'))?' futureloan':'').'">'."\n";
					echo '						<th class="ui-widget-header">'.$rowcounter.'</th>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->$num.'\');">'.$asset->AssetNumber.'</a></td>'."\n";
					echo '						<td><a href="javascript:viewasset(\''.$asset->$num.'\');">'.$asset->Description.'</a></td>'."\n";
					echo '						<td>'.$asset->from.'</td>'."\n";
					echo '						<td>'.$asset->to.'</td>'."\n";
					echo '						<td><a href="javascript:viewuser(\''.$asset->borrower.'\');">'.$asset->borrower.'</a></td>'."\n";
					echo '						<td><button id="return'.$rowcounter.'" loanid="'.$asset->loan_id.'" class="return ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">'._('Returned').'</button></td>'."\n";
					echo '					</tr>'."\n";
					
					// An automated call to this page every day can deal with sending people emails...
					// The tab calls should define this URI var to stop the emails being sent out on every page view
					if(!isset($_GET['blockemails']) )
					{
						$tomorrowtime = strtotime(date('Y-m-d',strtotime("tomorrow")));
						// Send reminder email
						if(strtotime($asset->to) == $tomorrowtime )
						{
							if( !class_exists( "PHPMailer" ) )
								require_once PT_SHARED_DIRECTORY.DIRECTORY_SEPARATOR."mailer.php";

							$to      = $asset->borrower;
							$subject = 'IT equipment reminder - '.$asset->Description;
							$message = '
Please remember that you are due to return  the IT equipment that you 
borrowed tomorrow morning. It will need to be returned to the IT dept 
before  10:30  so that  it can be  prepared  for storage or  the next 
person who requires it.

Can you also ensure that  all associated items (such as power cables, 
chargers, USB adapters, cases, bags, manuals etc. ) are also returned
along with it please.

Many thanks in advance,
IT.';
							send_email($to, $subject, $message, 'text/plain', 'Helpdesk', 'helpdesk@linguaphonegroup.com');
						} elseif ( strtotime($asset->to) < time() ) {
							if( !class_exists( "PHPMailer" ) )
								require_once PT_SHARED_DIRECTORY.DIRECTORY_SEPARATOR."mailer.php";

							$to      = $asset->borrower;
							$subject = 'IT equipment overdue - '.$asset->Description;
							$message = '
The  IT equipment  that  you  borrowed on  '.$asset->from.'  was due to be
returned on '.$asset->to.'  - but it has not yet been marked as returned
in the system.   Please return this item as soon as possible so that 
it can be prepared for storage or the next person who requires it.

Can you also ensure that all associated items (such as power cables,
chargers, USB adapters, cases, bags, manuals etc.) are also returned
along with it please.

If you have already returned the item,  please ask a member of IT to
update the loans database to reflect this fact.

Many thanks,
IT.';
							send_email($to, $subject, $message, 'text/plain', 'Helpdesk', 'helpdesk@linguaphonegroup.com');
						}
					}
				}
				
			}
			echo '				</tbody>'."\n";
			echo '			</table>'."\n";
			echo '		</form>'."\n";
		?>
	</body>
</html>
