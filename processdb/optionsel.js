var cat = new Array();
var firstcat = new Array();
var catID = new Array();

firstcat[0] ="Buildings";
firstcat[1] ="Transportation";
firstcat[2] ="Utilities and Industry";


//Category Buildings
cat[0] = new Array();
cat[0][0]="Wood";
cat[0][1]="Light Metal"; 
cat[0][2]="Low-Rise Reinforced Masonry or Reinforced Concrete"; 
cat[0][3]="Mid-Rise Reinforced Masonry or Reinforced Concrete"; 
cat[0][4]="High-Rise Reinforced Masonry or Reinforced Concrete";
cat[0][5]="Low-Rise Steel";
cat[0][6]="Mid-Rise Steel";
cat[0][7]="High-Rise Steel";
cat[0][8]="Reinforced Concrete Panel and Precast";
cat[0][9]="Unreinforced Masonry or Stone or Earthen Walled";

//Category Transportation
cat[1] = new Array();
cat[1][0] ="Bridges (Conventional)";
cat[1][1] ="Bridges (Major Engineered, over 100m)";
cat[1][2] ="Tunnels";
cat[1][3] ="Railroad (Roadbed)";
cat[1][4] ="Highway (Roadbed)";
cat[1][5] ="Runways";
cat[1][6] ="Waterfront Structures";
cat[1][7] ="Vehicles (Trains, Trucks, Airplanes, etc.)";

//Category Utilities and Industry
cat[2] = new Array();
cat[2][0] ="Chimneys";
cat[2][1] ="Cranes";
cat[2][2] ="Conveyor Systems";
cat[2][3] ="Electrical T&D";
cat[2][4] ="Electrical Substations (>100kv)";
cat[2][5] ="Towers (non-electrical T&D)";
cat[2][6] ="Tanks Underground";
cat[2][7] ="Tanks and Basins on Ground";
cat[2][8] ="Tanks Elevated";
cat[2][9] ="Equipment (Electrical)";
cat[2][10] ="Equipment (Mechanical)";
cat[2][11] ="Equipment (Other)";
cat[2][12] ="Treatment Plants and Process Facilities";
cat[2][13] ="Pipelines (Ordinary soil)";

//Assigned ID to each class in ATC-13
catID[0] = new Array();
catID[0][0] = "B1";
catID[0][1] = "B2";
catID[0][2] = "B3";
catID[0][3] = "B4";
catID[0][4] = "B5";
catID[0][5] = "B6";
catID[0][6] = "B7";
catID[0][7] = "B8";
catID[0][8] = "B9";
catID[0][9] = "B10";

catID[1] = new Array();
catID[1][0] = "T1"; 
catID[1][1] = "T2";
catID[1][2] = "T3"; 
catID[1][3] = "T4"; 
catID[1][4] = "T5"; 
catID[1][5] = "T6"; 
catID[1][6] = "T7";
catID[1][7] = "T8"; 

catID[2] = new Array();
catID[2][0] = "U1";
catID[2][1] = "U2";
catID[2][2] = "U3";
catID[2][3] = "U4";
catID[2][4] = "U5";
catID[2][5] = "U6";
catID[2][6] = "U7";
catID[2][7] = "U8";
catID[2][8] = "U9";
catID[2][9] = "U10";
catID[2][10] = "U11";
catID[2][11] = "U12";
catID[2][12] = "U13";
catID[2][13] = "U14";

var nn = (document.layers)?1:0;
var ie = (document.all)?1:0;
var dom= (!document.all && document.getElementById)?1:0;

function change_option() {
  var sel_str1 = "<select name='sel_2' ; onchange = 'textchange()'>";
  var cat_m = document.mpsel.sel_1.options.length;
  for(d=0;d<cat_m;d++){
     if (document.mpsel.sel_1.options[d].selected == true){
        var t_max = cat[d].length;
        for(c=0;c<t_max;c++){
           sel_str1 += "<option value="+catID[d][c]+">"+cat[d][c]+"\n";
        }
        sel_str1 += "</select>\n";
        if(ie==1){
          window["sel2"].innerHTML = sel_str1;
        }else if(nn==1){
          window["sel2"].innerHTML = sel_str1;
//          document.layers["sel2"].document.open();
//          document.layers["sel2"].document.write(sel_str1);
//          document.layers["sel2"].document.close();
        }else if(dom==1){
          document.getElementById("sel2").innerHTML = sel_str1;
        }
      } 
   }   
}  

function textchange(){
  var text_m1 = document.mpsel.sel_1.options.length;
  var text_m = document.mpsel.sel_2.options.length;
  for(g=0;g<text_m1;g++){
    if(document.mpsel.sel_1.options[g].selected == true){
      for(h=0;h<text_m;h++){
        if(document.mpsel.sel_2.options[h].selected == true){
	  //var textstr =  "<iframe src=./processdb/display_textdata.php?id=" + catID[g][h] + " name=DispData frameborder=0 height=500 width=860></iframe>";
         var textstr=  "./processdb/display_textdata.php?id=" + catID[g][h];
        }
        if(ie==1){
          //window["textinfo"].innerHTML = textstr;
	  parent.DispData.location.href=textstr;
        }else if(nn==1){
//        document.layers["textinfo"].document.open();
//        document.layers["textinfo"].document.write(textstr);
//        document.layers["textinfo"].document.close();
        }else if(dom==1){
          parent.DispData.location.href=textstr;
          //document.getElementById("textinfo").innerHTML = textstr;
        }
      }
    } 
  }
}

function setoption(){
  var str ="<select name='sel_1' onchange='change_option()'>";
  var first_m = firstcat.length;
  for(i=0;i<first_m;i++){ 
    str += "<option value="+firstcat[i]+">"+firstcat[i]+"\n";
  }
  str += "</select>\n";
  if(ie==1){
    window["sel1"].innerHTML = str;
  }else if(nn==1){
//  document.layers["sel1"].document.open();
//  document.layers["sel1"].document.write(str);
//  document.layers["sel1"].document.close();
  }else if(dom==1){
    document.getElementById("sel1").innerHTML = str;
  }
}
