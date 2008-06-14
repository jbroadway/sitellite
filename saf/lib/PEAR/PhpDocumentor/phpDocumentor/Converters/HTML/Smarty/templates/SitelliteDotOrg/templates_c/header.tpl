<?php /* Smarty version 2.5.0, created on 2004-06-15 20:44:22
         compiled from header.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'assign', 'header.tpl', 99, false),
array('function', 'eval', 'header.tpl', 104, false),
array('modifier', 'capitalize', 'header.tpl', 139, false),)); ?><html>
<head>
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['subdir']; ?>
media/style.css">
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" height="48" width="100%">
  <tr>
    <td class="header_top">
      <span class="header_logo"><a href="http://www.sitellite.org/"><img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/sitellite.gif" alt="Sitellite Community Web Site" title="Sitellite Community Web Site" border="0" /></a></span>
      Sitellite Application Framework
    </td>
  </tr>
  <tr><td class="header_line"><img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/empty.png" width="1" height="1" border="0" alt=""  /></td></tr>
  <tr>
    <td class="header_menu">
          <!-- Package: <?php echo $this->_tpl_vars['package']; ?>

          &nbsp; &nbsp; &nbsp; &nbsp; -->
  		  <a href="<?php echo $this->_tpl_vars['subdir']; ?>
classtrees_<?php echo $this->_tpl_vars['package']; ?>
.html" class="menu">Class Tree</a>
  		  &nbsp; &nbsp; &nbsp; &nbsp;
		  <a href="<?php echo $this->_tpl_vars['subdir']; ?>
elementindex_<?php echo $this->_tpl_vars['package']; ?>
.html" class="menu">Index</a>
		  &nbsp; &nbsp; &nbsp; &nbsp;
		  <a href="<?php echo $this->_tpl_vars['subdir']; ?>
elementindex.html" class="menu">All Elements</a>
    </td>
  </tr>
  <tr><td class="header_line"><img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/empty.png" width="1" height="1" border="0" alt=""  /></td></tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2" class="breadcrumb">
      You are here: <a href="/docs/">SAF</a>
      <?php if ($this->_tpl_vars['hasel']): ?>
        <?php if ($this->_tpl_vars['package'] != 'saf'): ?>
          / <a href="/docs/li_<?php echo $this->_tpl_vars['package']; ?>
.html"><?php echo $this->_tpl_vars['package']; ?>
</a>
        <?php endif; ?>
        / <?php echo $this->_tpl_vars['class_name']; ?>

      <?php endif; ?>
      <?php if (! $this->_tpl_vars['hasel']): ?>
        <?php if ($this->_tpl_vars['package'] != 'saf'): ?>
          / <?php echo $this->_tpl_vars['package']; ?>

        <?php endif; ?>
      <?php endif; ?>
    </td>
  </tr>
  <tr valign="top">
    <td width="175" class="menu">
<?php if (count ( $this->_tpl_vars['ric'] )): ?>
	<div id="ric">
		<?php if (isset($this->_sections['ric'])) unset($this->_sections['ric']);
$this->_sections['ric']['name'] = 'ric';
$this->_sections['ric']['loop'] = is_array($this->_tpl_vars['ric']) ? count($this->_tpl_vars['ric']) : max(0, (int)$this->_tpl_vars['ric']);
$this->_sections['ric']['show'] = true;
$this->_sections['ric']['max'] = $this->_sections['ric']['loop'];
$this->_sections['ric']['step'] = 1;
$this->_sections['ric']['start'] = $this->_sections['ric']['step'] > 0 ? 0 : $this->_sections['ric']['loop']-1;
if ($this->_sections['ric']['show']) {
    $this->_sections['ric']['total'] = $this->_sections['ric']['loop'];
    if ($this->_sections['ric']['total'] == 0)
        $this->_sections['ric']['show'] = false;
} else
    $this->_sections['ric']['total'] = 0;
if ($this->_sections['ric']['show']):

            for ($this->_sections['ric']['index'] = $this->_sections['ric']['start'], $this->_sections['ric']['iteration'] = 1;
                 $this->_sections['ric']['iteration'] <= $this->_sections['ric']['total'];
                 $this->_sections['ric']['index'] += $this->_sections['ric']['step'], $this->_sections['ric']['iteration']++):
$this->_sections['ric']['rownum'] = $this->_sections['ric']['iteration'];
$this->_sections['ric']['index_prev'] = $this->_sections['ric']['index'] - $this->_sections['ric']['step'];
$this->_sections['ric']['index_next'] = $this->_sections['ric']['index'] + $this->_sections['ric']['step'];
$this->_sections['ric']['first']      = ($this->_sections['ric']['iteration'] == 1);
$this->_sections['ric']['last']       = ($this->_sections['ric']['iteration'] == $this->_sections['ric']['total']);
?>
			<p><a href="<?php echo $this->_tpl_vars['subdir']; ?>
<?php echo $this->_tpl_vars['ric'][$this->_sections['ric']['index']]['file']; ?>
"><?php echo $this->_tpl_vars['ric'][$this->_sections['ric']['index']]['name']; ?>
</a></p>
		<?php endfor; endif; ?>
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['hastodos']): ?>
	<div id="todolist">
			<p><a href="<?php echo $this->_tpl_vars['subdir']; ?>
<?php echo $this->_tpl_vars['todolink']; ?>
">Todo List</a></p>
	</div>
<?php endif; ?>

<!-- h2>Search</h2>
<form method="post" action="/index/sitesearch-app">
<p>
  <input type="text" name="query" />
  <input type="hidden" name="ctype" value="SAF Docs" />
  <input type="hidden" name="show_types" value="yes" />
  <input type="hidden" name="domains" value="www.sitellite.org" />
  <input type="hidden" name="show_domains" value="yes" />
  <input type="submit" value="Go" />
</p>
</form -->

      <h2>Packages</h2>
      <p><?php if (isset($this->_sections['packagelist'])) unset($this->_sections['packagelist']);
$this->_sections['packagelist']['name'] = 'packagelist';
$this->_sections['packagelist']['loop'] = is_array($this->_tpl_vars['packageindex']) ? count($this->_tpl_vars['packageindex']) : max(0, (int)$this->_tpl_vars['packageindex']);
$this->_sections['packagelist']['show'] = true;
$this->_sections['packagelist']['max'] = $this->_sections['packagelist']['loop'];
$this->_sections['packagelist']['step'] = 1;
$this->_sections['packagelist']['start'] = $this->_sections['packagelist']['step'] > 0 ? 0 : $this->_sections['packagelist']['loop']-1;
if ($this->_sections['packagelist']['show']) {
    $this->_sections['packagelist']['total'] = $this->_sections['packagelist']['loop'];
    if ($this->_sections['packagelist']['total'] == 0)
        $this->_sections['packagelist']['show'] = false;
} else
    $this->_sections['packagelist']['total'] = 0;
if ($this->_sections['packagelist']['show']):

            for ($this->_sections['packagelist']['index'] = $this->_sections['packagelist']['start'], $this->_sections['packagelist']['iteration'] = 1;
                 $this->_sections['packagelist']['iteration'] <= $this->_sections['packagelist']['total'];
                 $this->_sections['packagelist']['index'] += $this->_sections['packagelist']['step'], $this->_sections['packagelist']['iteration']++):
$this->_sections['packagelist']['rownum'] = $this->_sections['packagelist']['iteration'];
$this->_sections['packagelist']['index_prev'] = $this->_sections['packagelist']['index'] - $this->_sections['packagelist']['step'];
$this->_sections['packagelist']['index_next'] = $this->_sections['packagelist']['index'] + $this->_sections['packagelist']['step'];
$this->_sections['packagelist']['first']      = ($this->_sections['packagelist']['iteration'] == 1);
$this->_sections['packagelist']['last']       = ($this->_sections['packagelist']['iteration'] == $this->_sections['packagelist']['total']);
?>
        <a href="<?php echo $this->_tpl_vars['subdir']; ?>
<?php echo $this->_tpl_vars['packageindex'][$this->_sections['packagelist']['index']]['link']; ?>
"><?php echo $this->_tpl_vars['packageindex'][$this->_sections['packagelist']['index']]['title']; ?>
</a><br />
      <?php endfor; endif; ?></p>
<?php if ($this->_tpl_vars['tutorials']): ?>
		<h2>Tutorials/Manuals</h2>
		<p><?php if ($this->_tpl_vars['tutorials']['pkg']): ?>
			<strong>Package-level:</strong>
			<?php if (isset($this->_sections['ext'])) unset($this->_sections['ext']);
$this->_sections['ext']['name'] = 'ext';
$this->_sections['ext']['loop'] = is_array($this->_tpl_vars['tutorials']['pkg']) ? count($this->_tpl_vars['tutorials']['pkg']) : max(0, (int)$this->_tpl_vars['tutorials']['pkg']);
$this->_sections['ext']['show'] = true;
$this->_sections['ext']['max'] = $this->_sections['ext']['loop'];
$this->_sections['ext']['step'] = 1;
$this->_sections['ext']['start'] = $this->_sections['ext']['step'] > 0 ? 0 : $this->_sections['ext']['loop']-1;
if ($this->_sections['ext']['show']) {
    $this->_sections['ext']['total'] = $this->_sections['ext']['loop'];
    if ($this->_sections['ext']['total'] == 0)
        $this->_sections['ext']['show'] = false;
} else
    $this->_sections['ext']['total'] = 0;
if ($this->_sections['ext']['show']):

            for ($this->_sections['ext']['index'] = $this->_sections['ext']['start'], $this->_sections['ext']['iteration'] = 1;
                 $this->_sections['ext']['iteration'] <= $this->_sections['ext']['total'];
                 $this->_sections['ext']['index'] += $this->_sections['ext']['step'], $this->_sections['ext']['iteration']++):
$this->_sections['ext']['rownum'] = $this->_sections['ext']['iteration'];
$this->_sections['ext']['index_prev'] = $this->_sections['ext']['index'] - $this->_sections['ext']['step'];
$this->_sections['ext']['index_next'] = $this->_sections['ext']['index'] + $this->_sections['ext']['step'];
$this->_sections['ext']['first']      = ($this->_sections['ext']['iteration'] == 1);
$this->_sections['ext']['last']       = ($this->_sections['ext']['iteration'] == $this->_sections['ext']['total']);
?>
				<?php echo $this->_tpl_vars['tutorials']['pkg'][$this->_sections['ext']['index']]; ?>

			<?php endfor; endif; ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['tutorials']['cls']): ?>
			<strong>Class-level:</strong>
			<?php if (isset($this->_sections['ext'])) unset($this->_sections['ext']);
$this->_sections['ext']['name'] = 'ext';
$this->_sections['ext']['loop'] = is_array($this->_tpl_vars['tutorials']['cls']) ? count($this->_tpl_vars['tutorials']['cls']) : max(0, (int)$this->_tpl_vars['tutorials']['cls']);
$this->_sections['ext']['show'] = true;
$this->_sections['ext']['max'] = $this->_sections['ext']['loop'];
$this->_sections['ext']['step'] = 1;
$this->_sections['ext']['start'] = $this->_sections['ext']['step'] > 0 ? 0 : $this->_sections['ext']['loop']-1;
if ($this->_sections['ext']['show']) {
    $this->_sections['ext']['total'] = $this->_sections['ext']['loop'];
    if ($this->_sections['ext']['total'] == 0)
        $this->_sections['ext']['show'] = false;
} else
    $this->_sections['ext']['total'] = 0;
if ($this->_sections['ext']['show']):

            for ($this->_sections['ext']['index'] = $this->_sections['ext']['start'], $this->_sections['ext']['iteration'] = 1;
                 $this->_sections['ext']['iteration'] <= $this->_sections['ext']['total'];
                 $this->_sections['ext']['index'] += $this->_sections['ext']['step'], $this->_sections['ext']['iteration']++):
$this->_sections['ext']['rownum'] = $this->_sections['ext']['iteration'];
$this->_sections['ext']['index_prev'] = $this->_sections['ext']['index'] - $this->_sections['ext']['step'];
$this->_sections['ext']['index_next'] = $this->_sections['ext']['index'] + $this->_sections['ext']['step'];
$this->_sections['ext']['first']      = ($this->_sections['ext']['iteration'] == 1);
$this->_sections['ext']['last']       = ($this->_sections['ext']['iteration'] == $this->_sections['ext']['total']);
?>
				<?php echo $this->_tpl_vars['tutorials']['cls'][$this->_sections['ext']['index']]; ?>

			<?php endfor; endif; ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['tutorials']['proc']): ?>
			<strong>Procedural-level:</strong>
			<?php if (isset($this->_sections['ext'])) unset($this->_sections['ext']);
$this->_sections['ext']['name'] = 'ext';
$this->_sections['ext']['loop'] = is_array($this->_tpl_vars['tutorials']['proc']) ? count($this->_tpl_vars['tutorials']['proc']) : max(0, (int)$this->_tpl_vars['tutorials']['proc']);
$this->_sections['ext']['show'] = true;
$this->_sections['ext']['max'] = $this->_sections['ext']['loop'];
$this->_sections['ext']['step'] = 1;
$this->_sections['ext']['start'] = $this->_sections['ext']['step'] > 0 ? 0 : $this->_sections['ext']['loop']-1;
if ($this->_sections['ext']['show']) {
    $this->_sections['ext']['total'] = $this->_sections['ext']['loop'];
    if ($this->_sections['ext']['total'] == 0)
        $this->_sections['ext']['show'] = false;
} else
    $this->_sections['ext']['total'] = 0;
if ($this->_sections['ext']['show']):

            for ($this->_sections['ext']['index'] = $this->_sections['ext']['start'], $this->_sections['ext']['iteration'] = 1;
                 $this->_sections['ext']['iteration'] <= $this->_sections['ext']['total'];
                 $this->_sections['ext']['index'] += $this->_sections['ext']['step'], $this->_sections['ext']['iteration']++):
$this->_sections['ext']['rownum'] = $this->_sections['ext']['iteration'];
$this->_sections['ext']['index_prev'] = $this->_sections['ext']['index'] - $this->_sections['ext']['step'];
$this->_sections['ext']['index_next'] = $this->_sections['ext']['index'] + $this->_sections['ext']['step'];
$this->_sections['ext']['first']      = ($this->_sections['ext']['iteration'] == 1);
$this->_sections['ext']['last']       = ($this->_sections['ext']['iteration'] == $this->_sections['ext']['total']);
?>
				<?php echo $this->_tpl_vars['tutorials']['proc'][$this->_sections['ext']['index']]; ?>

			<?php endfor; endif; ?>
		<?php endif; ?></p>
<?php endif; ?>
      <?php if (! $this->_tpl_vars['noleftindex']): ?><?php echo smarty_function_assign(array('var' => 'noleftindex','value' => false), $this) ; ?>
<?php endif; ?>
      <?php if (! $this->_tpl_vars['noleftindex']): ?>

      <?php if ($this->_tpl_vars['compiledclassindex']): ?>
      <h2>Classes</h2>
      <p><?php echo smarty_function_eval(array('var' => $this->_tpl_vars['compiledclassindex']), $this) ; ?>
</p>
      <?php endif; ?>

      <?php if ($this->_tpl_vars['compiledfileindex']): ?>
      <h2>Files</h2>
      <p><?php echo smarty_function_eval(array('var' => $this->_tpl_vars['compiledfileindex']), $this) ; ?>
</p>
      <?php endif; ?>

      <?php endif; ?>
    </td>
    <td>
      <table cellpadding="10" cellspacing="0" width="100%" border="0"><tr><td valign="top">

<?php if (! $this->_tpl_vars['hasel']): ?><?php echo smarty_function_assign(array('var' => 'hasel','value' => false), $this) ; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['hasel']): ?>

<div style="float: right">
<script type="text/javascript"><!--
google_ad_client = "pub-0776261361164405";
google_alternate_ad_url = "http://www.sitellite.org/adsense/125x125.html";
google_ad_width = 125;
google_ad_height = 125;
google_ad_format = "125x125_as";
google_ad_channel ="5965840350";
google_color_border = "A9B7C4";
google_color_bg = "FFFFFF";
google_color_link = "000000";
google_color_url = "EE9911";
google_color_text = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

<h1><?php echo $this->_run_mod_handler('capitalize', true, $this->_tpl_vars['eltype']); ?>
: <?php echo $this->_tpl_vars['class_name']; ?>
</h1>
Source Location: <?php echo $this->_tpl_vars['source_location']; ?>
<br /><br clear="all" />
<?php endif; ?>