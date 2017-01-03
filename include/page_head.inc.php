<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $config['title']; ?></title>
        <script type="text/javascript" src="<?php echo $web_path; ?>js/jquery-1.4.2.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo $web_path; ?>css/jquery.ui.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $web_path; ?>css/style.css" />
    </head>
    <body><div id="container">
        <hr>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="center"><h1><?php echo $config['title'].'&nbsp;-&nbsp;'.$page_title; ?></h1></td>
                <td align="right" valign="center">
<?php

if (isset($help_page))
{
    echo '<img src="'.$web_path.'image/help_48x48.png" alt="" title="Help" border="0" onclick="javascript:'."window.open('$help_page', 'Help', 'status=0, menubar=no, width=500, height=400, toolbar=no, left=300, top=300, dependable=0, resizable=0');".'">';
}
?>
                </td>
            </tr>
        </table><hr>
       