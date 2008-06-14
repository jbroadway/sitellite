<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:39
         compiled from page.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('eltype' => 'Procedural file'));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<h3><SPAN class="type">File:</SPAN> <?php echo $this->_tpl_vars['source_location']; ?>
<HR>
</h3>
<?php if ($this->_tpl_vars['tutorial']): ?>
<div class="maintutorial">Main Tutorial: <?php echo $this->_tpl_vars['tutorial']; ?>
</div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('desc' => $this->_tpl_vars['desc'],'sdesc' => $this->_tpl_vars['sdesc'],'tags' => $this->_tpl_vars['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
Classes in this file:
<dl>
<?php if (isset($this->_sections['classes'])) unset($this->_sections['classes']);
$this->_sections['classes']['name'] = 'classes';
$this->_sections['classes']['loop'] = is_array($this->_tpl_vars['classes']) ? count($this->_tpl_vars['classes']) : max(0, (int)$this->_tpl_vars['classes']);
$this->_sections['classes']['show'] = true;
$this->_sections['classes']['max'] = $this->_sections['classes']['loop'];
$this->_sections['classes']['step'] = 1;
$this->_sections['classes']['start'] = $this->_sections['classes']['step'] > 0 ? 0 : $this->_sections['classes']['loop']-1;
if ($this->_sections['classes']['show']) {
    $this->_sections['classes']['total'] = $this->_sections['classes']['loop'];
    if ($this->_sections['classes']['total'] == 0)
        $this->_sections['classes']['show'] = false;
} else
    $this->_sections['classes']['total'] = 0;
if ($this->_sections['classes']['show']):

            for ($this->_sections['classes']['index'] = $this->_sections['classes']['start'], $this->_sections['classes']['iteration'] = 1;
                 $this->_sections['classes']['iteration'] <= $this->_sections['classes']['total'];
                 $this->_sections['classes']['index'] += $this->_sections['classes']['step'], $this->_sections['classes']['iteration']++):
$this->_sections['classes']['rownum'] = $this->_sections['classes']['iteration'];
$this->_sections['classes']['index_prev'] = $this->_sections['classes']['index'] - $this->_sections['classes']['step'];
$this->_sections['classes']['index_next'] = $this->_sections['classes']['index'] + $this->_sections['classes']['step'];
$this->_sections['classes']['first']      = ($this->_sections['classes']['iteration'] == 1);
$this->_sections['classes']['last']       = ($this->_sections['classes']['iteration'] == $this->_sections['classes']['total']);
?>
<dt><?php echo $this->_tpl_vars['classes'][$this->_sections['classes']['index']]['link']; ?>
</dt>
	<dd><?php echo $this->_tpl_vars['classes'][$this->_sections['classes']['index']]['sdesc']; ?>
</dd>
<?php endfor; endif; ?>
</dl>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("include.tpl", array('summary' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("global.tpl", array('summary' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("define.tpl", array('summary' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("function.tpl", array('summary' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("include.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("global.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("define.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("function.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</HTML>