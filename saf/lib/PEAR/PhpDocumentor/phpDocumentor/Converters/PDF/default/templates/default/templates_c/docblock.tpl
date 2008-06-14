<?php /* Smarty version 2.5.0, created on 2003-07-27 05:12:40
         compiled from docblock.tpl */ ?>
<?php if ($this->_tpl_vars['sdesc']): ?><text size="12" justification="full" left="10"><C:indent:25><b><?php echo $this->_tpl_vars['sdesc']; ?>
</b>
<C:indent:-25><?php echo $this->_tpl_vars['desc']; ?>

</text><?php endif; ?>
<?php if ($this->_tpl_vars['tags']): ?>
<text size="10" left="15">
<C:indent:40>
<ul><?php if (isset($this->_sections['tags'])) unset($this->_sections['tags']);
$this->_sections['tags']['name'] = 'tags';
$this->_sections['tags']['loop'] = is_array($this->_tpl_vars['tags']) ? count($this->_tpl_vars['tags']) : max(0, (int)$this->_tpl_vars['tags']);
$this->_sections['tags']['show'] = true;
$this->_sections['tags']['max'] = $this->_sections['tags']['loop'];
$this->_sections['tags']['step'] = 1;
$this->_sections['tags']['start'] = $this->_sections['tags']['step'] > 0 ? 0 : $this->_sections['tags']['loop']-1;
if ($this->_sections['tags']['show']) {
    $this->_sections['tags']['total'] = $this->_sections['tags']['loop'];
    if ($this->_sections['tags']['total'] == 0)
        $this->_sections['tags']['show'] = false;
} else
    $this->_sections['tags']['total'] = 0;
if ($this->_sections['tags']['show']):

            for ($this->_sections['tags']['index'] = $this->_sections['tags']['start'], $this->_sections['tags']['iteration'] = 1;
                 $this->_sections['tags']['iteration'] <= $this->_sections['tags']['total'];
                 $this->_sections['tags']['index'] += $this->_sections['tags']['step'], $this->_sections['tags']['iteration']++):
$this->_sections['tags']['rownum'] = $this->_sections['tags']['iteration'];
$this->_sections['tags']['index_prev'] = $this->_sections['tags']['index'] - $this->_sections['tags']['step'];
$this->_sections['tags']['index_next'] = $this->_sections['tags']['index'] + $this->_sections['tags']['step'];
$this->_sections['tags']['first']      = ($this->_sections['tags']['iteration'] == 1);
$this->_sections['tags']['last']       = ($this->_sections['tags']['iteration'] == $this->_sections['tags']['total']);
?><li><b><?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword']; ?>
</b> <?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['data']; ?>
</li>
<?php endfor; endif; ?></ul>
<C:indent:-40></text>
<?php endif; ?>