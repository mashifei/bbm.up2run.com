<? session_start()?>
<?php
	include "config.inc.php";
	$action = htmlspecialchars($_GET['action'], ENT_QUOTES);
	if(!isSet($_SESSION['user_name']))
	{
		header("Location: login.php");
		exit;
	}
?>
<html >
<head>
	<title>books</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<LINK REL="StyleSheet" HREF="paginator.css" TYPE="text/css" >
	<? bs_here();?>
	<style type="text/css">
	#wrapper {
		width: 600px;
		margin: 60px auto 0;
		padding-top: 200px；
		font: 1.2em Verdana, Arial, sans-serif;
	}
	</style>
</head>
<body>
<? include "banner.php" ?>
<div id="wrapper">
	


<h1>books </h1>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=search"  method="post" class="form-inline">
	<input type="text" placeholder="some book title..." id="title" name="title" 
	value="<?echo $_POST["title"]; ?>" class="search-query input-medium"/>
	<input type="submit" value="search" class="btn-primary"/>
	<? if (is_login()) { ?>
		<a href ="book_new.php" class="btn">new book</a>
		<a href ="book_upload.php" class="btn">upload</a>
		<!-- <a href="logout.php" class="btn">logout</a>		 -->
	<?}else{?>
		<a href="login.php" class="btn">login</a>
	<? } ?>

</form>
<table  class="table table-striped table-bordered">
	<tr>
		<th>#</th>
		<th>No.</th>
		<th>book title</th>
		<th>borrowed</th>
		<th>devoter</th>
		<th>borrower</th>
	</tr>
<?
	$page = $_GET['page'];
	if (!$page)
		$page = 1;
	$pagerecords = 10 ;
	$from = ($page-1)*$pagerecords;
	$to = $pagerecords;
	if ($dbcheck) {
		$sql = "
select b.id ,b.title ,u.email as devoter_name,b.devote_id,b.state ,b.borrow_user_id ,
u1.email as b_name
from book b 
left join user u on b.devote_id = u.id
left join user u1 on b.borrow_user_id = u1.id		
		 ";
		if ($action=="search"){
			$title = trim($_POST['title']);
			if($title != "")
				$sql = $sql . " where title like '%". $title ."%' ";
			
			//echo $sql ;
		}
		$count_sql = "select count(1) from (${sql}) balias";
		$sql = $sql . " limit ${from},${to}";
		 // echo $sql;
		// echo $count_sql;
		$result = mysql_query($count_sql);
		$row = mysql_fetch_row($result);
		$total_records = $row[0];
		// echo $total_records;
		$result = mysql_query($sql);
		if (!$result){
			echo mysql_error();
		}else
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_row($result)) {
				$devote_id = $row[3];
				$curr_user_id = $_SESSION["user_id"];
				$borrowed = $row[4]==1;
				$url_e ="";
				$url_d ="";
				$url_borrow="";
				if (!$borrowed)
					$url_borrow = "<a class='' href='book_borrow.php?id=".$row[0]."'>Borrow</a>&nbsp;" ;
				if ($devote_id == $curr_user_id){
					 $url_e = "<a  href='book_edit.php?id=".$row[0]."'>Edit</a>&nbsp;" ;
					 $url_d = "<a  href='book_delete.php?id=".$row[0]."'>Del</a>&nbsp;" ;
				
				}
				$bstr = get_state($row[4]);
				$btn_group = "<div class=''>".$url_e. $url_d.$url_borrow."</div>" ;
				echo "<tr>" .
				 	  "<td>" . $btn_group. "</td>" . 
				 	  "<td>" . $row[0] . "</td>" .
				 	  "<td>" . $row[1] . "</td>" .
				 	  "<td>" . $bstr . "</td>" .
				 	  "<td>" .abbr($row[2]) . "</td>".
				 	  "<td>" . abbr($row[6]) . "</td>".
				 	  "<tr>";
			}
		} 
	}
?>
</table>
<?	
	include "paginator.php";
	$target = $_SERVER['PHP_SELF'];
	echo getPaginationString($page, $total_records, 
		$pagerecords, 1, $target, $pagestring = "?page=");
	echo "totals: " . $total_records;
	echo "&nbsp; ";
	if (is_login())
	 	echo "user:". $_SESSION["user_name"];
	 	echo "uid:".  $_SESSION["user_id"];
	 	// print_r($_SESSION);
?>


</div><!-- end #wrapper -->
</body>
</html>