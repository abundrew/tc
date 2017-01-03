{* Smarty *}
<head>
<title>Hello!</title>
</head>
<body>
Hello, {$name}!<br>
{$smarty.now|date_format:"%Y/%m/%d"}<br>
{$sentence|capitalize}
</body>