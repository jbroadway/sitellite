<?php /* Smarty version 2.5.0, created on 2003-04-23 16:13:30
         compiled from top_frame.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('top1' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (count ( $this->_tpl_vars['ric'] )): ?>
<ul>
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
	<li><a href="<?php echo $this->_tpl_vars['ric'][$this->_sections['ric']['index']]['file']; ?>
" target="right"><?php echo $this->_tpl_vars['ric'][$this->_sections['ric']['index']]['name']; ?>
</a></li>
<?php endfor; endif; ?>
</ul>
<?php endif; ?>
<h1>Packages</h1>
<ul>
<?php if (isset($this->_sections['p'])) unset($this->_sections['p']);
$this->_sections['p']['name'] = 'p';
$this->_sections['p']['loop'] = is_array($this->_tpl_vars['packages']) ? count($this->_tpl_vars['packages']) : max(0, (int)$this->_tpl_vars['packages']);
$this->_sections['p']['show'] = true;
$this->_sections['p']['max'] = $this->_sections['p']['loop'];
$this->_sections['p']['step'] = 1;
$this->_sections['p']['start'] = $this->_sections['p']['step'] > 0 ? 0 : $this->_sections['p']['loop']-1;
if ($this->_sections['p']['show']) {
    $this->_sections['p']['total'] = $this->_sections['p']['loop'];
    if ($this->_sections['p']['total'] == 0)
        $this->_sections['p']['show'] = false;
} else
    $this->_sections['p']['total'] = 0;
if ($this->_sections['p']['show']):

            for ($this->_sections['p']['index'] = $this->_sections['p']['start'], $this->_sections['p']['iteration'] = 1;
                 $this->_sections['p']['iteration'] <= $this->_sections['p']['total'];
                 $this->_sections['p']['index'] += $this->_sections['p']['step'], $this->_sections['p']['iteration']++):
$this->_sections['p']['rownum'] = $this->_sections['p']['iteration'];
$this->_sections['p']['index_prev'] = $this->_sections['p']['index'] - $this->_sections['p']['step'];
$this->_sections['p']['index_next'] = $this->_sections['p']['index'] + $this->_sections['p']['step'];
$this->_sections['p']['first']      = ($this->_sections['p']['iteration'] == 1);
$this->_sections['p']['last']       = ($this->_sections['p']['iteration'] == $this->_sections['p']['total']);
?>
	<li><a class="package" href='<?php echo $this->_tpl_vars['packages'][$this->_sections['p']['index']]['link']; ?>
' target='left_bottom'><?php echo $this->_tpl_vars['packages'][$this->_sections['p']['index']]['title']; ?>
</a></li>
<?php endfor; endif; ?>
</ul>
</body>
</html>