<?php /* Smarty version 2.5.0, created on 2003-04-23 16:32:58
         compiled from tutorial_toc.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'assign', 'tutorial_toc.tpl', 6, false),)); ?><?php if (count ( $this->_tpl_vars['toc'] )): ?>
<h1 align="center">Table of Contents</h1>
<ul>
<?php if (isset($this->_sections['toc'])) unset($this->_sections['toc']);
$this->_sections['toc']['name'] = 'toc';
$this->_sections['toc']['loop'] = is_array($this->_tpl_vars['toc']) ? count($this->_tpl_vars['toc']) : max(0, (int)$this->_tpl_vars['toc']);
$this->_sections['toc']['show'] = true;
$this->_sections['toc']['max'] = $this->_sections['toc']['loop'];
$this->_sections['toc']['step'] = 1;
$this->_sections['toc']['start'] = $this->_sections['toc']['step'] > 0 ? 0 : $this->_sections['toc']['loop']-1;
if ($this->_sections['toc']['show']) {
    $this->_sections['toc']['total'] = $this->_sections['toc']['loop'];
    if ($this->_sections['toc']['total'] == 0)
        $this->_sections['toc']['show'] = false;
} else
    $this->_sections['toc']['total'] = 0;
if ($this->_sections['toc']['show']):

            for ($this->_sections['toc']['index'] = $this->_sections['toc']['start'], $this->_sections['toc']['iteration'] = 1;
                 $this->_sections['toc']['iteration'] <= $this->_sections['toc']['total'];
                 $this->_sections['toc']['index'] += $this->_sections['toc']['step'], $this->_sections['toc']['iteration']++):
$this->_sections['toc']['rownum'] = $this->_sections['toc']['iteration'];
$this->_sections['toc']['index_prev'] = $this->_sections['toc']['index'] - $this->_sections['toc']['step'];
$this->_sections['toc']['index_next'] = $this->_sections['toc']['index'] + $this->_sections['toc']['step'];
$this->_sections['toc']['first']      = ($this->_sections['toc']['iteration'] == 1);
$this->_sections['toc']['last']       = ($this->_sections['toc']['iteration'] == $this->_sections['toc']['total']);
?>
<?php if ($this->_tpl_vars['toc'][$this->_sections['toc']['index']]['tagname'] == 'refsect1'): ?>
<?php echo smarty_function_assign(array('var' => 'context','value' => 'refsect1'), $this) ; ?>

<?php echo $this->_tpl_vars['toc'][$this->_sections['toc']['index']]['link']; ?>
<br />
<?php endif; ?>
<?php if ($this->_tpl_vars['toc'][$this->_sections['toc']['index']]['tagname'] == 'refsect2'): ?>
<?php echo smarty_function_assign(array('var' => 'context','value' => 'refsect2'), $this) ; ?>

&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['toc'][$this->_sections['toc']['index']]['link']; ?>
<br />
<?php endif; ?>
<?php if ($this->_tpl_vars['toc'][$this->_sections['toc']['index']]['tagname'] == 'refsect3'): ?>
<?php echo smarty_function_assign(array('var' => 'context','value' => 'refsect3'), $this) ; ?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['toc'][$this->_sections['toc']['index']]['link']; ?>
<br />
<?php endif; ?>
<?php if ($this->_tpl_vars['toc'][$this->_sections['toc']['index']]['tagname'] == 'table'): ?>
<?php if ($this->_tpl_vars['context'] == 'refsect2'): ?>&nbsp;&nbsp;&nbsp;<?php endif; ?>
<?php if ($this->_tpl_vars['context'] == 'refsect3'): ?>&nbsp;&nbsp;&nbsp;<?php endif; ?>
Table: <?php echo $this->_tpl_vars['toc'][$this->_sections['toc']['index']]['link']; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['toc'][$this->_sections['toc']['index']]['tagname'] == 'example'): ?>
<?php if ($this->_tpl_vars['context'] == 'refsect2'): ?>&nbsp;&nbsp;&nbsp;<?php endif; ?>
<?php if ($this->_tpl_vars['context'] == 'refsect3'): ?>&nbsp;&nbsp;&nbsp;<?php endif; ?>
Table: <?php echo $this->_tpl_vars['toc'][$this->_sections['toc']['index']]['link']; ?>

<?php endif; ?>
<?php endfor; endif; ?>
</ul>
<?php endif; ?>