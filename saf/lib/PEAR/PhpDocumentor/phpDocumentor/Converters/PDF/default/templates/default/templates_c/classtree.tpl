<?php /* Smarty version 2.5.0, created on 2003-04-23 18:11:46
         compiled from classtree.tpl */ ?>

<text size="26" justification="centre"><C:rf:2Appendix A - Class Trees>Appendix A - Class Trees
</text>
<?php if (isset($this->_sections['classtrees'])) unset($this->_sections['classtrees']);
$this->_sections['classtrees']['name'] = 'classtrees';
$this->_sections['classtrees']['loop'] = is_array($this->_tpl_vars['trees']) ? count($this->_tpl_vars['trees']) : max(0, (int)$this->_tpl_vars['trees']);
$this->_sections['classtrees']['show'] = true;
$this->_sections['classtrees']['max'] = $this->_sections['classtrees']['loop'];
$this->_sections['classtrees']['step'] = 1;
$this->_sections['classtrees']['start'] = $this->_sections['classtrees']['step'] > 0 ? 0 : $this->_sections['classtrees']['loop']-1;
if ($this->_sections['classtrees']['show']) {
    $this->_sections['classtrees']['total'] = $this->_sections['classtrees']['loop'];
    if ($this->_sections['classtrees']['total'] == 0)
        $this->_sections['classtrees']['show'] = false;
} else
    $this->_sections['classtrees']['total'] = 0;
if ($this->_sections['classtrees']['show']):

            for ($this->_sections['classtrees']['index'] = $this->_sections['classtrees']['start'], $this->_sections['classtrees']['iteration'] = 1;
                 $this->_sections['classtrees']['iteration'] <= $this->_sections['classtrees']['total'];
                 $this->_sections['classtrees']['index'] += $this->_sections['classtrees']['step'], $this->_sections['classtrees']['iteration']++):
$this->_sections['classtrees']['rownum'] = $this->_sections['classtrees']['iteration'];
$this->_sections['classtrees']['index_prev'] = $this->_sections['classtrees']['index'] - $this->_sections['classtrees']['step'];
$this->_sections['classtrees']['index_next'] = $this->_sections['classtrees']['index'] + $this->_sections['classtrees']['step'];
$this->_sections['classtrees']['first']      = ($this->_sections['classtrees']['iteration'] == 1);
$this->_sections['classtrees']['last']       = ($this->_sections['classtrees']['iteration'] == $this->_sections['classtrees']['total']);
?>
<text size="16" justification="centre"><C:rf:3<?php echo $this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['package']; ?>
>Package <?php echo $this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['package']; ?>

</text>
<?php if (isset($this->_sections['trees'])) unset($this->_sections['trees']);
$this->_sections['trees']['name'] = 'trees';
$this->_sections['trees']['loop'] = is_array($this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['trees']) ? count($this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['trees']) : max(0, (int)$this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['trees']);
$this->_sections['trees']['show'] = true;
$this->_sections['trees']['max'] = $this->_sections['trees']['loop'];
$this->_sections['trees']['step'] = 1;
$this->_sections['trees']['start'] = $this->_sections['trees']['step'] > 0 ? 0 : $this->_sections['trees']['loop']-1;
if ($this->_sections['trees']['show']) {
    $this->_sections['trees']['total'] = $this->_sections['trees']['loop'];
    if ($this->_sections['trees']['total'] == 0)
        $this->_sections['trees']['show'] = false;
} else
    $this->_sections['trees']['total'] = 0;
if ($this->_sections['trees']['show']):

            for ($this->_sections['trees']['index'] = $this->_sections['trees']['start'], $this->_sections['trees']['iteration'] = 1;
                 $this->_sections['trees']['iteration'] <= $this->_sections['trees']['total'];
                 $this->_sections['trees']['index'] += $this->_sections['trees']['step'], $this->_sections['trees']['iteration']++):
$this->_sections['trees']['rownum'] = $this->_sections['trees']['iteration'];
$this->_sections['trees']['index_prev'] = $this->_sections['trees']['index'] - $this->_sections['trees']['step'];
$this->_sections['trees']['index_next'] = $this->_sections['trees']['index'] + $this->_sections['trees']['step'];
$this->_sections['trees']['first']      = ($this->_sections['trees']['iteration'] == 1);
$this->_sections['trees']['last']       = ($this->_sections['trees']['iteration'] == $this->_sections['trees']['total']);
?>
<text size="12"><C:IndexLetter:<?php echo $this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['trees'][$this->_sections['trees']['index']]['class']; ?>
>
<?php echo $this->_tpl_vars['trees'][$this->_sections['classtrees']['index']]['trees'][$this->_sections['trees']['index']]['class_tree']; ?>
</text>
<?php endfor; endif; ?>
<?php endfor; endif; ?>