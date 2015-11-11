<?php

// Get the ID of each asset class, selected in asset data page

$id = $_GET["id"];

// connect to the MIRISK database

include("./connect_pg.php");
@$conn = connect_pg("MIRISK");

if(!$conn){
  echo "Fail to connect to the database";
  exit();
}


// EXTRACT text data from DB

$sql = "SELECT id, \"Asset_Class\", \"EQ_Description\", \"EQ_Damage\", \"EQ_Design\", \"Wind_Description\", \"Wind_Damage\", \"Wind_Design\", \"Flood_Description\", \"Flood_Damage\", \"Flood_Design\", \"Asset_Category\", \"Photo Description Path\", \"Photo Description Caption\", \"Photo Description Credit\", \"Photo ED Path\", \"Photo ED Caption\", \"Photo ED Credit\", \"Photo EM Path\", \"Photo EM Caption\", \"Photo EM Credit\", \"Photo WD Path\", \"Photo WD Caption\", \"Photo WD Credit\", \"Photo WM Path\", \"Photo WM Caption\", \"Photo WM Credit\", \"Photo FD Path\", \"Photo FD Caption\", \"Photo FD Credit\", \"Photo FM Path\", \"Photo FM Caption\", \"Photo FM Credit\" FROM assets2 WHERE id='$id'";

$res = pg_query($conn,$sql);

if(!$res){
  echo "Fail to get the data.";
}

$arr = pg_fetch_array($res,0);

/*
$handle = fopen("./html2pdf/pdfout/output.html", "wb");

fwrite($handle, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n");
fwrite($handle, "<HTML>\n");
fwrite($handle, "<HEAD>\n");
fwrite($handle, "<link href=\"../../../dbstyle.css\" rel=\"stylesheet\" type=\"text/css\">\n");
fwrite($handle, "</HEAD>\n");
fwrite($handle, "<BODY>\n");

fwrite($handle, "<TABLE WIDTH=\"800\" BORDER=\"0\">\n");
fwrite($handle, "<TR><TD ALIGN=\"right\">\n");
fwrite($handle, "<img src=\"../../../images/printicon.gif\">");
fwrite($handle, "<img src=\"../../../images/pdficon.gif\">\n");

fwrite($handle, "</TD>\n");
fwrite($handle, "</TR>\n");
fwrite($handle, "</TABLE>\n");

fwrite($handle, "<H1><U><A NAME=\"infotop\">".$arr[11].", ".$arr[1]."</A></U></H1><BR>\n");
fwrite($handle, "<img src=\"../../".$arr[12]."\"><BR><BR>\n");
fwrite($handle, $arr[13]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[14]."\n");

fwrite($handle, "<H2><U>Contents</U></H2>\n");
fwrite($handle, "<UL>\n");
fwrite($handle, "<LI><A HREF=\"#EQ-Description\">Earthquake Description</A><BR>\n");
fwrite($handle, "<LI><A HREF=\"#EQ-Performance\">Earthquake Performance</A><BR>\n");
fwrite($handle, "<LI><A HREF=\"#EQ-Design\">Earthquake Design</A><BR>\n");
fwrite($handle, "</UL>\n");

fwrite($handle, "<UL>\n");
fwrite($handle, "<LI><A HREF=\"#Wind-Description\">Wind Description</A><BR>\n");
fwrite($handle, "<LI><A HREF=\"#Wind-Performance\">Wind Performance</A><BR>\n");
fwrite($handle, "<LI><A HREF=\"#Wind-Design\">Wind Design</A><BR>\n");
fwrite($handle, "</UL>\n");

fwrite($handle, "<UL>\n");
fwrite($handle, "<LI><A HREF=\"#Flood-Description\">Flood Description</A><BR>\n");
fwrite($handle, "<LI><A HREF=\"#Flood-Performance\">Flood Performance</A><BR>\n");
fwrite($handle, "<LI><A HREF=\"#Flood-Design\">Flood Design</A><BR>\n");
fwrite($handle, "</UL>\n");

fwrite($handle, "<H2>Earthquake</H2>\n");
fwrite($handle, "<H3><A NAME=\"EQ-Description\">Earthquake Description</A></H3>\n");
fwrite($handle, $arr[2]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");
fwrite($handle, "<H3><A NAME=\"EQ-Performance\">Typical Seismic Damage and Performance</A></H3>\n");
fwrite($handle, "<img src=\"../../".$arr[15]."\"><BR><BR>\n");
fwrite($handle, $arr[16]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[17]."<BR><BR>\n");
fwrite($handle, $arr[3]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");
fwrite($handle, "<H3><A NAME=\"EQ-Design\">Seismic Resistant Design</A></H3>\n");
fwrite($handle, "<img src=\"../../".$arr[18]."\"><BR><BR>\n");
fwrite($handle, $arr[19]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[20]."<BR><BR>\n");
fwrite($handle, $arr[4]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");

fwrite($handle, "<H2>Wind</H2>\n");
fwrite($handle, "<H3><A NAME=\"Wind-Description\">Wind Description</A></H3>\n");
fwrite($handle, $arr[5]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");
fwrite($handle, "<H3><A NAME=\"Wind-Performance\">Typical Wind Damage and Performance</A></H3>\n");
fwrite($handle, "<img src=\"../../".$arr[21]."\"><BR><BR>\n");
fwrite($handle, $arr[22]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[23]."<BR><BR>\n");
fwrite($handle, $arr[6]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");
fwrite($handle, "<H3><A NAME=\"Wind-Design\">Wind Resistant Design</A></H3>");
fwrite($handle, "<img src=\"../../".$arr[24]."\"><BR><BR>\n");
fwrite($handle, $arr[25]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[26]."<BR><BR>\n");
fwrite($handle, $arr[7]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");

fwrite($handle, "<H2>Flood</H2>\n");
fwrite($handle, "<H3><A NAME=\"Flood-Description\">Flood Description</A></H3>\n");
fwrite($handle, $arr[8]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");
fwrite($handle, "<H3><A NAME=\"Flood-Performance\">Typical Flood Damage and Performance</A></H3>\n");
fwrite($handle, "<img src=\"../../".$arr[27]."\"><BR><BR>\n");
fwrite($handle, $arr[28]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[29]."<BR><BR>\n");
fwrite($handle, $arr[9]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");
fwrite($handle, "<H3><A NAME=\"Flood-Design\">Flood Resistant Design</A></H3>");
fwrite($handle, "<img src=\"../../".$arr[30]."\"><BR><BR>\n");
fwrite($handle, $arr[31]."<BR>\n");
fwrite($handle, "<U>Photo Source:</U> ".$arr[32]."<BR><BR>\n");
fwrite($handle, $arr[10]);
fwrite($handle, "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n");

fwrite($handle, "</BODY>\n");
fwrite($handle, "</HTML>\n");

fclose($handle); 
*/

// Display text information stored in DB
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<HTML>\n";
echo "<HEAD>\n";
echo "<link href=\"../dbstyle.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo "</HEAD>\n";
echo "<BODY>\n";

echo "<TABLE WIDTH=\"800\" BORDER=\"0\">\n";
echo "<TR><TD ALIGN=\"right\">\n";
echo "<img src=\"../images/printicon.gif\" onClick=\"window.print()\">";
//echo "<img src=\"../images/pdficon.gif\" onclick=\"window.open('./makepdf.php?file=display_textdata.php?id=",$id, "')\">\n";
//echo "<img src=\"../images/pdficon.gif\" onclick=\"window.open('./html2pdf/makepdf.php?file=output.html')\">\n";
echo "<img src=\"../images/pdficon.gif\">\n";

echo "</TD>\n";
echo "</TR>\n";
echo "</TABLE>\n";

echo "<H1><U><A NAME=\"infotop\">",$arr[11],", ",$arr[1],"</A></U></H1><BR>\n";
echo "<img src=\"",$arr[12],"\"><BR><BR>\n";
echo $arr[13],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[14],"\n";

echo "<H2><U>Contents</U></H2>\n";
echo "<UL>\n";
echo "<LI><A HREF=\"#EQ-Description\">Earthquake Description</A><BR>\n";
echo "<LI><A HREF=\"#EQ-Performance\">Earthquake Performance</A><BR>\n";
echo "<LI><A HREF=\"#EQ-Design\">Earthquake Design</A><BR>\n";
echo "</UL>\n";

echo "<UL>\n";
echo "<LI><A HREF=\"#Wind-Description\">Wind Description</A><BR>\n";
echo "<LI><A HREF=\"#Wind-Performance\">Wind Performance</A><BR>\n";
echo "<LI><A HREF=\"#Wind-Design\">Wind Design</A><BR>\n";
echo "</UL>\n";

echo "<UL>\n";
echo "<LI><A HREF=\"#Flood-Description\">Flood Description</A><BR>\n";
echo "<LI><A HREF=\"#Flood-Performance\">Flood Performance</A><BR>\n";
echo "<LI><A HREF=\"#Flood-Design\">Flood Design</A><BR>\n";
echo "</UL>\n";

echo "<H2>Earthquake</H2>\n";
echo "<H3><A NAME=\"EQ-Description\">Earthquake Description</A></H3>\n";
echo $arr[2];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
echo "<H3><A NAME=\"EQ-Performance\">Typical Seismic Damage and Performance</A></H3>\n";
echo "<img src=\"",$arr[15],"\"><BR><BR>\n";
echo $arr[16],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[17],"<BR><BR>\n";
echo $arr[3];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
echo "<H3><A NAME=\"EQ-Design\">Seismic Resistant Design</A></H3>\n";
echo "<img src=\"",$arr[18],"\"><BR><BR>\n";
echo $arr[19],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[20],"<BR><BR>\n";
echo $arr[4];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";

echo "<H2>Wind</H2>\n";
echo "<H3><A NAME=\"Wind-Description\">Wind Description</A></H3>\n";
echo $arr[5];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
echo "<H3><A NAME=\"Wind-Performance\">Typical Wind Damage and Performance</A></H3>\n";
echo "<img src=\"",$arr[21],"\"><BR><BR>\n";
echo $arr[22],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[23],"<BR><BR>\n";
echo $arr[6];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
echo "<H3><A NAME=\"Wind-Design\">Wind Resistant Design</A></H3>";
echo "<img src=\"",$arr[24],"\"><BR><BR>\n";
echo $arr[25],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[26],"<BR><BR>\n";
echo $arr[7];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";

echo "<H2>Flood</H2>\n";
echo "<H3><A NAME=\"Flood-Description\">Flood Description</A></H3>\n";
echo $arr[8];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
echo "<H3><A NAME=\"Flood-Performance\">Typical Flood Damage and Performance</A></H3>\n";
echo "<img src=\"",$arr[27],"\"><BR><BR>\n";
echo $arr[28],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[29],"<BR><BR>\n";
echo $arr[9];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
echo "<H3><A NAME=\"Flood-Design\">Flood Resistant Design</A></H3>";
echo "<img src=\"",$arr[30],"\"><BR><BR>\n";
echo $arr[31],"<BR>\n";
echo "<U>Photo Source:</U> ",$arr[32],"<BR><BR>\n";
echo $arr[10];
echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";

echo "</BODY>\n";
echo "</HTML>\n";
?>
