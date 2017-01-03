<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script language="JavaScript" type="text/javascript" src="functions.js">
</head>

<body>
 {assign_adv var="data_array" value="array( array('nr'=>1, 'name'=>'Patrick'), array('nr'=>2, 'name'=>'Danny'), array('nr'=>3, 'name'=>Brigit') )"}
 {datatable data=$data_array sortable=1 cycle=1 mouseover=1 width="200px" row_onClick="row_clicked(\$nr, '\$name')"}
  {column id="nr" name="Number" align="right" sorttype="Numerical"}
  {column id="name" name="First Name" align="center"}
 {/datatable}

{javascript}
function row_clicked( nr, name )
{
  alert( 'You clicked row nr '+nr+', name is '+name+'.' );
}
{/javascript}
</body>
</html>