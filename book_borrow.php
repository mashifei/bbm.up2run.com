<?
	session_start();
	include "config.inc.php";
	$id = $_GET['id'];
	$sql = "select title from book where id='${id}'";
	$result = db_query($sql);
	if (!$result){
		echo mysql_error();
	}else {
		if (db_row_count($result) >0){
			$row = db_fetch_row($result);
			$title = $row[0];
			$sql = "select id from  book where id='${id}' ";
			$result = db_query($sql);
			if (!result){
				echo mysql_error();
			}else if (db_row_count($result)>0){
				$user_id = $_SESSION['user_id'];
				$sql = "update book set state = 1,borrow_user_id =${user_id}  where id=${id}";
				echo $sql;
				$result = db_query($sql);
				// $book_id = $id ;
				// $sql = "insert into borrowed (user_id,book_id) values('${user_id}','${book_id}')";
				// echo $sql;
				// $result = db_query($sql);
				if (!$result)
					echo mysql_error();
			}
		}
	}
?>
<html>
<head>
	<title>borrowed </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<LINK REL="StyleSheet" HREF="bootstrap/css/bootstrap.min.css" TYPE="text/css" >
	<style type="text/css">
	#wrapper {
		width: 600px;
		padding-top: 100px;
		margin: 20px auto 0;
		font: 1.2em Verdana, Arial, sans-serif;
	}
	</style>
</head>
<body>	
	<? include "banner.php";?>
	<div id="wrapper">
		<h1>borrowed  </h1>
		<p>  <?echo $title;?></p>
		<a href="book_list.php" class="btn">back list</a>
		<a href="book_borrow_commit.php?action=commit" class="btn">commit</a>
		<h1>cart</h1>
		<table cellpadding="2" class="table table-striped table-bordered">
		<tr>
			<th>#</th>
			<th>No.</th>
			<th>book title</th>
			<th>devoter</th>
		</tr>
	
<?
	$page = $_GET['page'];
	if (!$page)
		$page = 1;
	$pagerecords = 10 ;
	$from = ($page-1)*$pagerecords;
	$to = $pagerecords;
	if ($dbcheck) {
		$sql = "select bo.id ,bo.title ,bo.devote_id ,u.email 
			from book bo
			left join user u on bo.devote_id = u.id 
			where bo.state = 1 ";//1== incart
		$count_sql = "select count(1) from (${sql}) balias";
		$sql = $sql . " limit ${from},${to}";
		// echo $count_sql;
		$result = db_query($count_sql);
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
				$url_d = "<a  href='book_remove_from_cart.php?id=".$row[0]."'>Remove from cart</a>&nbsp;" ;
				$btn_group = "<div class=''>".$url_d."</div>" ;
				echo "<tr>" .
				 	  "<td>" . $btn_group. "</td>" . 
				 	  "<td>" . $row[0] . "</td>" .
				 	  "<td>" . $row[1] . "</td>" .
				 	  "<td>" . $row[3] . "</td>" .
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
	
?>
	</div>
</body>
</html>