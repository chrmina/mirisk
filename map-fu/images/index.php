<?php
$protocol=(empty($_SERVER['HTTPS']))?"http://":"https://";
$dirs=explode("/",dirname($_SERVER['PHP_SELF']));
unset($dirs[count($dirs)-1]);
$full_path=$protocol.$_SERVER['HTTP_HOST'].implode("/",$dirs);
header("Location: $full_path");
exit;
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Forbidden</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="refresh" content="2;url=<?php echo $full_path; ?>">
</head>
<body>
<font size="+2"><strong>Forbidden. Redirecting ...</strong></font>
</body>
</html>
