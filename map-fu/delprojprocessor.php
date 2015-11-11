<?
/****************************************************************************
infoclick.php - takes a click point in extent coordinates ($x, $y), and spits
back the metadata of all features that intersect with a radius around the
click.
*****************************************************************************/

//require_once("../includes/php/.root_config.php");
require_once("./config.php");
import_request_variables("gP", "req_");

session_set_cookie_params(7200);
session_start();

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>MIRISK - delprojprocessor.php</title>\n";
echo "  <meta name=\"description\" content=\"Mitigation Information and Risk Identification System\">\n";
echo "  <meta name=\"author\" content=\"MIRISK Team\">\n";
echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
echo "  <meta name=\"abstract\" content=\"MIRISK\">\n";
echo "  <meta name=\"description\" content=\"MIRISK\">\n";
echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
echo "  <link href=\"../dbstyle.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo "</head>\n";
echo "<body>\n";
echo "<center>\n";
echo "<form name=\"projsave\" method =\"POST\" action=\"./delproj.php\" target=\"result\" onsubmit=\"window.open('','result','width=360,height=360')\">\n";
echo "<B>Input Component ID to be DELETED!</B><BR>\n";
echo "(Component ID's are shown as yellow numbers on the map.)<BR><BR>\n";
echo "<input textarea name=\"projectid\" wrap=virtual rows=1 cols=27></textarea><BR>\n";
echo "<BR><BR>\n";
echo "<input type=\"submit\" name=\"deletedata\" value=\"Delete Component\">\n";
echo "</form>\n";
echo "</center>\n";

?>
