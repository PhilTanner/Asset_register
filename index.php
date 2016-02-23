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
		<link rel="stylesheet" type="text/css" media="all" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/base/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" media="all" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/ui-cupertino/jquery-ui.css" />
		<!-- <link rel='stylesheet' type='text/css' media='all' href='http://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' /> -->
		
		<style>
			body { font-size: 62.5%; background-color:White; }
			#popup label, #popup input { display:block; }
			#popup input { margin-bottom:4px; width:95%; }
			#popup input:after { content:":"; }
			#ui-datepicker-div { display:none; }
			form tbody { height:500px;overflow-y:scroll;margin-right:20px; }
			form table { width:100%; }
			#popup tbody { height:auto; }
			#searchtext{ float:right; }
			#searchbutton{ float:right; }
		</style>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
			// <![CDATA[
				google.load('jquery',   '1.4');
				google.load('jqueryui', '1.8');
			// ]]>
		</script>
		<script type="text/javascript">
			// <![CDATA[
				
				var borrowers = assets = assetnames = manufacturer = assettype = modelnumber = assetlocation = assetdescription = [];

				$( function() 
				{

					$('#popup').dialog({ 
						autoOpen:false,
						modal:true,
						zIndex:3000,
						title:'Item Details',
						width:800 
					}).bind( 
						"dialogclose", 
						function(event, ui)
						{
							// Need to kill the last one, so we can create the next one...
							$('#popup').accordion('destroy');
							$('#popup').html('');
						}
					);

					// Get and setup our autocomplete fields.
					$.post('ajaxsave.php', 
						'action=updatejsvars',
						function(data)
						{
							$('body').append( data );
						}
					);
					$( "#tabs" ).tabs({
						ajaxOptions: {
							error: 		function( xhr, status, index, anchor ) 
									{
										$( anchor.hash ).html("Ooops! Couldn't load this tab." );
									},
							success:	function( data, textStatus, jqXHR ) 
									{
										if( $( "#tabs" ).tabs('option', 'selected') == 0 )
											loanpageready();
										else if( $( "#tabs" ).tabs('option', 'selected') == 1 )
											assetspageready();
										else if( $( "#tabs" ).tabs('option', 'selected') == 2 )
											licencespageready();
										else alert( $( "#tabs" ).tabs('option', 'selected') );
									}
						}
					});

					// Filter displayed rows by search text
					$( "#searchtext" ).addClass(
						"ui-widget ui-state-default ui-corner-all ui-icon-search"
					).bind(
						'keyup',
						function() 
						{
							// Need to save it as a cookie, so PHP can read it?
							document.cookie='search='+$(this).val();
							// Reload current selected tab, so filter query can work
							$("#tabs").tabs('load',$( "#tabs" ).tabs('option', 'selected')); 
						}
					).bind(
						'focus',
						function()
						{ 
							$(this).removeClass('ui-state-default'); 
						}
					).bind(
						'blur',
						function()
						{
							$(this).addClass('ui-state-default');
						}
					);
					$('#debug').button({
						icons: { secondary: "ui-icon-alert" }
					}).click(
						function()
						{
							console.log('borrowers:');
							console.debug(borrowers);
							console.log('assets:');
							console.debug(assets);
							console.log('assetnames:');
							console.debug(assetnames);
							console.log('manufacturer:');
							console.debug(manufacturer);
							console.log('assettype:');
							console.debug(assettype);
							console.log('modelnumber:');
							console.debug(modelnumber);
							console.log('assetlocation:');
							console.debug(assetlocation);
							console.log('assetdescription:');
							console.debug(assetdescription);
						}
					);
				});

				function loanpageready()
				{
					$( ".datefield" ).datepicker({
						showOn: "button",
						buttonImage: "calendar.gif",
						buttonImageOnly: true,
						dateFormat: 'yy-mm-dd'
					});
					$( "#loansassetnum" ).autocomplete({
						minLength: 0,
						source: assets,
						focus: function( event, ui ) {
							$( "#loansassetnum" ).val( ui.item.label );
							return false;
						},
						select: function( event, ui ) {
							$( "#loansassetnum" ).val( ui.item.label );
							$( "#loansdescrip" ).val( ui.item.desc );
							return false;
						}
					}).data( "autocomplete" )._renderItem = function( ul, item ) {
						return $( "<li></li>" )
							.data( "item.autocomplete", item )
							.append( "<a>" + item.label + "<br>&nbsp;&nbsp;&nbsp;" + item.desc + "</a>" )
							.appendTo( ul );
					};
					$( "#loansdescrip" ).autocomplete({
						minLength: 0,
						source: assetnames,
						focus: function( event, ui ) {
							$( "#loansdescrip" ).val( ui.item.label );
							return false;
						},
						select: function( event, ui ) {
							$( "#loansassetnum" ).val( ui.item.desc );
							$( "#loansdescrip" ).val( ui.item.label );
							return false;
						}
					}).data( "autocomplete" )._renderItem = function( ul, item ) {
						return $( "<li></li>" )
							.data( "item.autocomplete", item )
							.append( "<a>" + item.label + "<br>&nbsp;&nbsp;&nbsp;" + item.desc + "</a>" )
							.appendTo( ul );
					};
					$( "#loansborrower" ).autocomplete({ source: borrowers });
					
					$( "#loansgobutton" ).button().click(
						function() 
						{
							var error = false;
							$("#loansgobutton").button({disabled:true, icons: { secondary: "ui-icon-clock" }});
							$('input').removeClass('ui-state-error');
							if( !$.trim($('#loansassetnum').val()).length )
							{
								error = true;
								$('#assetnum').addClass('ui-state-error');
								alert('<?= _('You need to enter the asset number.');?>');
							}
							if( !$.trim($('#loansdescrip').val()).length )
							{
								error = true;
								$('#descrip').addClass('ui-state-error');
								alert('<?= _('You need to enter a description for the asset.');?>');
							}
							if( !$.trim($('#loansdatefrom').val()).length )
							{
								error = true;
								$('#loansdatefrom').addClass('ui-state-error');
								alert('<?= _('You need to enter a start date for the loan.');?>');
							}
							if( !$.trim($('#loansdatedue').val()).length )
							{
								error = true;
								$('#loansdatedue').addClass('ui-state-error');
								alert('<?= _('You need to enter a return date for the loan.');?>');
							}
							if( !$.trim($('#loansborrower').val()).length )
							{
								error = true;
								$('#loansborrower').addClass('ui-state-error');
								
								alert('<?= _('You need to enter the email address of who is borrowing the asset.');?>');
							}

							if(error) return;
							
							// validations passed.
							$.post('ajaxsave.php', 
								$('#newloan').serialize()+'&action=newloan',
								function(data)
								{
									$('tbody').append( data );
									$('input').val('');
									$("#loansgobutton").button({disabled:false, icons: { }});
								}
							);

							// Update any dropdowns with new values from our new entry.
							$.post('ajaxsave.php', 
								'action=updatejsvars',
								function(data)
								{
									$('body').append( data );
								}
							);
						}
					);

					$( ".return" ).button().click(
						function() 
						{
							var button = $(this);
							if( confirm('Are you sure you want to mark this item as returned?') )
								$.post('ajaxsave.php', 
									'action=return&loanid='+button.attr('loanid'),
									function(data)
									{
										button.parent().parent().fadeOut('slow');
									}
								);
						}
					);
					
					$('tr.futureloan td,tr.futureloan th,tr.futureloan').fadeTo(0,0.5);
					$('tr.futureloan button').button({disabled: true});

				}
				
				function assetspageready()
				{
					$( "#assetsnewdescrip" ).autocomplete({ source: assetnames});
					$( "#assetstype" ).autocomplete({ source: assettype });
					$( "#assetsmanufacturer" ).autocomplete({ source: manufacturer });
					$( "#assetsmodelnum" ).autocomplete({ source: modelnumber });
					$( "#assetslocation" ).autocomplete({ source: assetlocation });

					$( "#assetsaddasset" ).button().click(
						function() 
						{
							var error = false;
							$("#assetsaddasset").button({disabled:true, icons: { secondary: "ui-icon-clock" }});
							
							$('input').removeClass('ui-state-error');

							if( !$.trim($('#assetsassetnum').val()).length )
							{
								error = true;
								$('#assetsassetnum').addClass('ui-state-error');
								alert('<?= _('You need to enter the asset number.');?>');
							}
							if( !$.trim($('#assetstype').val()).length )
							{
								error = true;
								$('#assetstype').addClass('ui-state-error');
								alert('<?= _('You need to enter the type of the asset.');?>');
							}

							if(error) return;
							
							// validations passed.
							$.post('ajaxsave.php', 
								$('#newasset').serialize()+'&action=newasset',
								function(data)
								{
									$('tbody').append( data );
									$('input').val('');
									$("#assetsaddasset").button({disabled:false, icons: { }});
								}
							);

							// Update any dropdowns with new values from our new entry.
							$.post('ajaxsave.php', 
								'action=updatejsvars',
								function(data)
								{
									$('body').append( data );
								}
							);
						}
					);

				}

				function licencespageready()
				{
					$( "#licencelocation" ).autocomplete({ source: assetlocation });
					$( "#addlicence" ).button().click(
						function() 
						{
							var error = false;
							$('input').removeClass('ui-state-error');

							if( !$.trim($('#licencename').val()).length )
							{
								error = true;
								$('#licencename').addClass('ui-state-error');
								alert('<?= _('You need to enter the name of the licence.');?>');
							}
							if( !$.trim($('#licenceversion').val()).length )
							{
								error = true;
								$('#licenceversion').addClass('ui-state-error');
								alert('<?= _('You need to enter the version of the licence.');?>');
							}
							if( !$.trim($('#licencenum').val()).length )
							{
								error = true;
								$('#licencenum').addClass('ui-state-error');
								alert('<?= _('You need to enter the serial number of the licence.');?>');
							}
							if( !$.trim($('#licenceqty').val()).length )
							{
								error = true;
								$('#licenceqty').addClass('ui-state-error');
								alert('<?= _('You need to enter the number of licences.');?>');
							}

							if(error) return;
							
							// validations passed.
							$.post('ajaxsave.php', 
								$('#newlicence').serialize()+'&action=newlicence',
								function(data)
								{
									$('tbody').append( data );
									$('input').val('');
									$('#licenceqty').val('1');
								}
							);

							// Update any dropdowns with new values from our new entry.
							$.post('ajaxsave.php', 
								'action=updatejsvars',
								function(data)
								{
									$('body').append( data );
								}
							);
						}
					);
				}

				function viewasset(assetnum)
				{
					$('#popup').dialog('open').load(
						'ajaxsave.php', 
						{ action:'viewasset', asset:assetnum }, 
						function(data)
						{ 
							$('#popup').accordion({ autoHeight: false, header: 'h1' });
							$( "#editdescription" ).autocomplete({ source: assetnames});
							$( "#edittype" ).autocomplete({ source: assettype });
							$( "#editmanufacturer" ).autocomplete({ source: manufacturer });
							$( "#editmodelnumber" ).autocomplete({ source: modelnumber });
							$('#editlocation').autocomplete({ source: assetlocation });
						}
					);	
				}	
				
				function viewlicence(licenceid)
				{
					$('#popup').dialog('open').load(
						'ajaxsave.php', 
						{ action:'viewlicence', licence:licenceid }, 
						function(data)
						{ 
							$('#popup').accordion({ autoHeight: false, header: 'h1' });
							$( "#editdescription" ).autocomplete({ source: assetnames});
							$( "#edittype" ).autocomplete({ source: assettype });
							$( "#editmanufacturer" ).autocomplete({ source: manufacturer });
							$( "#editmodelnumber" ).autocomplete({ source: modelnumber });
							$('#editlocation').autocomplete({ source: assetlocation });
						}
					);	
				}	
				
				function updateasset()
				{
					var error = false;
					$('input').removeClass('ui-state-error');
					
					$("#saveasset").button({disabled:true, icons: { primary: "ui-icon-disk",secondary: "ui-icon-clock" }});

					if( !$.trim($('#editassetnum').val()).length )
					{
						error = true;
						$('#editassetnum').addClass('ui-state-error');
						alert('<?= _('You need to enter the asset number.');?>');
					}
					if( !$.trim($('#edittype').val()).length )
					{
						error = true;
						$('#edittype').addClass('ui-state-error');
						alert('<?= _('You need to enter the type of the asset.');?>');
					}
	
					if(error) return;
					
					// validations passed.
					$.post('ajaxsave.php', 
						$('#editasset').serialize()+'&action=updateasset',
						function(data){
							$("#saveasset").button({ disabled: false, icons: { primary: "ui-icon-disk" } });
						}
					);
					// Update any dropdowns with new values from our new entry.
					$.post('ajaxsave.php', 
						'action=updatejsvars',
						function(data)
						{
							$('body').append( data );
						}
					);
				}
				
				function updatelicence()
				{
					var error = false;
					$('input').removeClass('ui-state-error');
					
					$("#savelicence").button({disabled:true, icons: { primary: "ui-icon-disk",secondary: "ui-icon-clock" }});

					if( !$.trim($('#editlicencename').val()).length )
					{
						error = true;
						$('#editlicencename').addClass('ui-state-error');
						alert('<?= _('You need to enter the licence name.');?>');
					}
					if( !$.trim($('#editlicenceversion').val()).length )
					{
						error = true;
						$('#editlicenceversion').addClass('ui-state-error');
						alert('<?= _('You need to enter the version of the licence.');?>');
					}
					if( !$.trim($('#editlicence_number').val()).length )
					{
						error = true;
						$('#editlicence_number').addClass('ui-state-error');
						alert('<?= _('You need to enter the serial number of the licence.');?>');
					}
					if( !$.trim($('#editquantity').val()).length )
					{
						error = true;
						$('#editquantity').addClass('ui-state-error');
						alert('<?= _('You need to enter the assigned quantity of the licence.');?>');
					}
	
					if(error) return;
					
					// validations passed.
					$.post('ajaxsave.php', 
						$('#editlicence').serialize()+'&action=updatelicence',
						function(data){
							$("#savelicence").button({ disabled: false, icons: { primary: "ui-icon-disk" } });
						}
					);
					// Update any dropdowns with new values from our new entry.
					$.post('ajaxsave.php', 
						'action=updatejsvars',
						function(data)
						{
							$('body').append( data );
						}
					);
				}
				
			// ]]>
		</script>
	</head>
	<body>
		<div id="tabs">
			<ul>
				<li><a href="./loans.php?blockemails=true">Loans</a></li>
				<li><a href="./assets.php">Assets</a></li>
				<li><a href="./licences.php">Licences</a></li>
				<input type="text" id="searchtext" value="<?php echo $_COOKIE['search']; ?>" />
			</ul>
			<div id="tabs-1"></div>
			<button id="debug">Debug Autocomplete Values</button>
		</div>
		<p> TODO:</p>
		<ul>
			<li>viewuser() function, assigned assets, loan history</li>
			<li>tbody max-height</li>
			<li>In-place update changes in tbody</li>
			<li>Licences assigned to asset</li>
			<li>Reselect first input after entering new details </li>
			<li>Reports</li>
			<li>Remove $num, $out, $due</li>
			<li>Sort by columns</li>
			<li>Find a better way to do filtering, rather than cookies</li>
		</ul>
		<br />
		<div id="popup"></div>
	</body>
</html>
