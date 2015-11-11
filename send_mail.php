<?php
@extract($_POST);
$name = stripslashes($name);
$email = stripslashes($email);
$subject = stripslashes($subject);
$text01 = stripslashes($text01);
$rating01 = stripslashes($rating01);
$text01a = stripslashes($text01a);
$rating01a = stripslashes($rating01a);

$headers =
  'Reply-To: webmaster@mirisk.dum.kyoto-u.ac.jp'."\r\n"
  .'X-Mailer: PHP/'.phpversion()."\r\n"
  .'MIME-Version: 1.0'."\r\n"
  .'Content-type: text/html; charset=iso-8859-1'."\r\n"
  .'From: '.$name.'<'.$email.'>'."\r\n"
  .'Cc: cscawthorn@worldnet.att.net'."\r\n";
  //.'Bcc: birthdaycheck@example.com'."\r\n";

$message =
  // Submitters Data
  "<B><U>Submitter's Name:</U></B> ".$name."<BR>\n"
  ."<B><U>Submitter's E-mail:</U></B> ".$email."<BR><BR>\n"
  ."<HR><BR>\n"
  // Main tab
  ."<B><U>1. Main (opening screen)</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating01."<BR>\n"
  ."<B>Comments:</B> <pre>".$text01."</pre>\n"
  ."<U>1a. Attractiveness</U><BR>\n"
  ."<B>Rating:</B> ".$rating01a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text01a."</pre>\n"
  ."<U>1b. Informativeness</U><BR>\n"
  ."<B>Rating:</B> ".$rating01b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text01b."</pre>\n"
  ."<U>1c. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating01c."<BR>\n"
  ."<B>Comments:</B> <pre>".$text01c."</pre><BR>\n"
  ."<HR><BR>\n"
  // Wiki
  ."<B><U>2. Wiki</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating02."<BR>\n"
  ."<B>Comments:</B> <pre>".$text02."</pre>\n"
  ."<U>2a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating02a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text02a."</pre>\n"
  ."<U>2b. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating02b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text02b."</pre><BR>\n"
  ."<HR><BR>\n"
  // Tutorial
  ."<B><U>3. Tutorial</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating03."<BR>\n"
  ."<B>Comments:</B> <pre>".$text03."</pre>\n"
  ."<U>3a. Accessibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating03a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text03a."</pre>\n"
  ."<U>3b. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating03b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text03b."</pre>\n"
  ."<U>3c. Informativeness</U><BR>\n"
  ."<B>Rating:</B> ".$rating03c."<BR>\n"
  ."<B>Comments:</B> <pre>".$text03c."</pre><BR>\n"
  ."<U>3d. Duration</U><BR>\n"
  ."<B>Rating:</B> ".$rating03d."<BR>\n"
  ."<B>Comments:</B> <pre>".$text03d."</pre>\n"
  ."<U>3e. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating03e."<BR>\n"
  ."<B>Comments:</B> <pre>".$text03e."</pre><BR>\n"
  ."<HR><BR>\n"
  // Tab 1 : Project Data
  ."<B><U>4. Tab 1: Project Data</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating04."<BR>\n"
  ."<B>Comments:</B> <pre>".$text04."</pre>\n"
  ."<U>4a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating04a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text04a."</pre>\n"
  ."<U>4b. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating04b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text04b."</pre>\n"
  ."<U>4c. Data Entry</U><BR>\n"
  ."<B>Rating:</B> ".$rating04c."<BR>\n"
  ."<B>Comments:</B> <pre>".$text04c."</pre><BR>\n"
  ."<HR><BR>\n"
  // Tab 2 : Location/Hazard Data
  ."<B><U>5. Tab 2: Location/Hazard Data</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating05."<BR>\n"
  ."<B>Comments:</B> <pre>".$text05."</pre>\n"
  ."<U>5a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating05a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text05a."</pre>\n"
  ."<U>5b. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating05b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text05b."</pre><BR>\n"
  ."<HR><BR>\n"
  // Tab 3 : Component (Asset) Data
  ."<B><U>6. Tab 3: Component (Asset) Data</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating06."<BR>\n"
  ."<B>Comments:</B> <pre>".$text06."</pre>\n"
  ."<U>6a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating06a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text06a."</pre>\n"
  ."<U>6b. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating06b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text06b."</pre><BR>\n"
  ."<HR><BR>\n"
  // Tab 4 : Analysis/Report
  ."<B><U>7. Tab 4: Analysis/Report</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating07."<BR>\n"
  ."<B>Comments:</B> <pre>".$text07."</pre>\n"
  ."<U>7a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating07a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text07a."</pre>\n"
  ."<U>7b. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating07b."<BR>\n"
  ."<B>Comments:</B> <pre>".$text07b."</pre><BR>\n"
  ."<HR><BR>\n"
  // Help
  ."<B><U>8. Help</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating08."<BR>\n"
  ."<B>Comments:</B> <pre>".$text08."</pre>\n"
  ."<U>8a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating08a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text08a."</pre><BR>\n"
  ."<HR><BR>\n"
  // About
  ."<B><U>9. About</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating09."<BR>\n"
  ."<B>Comments:</B> <pre>".$text09."</pre>\n"
  ."<U>9a. Useability</U><BR>\n"
  ."<B>Rating:</B> ".$rating09a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text09a."</pre><BR>\n"
  ."<HR><BR>\n"
  // Peer Review
  ."<B><U>10. Peer Review</U></B><BR>\n"
  ."<B>Rating:</B> ".$rating10."<BR>\n"
  ."<B>Comments:</B> <pre>".$text10."</pre>\n"
  ."<U>10a. Comprehensibility</U><BR>\n"
  ."<B>Rating:</B> ".$rating10a."<BR>\n"
  ."<B>Comments:</B> <pre>".$text10a."</pre><BR>\n"
  ."<HR><BR>\n"
  // Other Comments
  ."<B><U>11. Other Comments</U></B><BR>\n"
  ."<B>Comments:</B> <pre>".$text11."</pre>\n";

mail('chrismina@civil.mbox.media.kyoto-u.ac.jp',$subject,$message,$headers);
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>MIRISK - projectdatasave.php</title>\n";
echo "  <meta name=\"description\" content=\"Mitigation Information and Risk Identification System\">\n";
echo "  <meta name=\"author\" content=\"MIRISK Team\">\n";
echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
echo "  <meta name=\"abstract\" content=\"MIRISK\">\n";
echo "  <meta name=\"description\" content=\"MIRISK\">\n";
echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
echo "  <link href=\"dbstyle.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo "</head>\n";
echo "<body>\n";
echo "<CENTER>\n";
echo "<H1>Thank you for your feedback.</H1>\n";
echo "<BR>\n";
echo "<H3>Thank you for taking the time to complete the Peer Review of MIRISK.<BR>\n";
echo "Your feedback is very important and will be used to make MIRISK work best for YOU!</H3><BR><BR>\n";
echo "Return to <A HREF=\"javascript:javascript:history.go(-1)\">the previous page</A>.<BR>\n";
echo "(USE ONLY if you wish to revise your Feedback at this time - your Feedback cannot be revised later, but must be submitted as a complete new Feedback).<BR><BR>\n";
echo "Return to the <A HREF=\"./index.html\">MIRISK Main Page.</A>\n";
echo "</CENTER>\n";
echo "</BODY>\n";
echo "</HTML>\n";
?>
