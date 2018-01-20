<?php
header('Content-Type: text/html; charset=UTF-8');
$filename='keijiban.txt';
$deletefile='sakujo.txt';

$fp=fopen($filename,'a');
fclose($fp);
$posts = file($filename);

$fp_d=fopen($deletefile,'a');
fclose($fp_d);
$posts_d = file($deletefile);

$name=null;
$message=null;

$name=$_POST["input"][0];
$message=$_POST["input"][1];
$pass=$_POST["input"][2];
$deleteNo=$_POST["delete_num"];
$deletepass=$_POST["delete_pass"];
$editNo=$_POST["edit_num"];
$editpass=$_POST["edit_pass"];
$add_edit=null;//0だと編集モードoff
$time=date('Y-m-d H:i:s'); 
//sizeof(変数)　指定した変数内に格納されている変数のインデックス値を返す 
//↓はうまくいく　最後の投稿番号+1の形
$lastposts=explode("<>",$posts[sizeof($posts)-1]);
$num=$lastposts[0]+1;

// ===→型と値が等しいときTRUE ==→値が等しいときTRUE
// REQUEST_METHOD→ページにアクセスする際に使用されたリクエストのメソッド名
// $_SERVER['REQUEST_METHOD']ページがリクエストされたときのリクエストメソッド名を返す
//isset(関数) 変数がセットされている、null出ないことを検査→true/false
//
//三項演算子での振り分け→条件 ? 条件に一致した場合に返す値 : 条件に一致しない場合に返す値
if($_SERVER["REQUEST_METHOD"] == "POST"){
 //関数定義
 $add_edit="add";
 $error_name=null;
 $error_message=null;
 $error_pass=null;
 $error_deleteNo=null;
 $num_error=null;
 //isset()で変更できるきがする
 if($name==null and $message==null){
  $num_error=1;
  }else{
   if($message==null){
    $num_error=2;
    }else{
	 if($name==null){
	  $num_error=3;
	  }else{
	  $num_error=4;
	  if(empty($pass)){$num_error=3.5;}
	   }
   }
 }
   //if(isset(a,b)) a,bが全てnullでなければ
   //if(!isset(a,b)) a,bのうちどれか一つでもnullなら

// echo $add_edit;
//echo $num_error;

 switch($_POST["mode"]){
  case "add":
  //== が等号　=は「代入できる」なので注意
   switch($num_error){
    case 1:
     $error_name="名前が入力されていません";
	 $error_message="コメントが入力されていません";
	 if($pass==null){
	 $error_pass="パスワードが未入力です";
	 }
	 break;
    case 2:
	 $error_message="コメントが入力されていません";
	 if($pass==null){
	 $error_pass="パスワードが未入力です";
	 }
	 break;
    case 3:
	 $error_name="名前が入力されていません";
	 if($pass==null){
	 $error_pass="パスワードが未入力です";
	 }
	 break;
	case 3.5;
	 $error_pass="パスワードが未入力です";
	 break;
    case 4:
     $fp=fopen($filename,'a'); 
	 fwrite($fp,$num."<>".$name."<>".$message."<>".$time."<>".$pass."<>");
	 fwrite($fp,"\n");
     fclose($fp);
	 break;	 
   }
   break;
   
  case "delete":
  if($deletepass==null){
  $error_pass_delete="パスワードが未入力です";
	}else{
   if($deleteNo==null){
   //echo "Noなし";
   }else{
	for($j=0; $j<count($posts); $j++){
		$aa=explode("<>",$posts[$j]);
		//if($aa[0]==$deleteNo or $aa[4]==$deletepass){
		//strcmp(文字列1, 文字列2)==0で文字列比較
		if($aa[0]==$deleteNo && strcmp($aa[4],$deletepass)==0){
			$fp_d=fopen($deletefile,'a');
			fwrite($fp_d,$aa[0]."<>"."\n");
			fclose($fp_d);
			$error_pass_delete=null;
			break;
			/*以下元々
			array_splice($posts,$j,1);
			file_put_contents($filename,$posts);
			*/
		}else{
		$error_pass_delete="パスワードが間違っています";
		}
	}
	//ここに配列=array_values(配列)入れれば歯抜けの要素が詰められる？
	//foreach($posts as $key => $value){
	//	echo $key."<>".$value."\n";
	//}	
	//$posts=array_values($posts);
	//foreach($posts as $key => $value){	
	//	echo $key."<>".$value."\n";
	//}
    }
   }
   break; 
   
   case "edit_set":
    if(isset($editNo)){
	//echo $editNo;  
	if(isset($editpass)){
	//echo $editpass; 	
   	for($j=0; $j<count($posts); $j++){  
		$aa=explode("<>",$posts[$j]);
		if($aa[0]==$editNo && strcmp($aa[4],$editpass)==0){
			$add_edit="edit";//編集モードon
			//echo $add_edit;
			$edit_number_send=$aa[0];
			$edit_name=$aa[1];
			$edit_message=$aa[2];
			$edit_pass_p=$aa[4];
			$edit_mode_label="＜編集モード＞";
			$error_pass_edit=null;
			//echo $aa[1];
			//echo $aa[2];
			break;
		}
		$error_pass_edit="パスワードが間違っています";
		}
    }else{
    $error_pass_edit="パスワードが未入力です";   
     }
    }else{
    //$error_pass="パスワードが未入力です";  
     }
    break; 
   
   case "edit":
    //echo "edit_mode"."<br>";
    //echo $_POST["e_number"]."<br>";
	if(isset($name,$message)){
    for($j=0; $j<count($posts); $j++){
		$aa=explode("<>",$posts[$j]);
		if($aa[0]==$_POST["e_number"]){
		//array_splice(配列,削除開始位置,削除する配列要素数,新たな配列要素) 配列要素編集
		//$aa内で行い、さらに$posts内で行う
			$edit_array=array($name,$message,$time);
			//print_r($edit_array);
			//echo $aa."<br>";
			//print_r($aa);
			array_splice($aa,1,3,$edit_array);
			//print_r($aa);
			$aa_edit_im=implode("<>",$aa);
			//echo $aa_edit_im;
			array_splice($posts,$j,1,$aa_edit_im);
			file_put_contents($filename,$posts);
		}
	}
   $add_edit="add";
   }
   break; 
 }
} 

//コメント取得処理
//全てのコメントを変数で受け取る
$comment = null;
$posts = file($filename);
$posts_d = file($deletefile);
 foreach($posts as $post){
 //listは
 list($num, $name, $message, $time) = explode("<>", $post);
 //↓ここから削除処理**l.120~122に対応 不要なら両方消す
  foreach($posts_d as $post_d){
 $delete_n=explode("<>", $post_d);
 if($num==$delete_n[0]){
	$name=null;
	$message="削除されました";
	$time=null;
	}
 }
 //↑ここまで
 //.= は結合代入演算子　$commentに.=以降を加える
 $comment.= h($num);
 $comment.="<br>";
 $comment.=h($name);
 $comment.="<br>";
 $comment.=h($message);
 $comment.="<br>";
 $comment.=h($time);
 $comment.="<br><hr>";
 }

 
  function h($s){
 //htmlspecialchars　htmlのエンティティ化(>や""などを単なる文字列変換する)
 //htmlspecialchars(文字列,(フラグ,エンコード←これら省略化))
 //ENT_QUOTES:シングル、ダブルクォートを変換する
 return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
 }
?>

<html lang="ja">
<head>
<meta http-equiv="Content-Type" charset="UTF-8">
<title>散歩日記</title>
</head>
<body>
<h1>散歩日記</h1>
 <!-htmlのformタグでデータの受け渡しをする->
 <!-action属性で指定ファイルにデータ受け渡し method属性で送信方法指定 postはURLとは別にデータを送る方法->
 <!-postはURLで引き渡される送信方法->
<form action='mission_2-6.php' method='post'>
 <!-input typeは指定フィールド　nameは指定の名前　valueでボタンに表示する名前　※typeと他1つが必須->
 <!-変数名[] で二次元配列的に値を受け取る　同一フォーム内で複数の値を受け取れる->
 <!-html内ではphpの記号は使えない $とか->
<font size='-1'>名前:</font><br/>
<input type='text' name="input[]" value="<?php echo $edit_name ?>" required><?php echo $error_name; ?><br/>
<font size='-1'>コメント:</font><br/>
<!-textareaタグ colsが幅 rowsが高さ /textareaで終了->
<textarea name="input[]" cols="50" rows="5" required><?php echo $edit_message ?></textarea><?php echo $error_message; ?><br/>
<!-input type='text' size='50' name="input[]" ->
<!-a.2-1の入力フォームの項目にパスワードを追加する->
<font size='-1'>パスワード(半角英数8文字まで):</font><br/>
<input type='password' name="input[]" maxlength="8" value="<?php echo $edit_pass_p ?>" required><?php echo $error_pass; ?><br/>
<input type='submit' value='送信'><?php echo $edit_mode_label ?><br/><br/>
<input type='hidden' name="mode" value="<?php echo $add_edit ?>">
<input type='hidden' name="e_number" value="<?php echo $edit_number_send ?>">
</form>
<form action='mission_2-6.php' method='post'>
<font size='-1'>削除対象番号:</font><br/>
<input type='text' size='10' name="delete_num" placeholder="半角数字" required><input type='password' name="delete_pass" placeholder="投稿時パスワード" required><?php echo $error_deleteNo; ?><?php echo $error_pass_delete; ?><br/>
<input type='submit' value='削除'><br/><br/>
<input type='hidden' name="mode" value="delete">
</form>
<!-編集番号指定フォーム作成->
<form action='mission_2-6.php' method='post'>
<font size='-1'>編集対象番号:</font><br/>
<input type='text' size='10' name="edit_num" placeholder="半角数字" required><input type='password' name="edit_pass" placeholder="投稿時パスワード" required><?php echo $error_editNo; ?><?php echo $error_pass_edit; ?><br/>
<input type='submit' value='編集'><br/><br/>
<input type='hidden' name="mode" value="edit_set">
</form>

<?php
 
echo $comment;
/*
echo"<pre>";
print_r($posts);
echo"</pre>";
echo"<br/>";
*/
?>
</body>
</html>