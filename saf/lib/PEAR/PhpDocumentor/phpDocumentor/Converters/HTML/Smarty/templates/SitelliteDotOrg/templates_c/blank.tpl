<?php /* Smarty version 2.5.0, created on 2004-06-15 20:11:53
         compiled from blank.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'eval', 'blank.tpl', 36, false),)); ?><div style="float: right">
<script type="text/javascript"><!--
google_ad_client = "pub-0776261361164405";
google_alternate_ad_url = "http://www.sitellite.org/adsense/120x600.html";
google_ad_width = 120;
google_ad_height = 600;
google_ad_format = "120x600_as";
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

<h1><?php echo $this->_tpl_vars['maintitle']; ?>
</h1>

<?php if ($this->_tpl_vars['package'] == 'saf'): ?>
  <h2>Packages</h2>
  <p>
    <?php if (isset($this->_sections['packagelist'])) unset($this->_sections['packagelist']);
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
    <?php endfor; endif; ?>
  </p>
<?php endif; ?>

<?php if ($this->_tpl_vars['package'] != 'saf'): ?>
  <p>Package: <?php echo $this->_tpl_vars['package']; ?>
</p>

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