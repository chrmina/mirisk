Print
  paper_mode 0
  paper a4
  paper_width 8.268000
  paper_height 11.693000
  paper_left 0.500000
  paper_right 0.500000
  paper_top 1.000000
  paper_bottom 1.000000
  do_scriptfile 0
  scriptfile 
  do_psfile 0
  psfile 
  do_pdffile 0
  pdffile 
  do_pngfile 0
  pngfile 
  pngresolution 100
  do_printer 0
  printer lpr
End
Group Population
  _check 1
  Vector more than 1 billion people
    _check 1
    map countries
    display_shape 1
    display_cat 0
    display_topo 0
    display_dir 0
    display_attr 0
    type_point 0
    type_line 0
    type_boundary 0
    type_centroid 0
    type_area 1
    type_face 0
    color #000000
    fcolor #ff0000
    _use_fcolor 1
    lcolor #000000
    icon basic/cross
    size 5
    field 1
    lfield 1
    attribute 
    xref left
    yref center
    lsize 8
    cat 
    where total > 1000000000
    _query_text 0
    _query_edit 0
    _use_where 1
    minreg 
    maxreg 
    _width 1
  End
  Vector more than 100,000,000 people
    _check 1
    map countries
    display_shape 1
    display_cat 0
    display_topo 0
    display_dir 0
    display_attr 0
    type_point 0
    type_line 0
    type_boundary 0
    type_centroid 0
    type_area 1
    type_face 0
    color #000000
    fcolor #ffff00
    _use_fcolor 1
    lcolor #000000
    icon basic/cross
    size 5
    field 1
    lfield 1
    attribute 
    xref left
    yref center
    lsize 8
    cat 
    where total < 1000000000 and total > 100000000
    _query_text 0
    _query_edit 0
    _use_where 1
    minreg 
    maxreg 
    _width 1
  End
  Vector more than 1 million people
    _check 1
    map countries
    display_shape 1
    display_cat 0
    display_topo 0
    display_dir 0
    display_attr 0
    type_point 0
    type_line 0
    type_boundary 0
    type_centroid 0
    type_area 1
    type_face 0
    color #000000
    fcolor #009900
    _use_fcolor 1
    lcolor #000000
    icon basic/cross
    size 5
    field 1
    lfield 1
    attribute 
    xref left
    yref center
    lsize 8
    cat 
    where total < 100000000 and total > 1000000
    _query_text 0
    _query_edit 0
    _use_where 1
    minreg 
    maxreg 
    _width 1
  End
  Vector less than 1 million people
    _check 1
    map countries
    display_shape 1
    display_cat 0
    display_topo 0
    display_dir 0
    display_attr 0
    type_point 0
    type_line 0
    type_boundary 0
    type_centroid 0
    type_area 1
    type_face 0
    color #000000
    fcolor #00ffff
    _use_fcolor 1
    lcolor #000000
    icon basic/cross
    size 5
    field 1
    lfield 1
    attribute 
    xref left
    yref center
    lsize 8
    cat 
    where total < 1000000
    _query_text 0
    _query_edit 0
    _use_where 1
    minreg 
    maxreg 
    _width 1
  End
End
