<!--   
CONTENTdm Version 4.3000.0    
(c) DiMeMa, Inc. 2007 - All Rights Reserved
//-->

<? include("config.php"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//<?=strtoupper(LANG)?>"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANG?>" lang="<?=LANG?>">
<head>

<meta http-equiv="content-type" content="text/html; charset=<?=CHARSET?>" />
	
<title><?=L_SITE_TITLE?> <?=L_MENU_SPACER_1?> <?=ucfirst(L_REFERENCE_URL)?></title>

<? include(INCLUDE_PATH."js_scripts.php"); ?>

</head>

<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" rightmargin="0" bgcolor="<?=S_HEADER_BG?>">

<table cellpadding="<?=S_RESULTS_PADDING_1?>" cellspacing="0" border="0" width="100%">
<tr>
	<td align="left" valign="top">

	<table cellpadding="<?=S_RESULTS_PADDING_2?>" cellspacing="0" border="0" width="100%" bgcolor="<?=S_PAGE_BG?>">
	<tr>
		<td align="left" valign="top" style="border-top: <?=S_RESULTS_BORDER?>px solid <?=S_RESULTS_BORDER_BG?>;border-bottom: <?=S_RESULTS_BORDER?>px solid <?=S_RESULTS_BORDER_BG?>;border-right: <?=S_RESULTS_BORDER?>px solid <?=S_RESULTS_BORDER_BG?>;border-left: <?=S_RESULTS_BORDER?>px solid <?=S_RESULTS_BORDER_BG?>">

		<table width="100%" cellpadding="5" cellspacing="0" border="0" bgcolor="<?=S_FORM_PAGES_BG?>">

		<form name="cita">
		
		<tr>
			<td valign="top"><span class="maintext">

			<p><br />

			<?=ucfirst(L_REFERENCE_URL_TEXT)?>

			</p>
			</span></td>
		</tr>
		<tr>
			<td valign="top" align="center" bgcolor="<?=S_FORM_FIELDS_BG?>">

			<input type="text" name="tion" value="<?=S_PROTOCOL?>://<?=$_SERVER['HTTP_HOST']?>/u?<?=$_GET['CISOROOT']?>,<?=$_GET['CISOPTR']?>" style="width:100%" onclick="document.cita.tion.select()">

			</td>
		</tr>
		<tr>
			<td valign="top" align="right">
			<input type="button" class="buttons" value="<?=L_CLOSE?>" onclick="self.close()" /><br /></td>
		</tr>

		</form>
	
		</table>
		
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</body>
</html>