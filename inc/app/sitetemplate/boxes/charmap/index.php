<?php

global $cgi;

?>
<style type="text/css">

.charmap {
	margin-top: 25px;
	margin-bottom: 25px;
	width: 240px;
	border: 0px none;
	border-left: 1px solid #aaa;
	border-top: 1px solid #aaa;
}

.charmap td {
	border: 0px none;
	border-right: 1px solid #aaa;
	border-bottom: 1px solid #aaa;
	background-color: #fff;
	width: 5%;
	padding: 5px;
	text-align: center;
	vertical-align: middle;
}

.charmap td:hover {
	background-color: #eee;
}

.charmap a {
	text-decoration: none;
	color: #333;
}

.charmap a:hover {
	text-decoration: none;
	color: #333;
}

</style>

<script language="javascript" type="text/javascript">

function charmap_select (n) {
	if (opener && ! opener.closed) {
		opener.document.sitetemplate_charmap_handler (n);
	} else {
		alert ('Hey, where did my parent go?');
	}
	window.close ();
	return false;
}

</script>

<table cellspacing="0" class="charmap">
	<tr>
		<td><a href="#" onclick="charmap_select ('34'); return false">&#34;</a></td>
		<td><a href="#" onclick="charmap_select ('35'); return false">&#35;</a></td>
		<td><a href="#" onclick="charmap_select ('36'); return false">&#36;</a></td>
		<td><a href="#" onclick="charmap_select ('37'); return false">&#37;</a></td>
		<td><a href="#" onclick="charmap_select ('38'); return false">&#38;</a></td>
		<td><a href="#" onclick="charmap_select ('39'); return false">&#39;</a></td>
		<td><a href="#" onclick="charmap_select ('40'); return false">&#40;</a></td>
		<td><a href="#" onclick="charmap_select ('41'); return false">&#41;</a></td>
		<td><a href="#" onclick="charmap_select ('42'); return false">&#42;</a></td>
		<td><a href="#" onclick="charmap_select ('43'); return false">&#43;</a></td>
		<td><a href="#" onclick="charmap_select ('44'); return false">&#44;</a></td>
		<td><a href="#" onclick="charmap_select ('45'); return false">&#45;</a></td>
		<td><a href="#" onclick="charmap_select ('46'); return false">&#46;</a></td>
		<td><a href="#" onclick="charmap_select ('47'); return false">&#47;</a></td>
		<td><a href="#" onclick="charmap_select ('58'); return false">&#58;</a></td>
		<td><a href="#" onclick="charmap_select ('59'); return false">&#59;</a></td>
		<td><a href="#" onclick="charmap_select ('60'); return false">&#60;</a></td>
		<td><a href="#" onclick="charmap_select ('61'); return false">&#61;</a></td>
		<td><a href="#" onclick="charmap_select ('62'); return false">&#62;</a></td>
		<td><a href="#" onclick="charmap_select ('63'); return false">&#63;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('64'); return false">&#64;</a></td>
		<td><a href="#" onclick="charmap_select ('123'); return false">&#123;</a></td>
		<td><a href="#" onclick="charmap_select ('124'); return false">&#124;</a></td>
		<td><a href="#" onclick="charmap_select ('125'); return false">&#125;</a></td>
		<td><a href="#" onclick="charmap_select ('126'); return false">&#126;</a></td>
		<td><a href="#" onclick="charmap_select ('127'); return false">&#127;</a></td>
		<td><a href="#" onclick="charmap_select ('128'); return false">&#128;</a></td>
		<td><a href="#" onclick="charmap_select ('129'); return false">&#129;</a></td>
		<td><a href="#" onclick="charmap_select ('130'); return false">&#130;</a></td>
		<td><a href="#" onclick="charmap_select ('131'); return false">&#131;</a></td>
		<td><a href="#" onclick="charmap_select ('132'); return false">&#132;</a></td>
		<td><a href="#" onclick="charmap_select ('133'); return false">&#133;</a></td>
		<td><a href="#" onclick="charmap_select ('134'); return false">&#134;</a></td>
		<td><a href="#" onclick="charmap_select ('135'); return false">&#135;</a></td>
		<td><a href="#" onclick="charmap_select ('136'); return false">&#136;</a></td>
		<td><a href="#" onclick="charmap_select ('137'); return false">&#137;</a></td>
		<td><a href="#" onclick="charmap_select ('138'); return false">&#138;</a></td>
		<td><a href="#" onclick="charmap_select ('139'); return false">&#139;</a></td>
		<td><a href="#" onclick="charmap_select ('140'); return false">&#140;</a></td>
		<td><a href="#" onclick="charmap_select ('141'); return false">&#141;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('142'); return false">&#142;</a></td>
		<td><a href="#" onclick="charmap_select ('143'); return false">&#143;</a></td>
		<td><a href="#" onclick="charmap_select ('144'); return false">&#144;</a></td>
		<td><a href="#" onclick="charmap_select ('145'); return false">&#145;</a></td>
		<td><a href="#" onclick="charmap_select ('146'); return false">&#146;</a></td>
		<td><a href="#" onclick="charmap_select ('147'); return false">&#147;</a></td>
		<td><a href="#" onclick="charmap_select ('148'); return false">&#148;</a></td>
		<td><a href="#" onclick="charmap_select ('149'); return false">&#149;</a></td>
		<td><a href="#" onclick="charmap_select ('150'); return false">&#150;</a></td>
		<td><a href="#" onclick="charmap_select ('151'); return false">&#151;</a></td>
		<td><a href="#" onclick="charmap_select ('152'); return false">&#152;</a></td>
		<td><a href="#" onclick="charmap_select ('153'); return false">&#153;</a></td>
		<td><a href="#" onclick="charmap_select ('154'); return false">&#154;</a></td>
		<td><a href="#" onclick="charmap_select ('155'); return false">&#155;</a></td>
		<td><a href="#" onclick="charmap_select ('156'); return false">&#156;</a></td>
		<td><a href="#" onclick="charmap_select ('157'); return false">&#157;</a></td>
		<td><a href="#" onclick="charmap_select ('158'); return false">&#158;</a></td>
		<td><a href="#" onclick="charmap_select ('159'); return false">&#159;</a></td>
		<td><a href="#" onclick="charmap_select ('161'); return false">&#161;</a></td>
		<td><a href="#" onclick="charmap_select ('162'); return false">&#162;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('163'); return false">&#163;</a></td>
		<td><a href="#" onclick="charmap_select ('164'); return false">&#164;</a></td>
		<td><a href="#" onclick="charmap_select ('165'); return false">&#165;</a></td>
		<td><a href="#" onclick="charmap_select ('166'); return false">&#166;</a></td>
		<td><a href="#" onclick="charmap_select ('167'); return false">&#167;</a></td>
		<td><a href="#" onclick="charmap_select ('168'); return false">&#168;</a></td>
		<td><a href="#" onclick="charmap_select ('169'); return false">&#169;</a></td>
		<td><a href="#" onclick="charmap_select ('170'); return false">&#170;</a></td>
		<td><a href="#" onclick="charmap_select ('171'); return false">&#171;</a></td>
		<td><a href="#" onclick="charmap_select ('172'); return false">&#172;</a></td>
		<td><a href="#" onclick="charmap_select ('174'); return false">&#174;</a></td>
		<td><a href="#" onclick="charmap_select ('175'); return false">&#175;</a></td>
		<td><a href="#" onclick="charmap_select ('176'); return false">&#176;</a></td>
		<td><a href="#" onclick="charmap_select ('177'); return false">&#177;</a></td>
		<td><a href="#" onclick="charmap_select ('178'); return false">&#178;</a></td>
		<td><a href="#" onclick="charmap_select ('179'); return false">&#179;</a></td>
		<td><a href="#" onclick="charmap_select ('180'); return false">&#180;</a></td>
		<td><a href="#" onclick="charmap_select ('181'); return false">&#181;</a></td>
		<td><a href="#" onclick="charmap_select ('182'); return false">&#182;</a></td>
		<td><a href="#" onclick="charmap_select ('183'); return false">&#183;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('184'); return false">&#184;</a></td>
		<td><a href="#" onclick="charmap_select ('185'); return false">&#185;</a></td>
		<td><a href="#" onclick="charmap_select ('186'); return false">&#186;</a></td>
		<td><a href="#" onclick="charmap_select ('187'); return false">&#187;</a></td>
		<td><a href="#" onclick="charmap_select ('188'); return false">&#188;</a></td>
		<td><a href="#" onclick="charmap_select ('189'); return false">&#189;</a></td>
		<td><a href="#" onclick="charmap_select ('190'); return false">&#190;</a></td>
		<td><a href="#" onclick="charmap_select ('191'); return false">&#191;</a></td>
		<td><a href="#" onclick="charmap_select ('192'); return false">&#192;</a></td>
		<td><a href="#" onclick="charmap_select ('193'); return false">&#193;</a></td>
		<td><a href="#" onclick="charmap_select ('194'); return false">&#194;</a></td>
		<td><a href="#" onclick="charmap_select ('195'); return false">&#195;</a></td>
		<td><a href="#" onclick="charmap_select ('196'); return false">&#196;</a></td>
		<td><a href="#" onclick="charmap_select ('197'); return false">&#197;</a></td>
		<td><a href="#" onclick="charmap_select ('198'); return false">&#198;</a></td>
		<td><a href="#" onclick="charmap_select ('199'); return false">&#199;</a></td>
		<td><a href="#" onclick="charmap_select ('200'); return false">&#200;</a></td>
		<td><a href="#" onclick="charmap_select ('201'); return false">&#201;</a></td>
		<td><a href="#" onclick="charmap_select ('202'); return false">&#202;</a></td>
		<td><a href="#" onclick="charmap_select ('203'); return false">&#203;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('204'); return false">&#204;</a></td>
		<td><a href="#" onclick="charmap_select ('205'); return false">&#205;</a></td>
		<td><a href="#" onclick="charmap_select ('206'); return false">&#206;</a></td>
		<td><a href="#" onclick="charmap_select ('207'); return false">&#207;</a></td>
		<td><a href="#" onclick="charmap_select ('208'); return false">&#208;</a></td>
		<td><a href="#" onclick="charmap_select ('209'); return false">&#209;</a></td>
		<td><a href="#" onclick="charmap_select ('210'); return false">&#210;</a></td>
		<td><a href="#" onclick="charmap_select ('211'); return false">&#211;</a></td>
		<td><a href="#" onclick="charmap_select ('212'); return false">&#212;</a></td>
		<td><a href="#" onclick="charmap_select ('213'); return false">&#213;</a></td>
		<td><a href="#" onclick="charmap_select ('214'); return false">&#214;</a></td>
		<td><a href="#" onclick="charmap_select ('215'); return false">&#215;</a></td>
		<td><a href="#" onclick="charmap_select ('216'); return false">&#216;</a></td>
		<td><a href="#" onclick="charmap_select ('217'); return false">&#217;</a></td>
		<td><a href="#" onclick="charmap_select ('218'); return false">&#218;</a></td>
		<td><a href="#" onclick="charmap_select ('219'); return false">&#219;</a></td>
		<td><a href="#" onclick="charmap_select ('220'); return false">&#220;</a></td>
		<td><a href="#" onclick="charmap_select ('221'); return false">&#221;</a></td>
		<td><a href="#" onclick="charmap_select ('222'); return false">&#222;</a></td>
		<td><a href="#" onclick="charmap_select ('223'); return false">&#223;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('224'); return false">&#224;</a></td>
		<td><a href="#" onclick="charmap_select ('225'); return false">&#225;</a></td>
		<td><a href="#" onclick="charmap_select ('226'); return false">&#226;</a></td>
		<td><a href="#" onclick="charmap_select ('227'); return false">&#227;</a></td>
		<td><a href="#" onclick="charmap_select ('228'); return false">&#228;</a></td>
		<td><a href="#" onclick="charmap_select ('229'); return false">&#229;</a></td>
		<td><a href="#" onclick="charmap_select ('230'); return false">&#230;</a></td>
		<td><a href="#" onclick="charmap_select ('231'); return false">&#231;</a></td>
		<td><a href="#" onclick="charmap_select ('232'); return false">&#232;</a></td>
		<td><a href="#" onclick="charmap_select ('233'); return false">&#233;</a></td>
		<td><a href="#" onclick="charmap_select ('234'); return false">&#234;</a></td>
		<td><a href="#" onclick="charmap_select ('235'); return false">&#235;</a></td>
		<td><a href="#" onclick="charmap_select ('236'); return false">&#236;</a></td>
		<td><a href="#" onclick="charmap_select ('237'); return false">&#237;</a></td>
		<td><a href="#" onclick="charmap_select ('238'); return false">&#238;</a></td>
		<td><a href="#" onclick="charmap_select ('239'); return false">&#239;</a></td>
		<td><a href="#" onclick="charmap_select ('240'); return false">&#240;</a></td>
		<td><a href="#" onclick="charmap_select ('241'); return false">&#241;</a></td>
		<td><a href="#" onclick="charmap_select ('242'); return false">&#242;</a></td>
		<td><a href="#" onclick="charmap_select ('243'); return false">&#243;</a></td>
	</tr>
	<tr>
		<td><a href="#" onclick="charmap_select ('244'); return false">&#244;</a></td>
		<td><a href="#" onclick="charmap_select ('245'); return false">&#245;</a></td>
		<td><a href="#" onclick="charmap_select ('246'); return false">&#246;</a></td>
		<td><a href="#" onclick="charmap_select ('247'); return false">&#247;</a></td>
		<td><a href="#" onclick="charmap_select ('248'); return false">&#248;</a></td>
		<td><a href="#" onclick="charmap_select ('249'); return false">&#249;</a></td>
		<td><a href="#" onclick="charmap_select ('250'); return false">&#250;</a></td>
		<td><a href="#" onclick="charmap_select ('251'); return false">&#251;</a></td>
		<td><a href="#" onclick="charmap_select ('252'); return false">&#252;</a></td>
		<td><a href="#" onclick="charmap_select ('253'); return false">&#253;</a></td>
		<td><a href="#" onclick="charmap_select ('254'); return false">&#254;</a></td>
		<td><a href="#" onclick="charmap_select ('255'); return false">&#255;</a></td>
	</tr>
</table>
