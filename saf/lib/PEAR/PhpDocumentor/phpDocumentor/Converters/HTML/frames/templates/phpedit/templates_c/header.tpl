<?php /* Smarty version 2.5.0, created on 2003-04-23 16:12:45
         compiled from header.tpl */ ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="iso-8859-1"<?php echo '?>'; ?>

<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo $this->_tpl_vars['title']; ?>
</title>
  <link rel="stylesheet" href="<?php echo $this->_tpl_vars['subdir']; ?>
media/stylesheet.css" />
<?php if ($this->_tpl_vars['top2'] || $this->_tpl_vars['top3']): ?>
  <script src="<?php echo $this->_tpl_vars['subdir']; ?>
media/lib/classTree.js"></script>
<link id="webfx-tab-style-sheet" type="text/css" rel="stylesheet" href="<?php echo $this->_tpl_vars['subdir']; ?>
media/lib/tab.webfx.css" />
<script type="text/javascript" src="<?php echo $this->_tpl_vars['subdir']; ?>
media/lib/tabpane.js"></script>
<?php endif; ?>
<?php if ($this->_tpl_vars['top2']): ?>
  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'/>
<?php endif; ?>
<?php if ($this->_tpl_vars['top3'] || $this->_tpl_vars['top2']): ?>
  <script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['subdir']; ?>
media/lib/ua.js"></script>
<script language="javascript" type="text/javascript">
	var imgPlus = new Image();
	var imgMinus = new Image();
	imgPlus.src = "<?php echo $this->_tpl_vars['subdir']; ?>
media/images/plus.gif";
	imgMinus.src = "<?php echo $this->_tpl_vars['subdir']; ?>
media/images/minus.gif";
	
	function showNode(Node){
        switch(navigator.family){
        	case 'nn4':
        		// Nav 4.x code fork...
				var oTable = document.layers["span" + Node];
				var oImg = document.layers["img" + Node];
        		break;
        	case 'ie4':
        		// IE 4/5 code fork...
				var oTable = document.all["span" + Node];
				var oImg = document.all["img" + Node];
        		break;
        	case 'gecko':
        		// Standards Compliant code fork...
				var oTable = document.getElementById("span" + Node);
				var oImg = document.getElementById("img" + Node);
        		break;
        }
		oImg.src = imgMinus.src;
		oTable.style.display = "block";
	}
	
	function hideNode(Node){
        switch(navigator.family){
        	case 'nn4':
        		// Nav 4.x code fork...
				var oTable = document.layers["span" + Node];
				var oImg = document.layers["img" + Node];
        		break;
        	case 'ie4':
        		// IE 4/5 code fork...
				var oTable = document.all["span" + Node];
				var oImg = document.all["img" + Node];
        		break;
        	case 'gecko':
        		// Standards Compliant code fork...
				var oTable = document.getElementById("span" + Node);
				var oImg = document.getElementById("img" + Node);
        		break;
        }
		oImg.src = imgPlus.src;
		oTable.style.display = "none";
	}
	
	function nodeIsVisible(Node){
        switch(navigator.family){
        	case 'nn4':
        		// Nav 4.x code fork...
				var oTable = document.layers["span" + Node];
        		break;
        	case 'ie4':
        		// IE 4/5 code fork...
				var oTable = document.all["span" + Node];
        		break;
        	case 'gecko':
        		// Standards Compliant code fork...
				var oTable = document.getElementById("span" + Node);
        		break;
        }
		return (oTable && oTable.style.display == "block");
	}
	
	function toggleNodeVisibility(Node){
		if (nodeIsVisible(Node)){
			hideNode(Node);
		}else{
			showNode(Node);
		}
	}
</script>
<?php endif; ?>
<!-- template designed by Julien Damon based on PHPEdit's generated templates, and tweaked by Greg Beaver -->
<body bgcolor="#ffffff" <?php if ($this->_tpl_vars['top2']): ?> topmargin="3" leftmargin="3" rightmargin="2" bottommargin="3"<?php endif; ?>>