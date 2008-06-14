<?php /* Smarty version 2.5.0, created on 2003-04-23 18:11:47
         compiled from source_loop.tpl */ ?>

<?php if (count((array)$this->_tpl_vars['source'])):
    foreach ((array)$this->_tpl_vars['source'] as $this->_tpl_vars['code']):
?>
<text size="16" justification="centre"><C:rf:3Package <?php echo $this->_tpl_vars['package']; ?>
>Package <?php echo $this->_tpl_vars['package']; ?>

</text>
<?php if (isset($this->_sections['code'])) unset($this->_sections['code']);
$this->_sections['code']['name'] = 'code';
$this->_sections['code']['loop'] = is_array($this->_tpl_vars['code']) ? count($this->_tpl_vars['code']) : max(0, (int)$this->_tpl_vars['code']);
$this->_sections['code']['show'] = true;
$this->_sections['code']['max'] = $this->_sections['code']['loop'];
$this->_sections['code']['step'] = 1;
$this->_sections['code']['start'] = $this->_sections['code']['step'] > 0 ? 0 : $this->_sections['code']['loop']-1;
if ($this->_sections['code']['show']) {
    $this->_sections['code']['total'] = $this->_sections['code']['loop'];
    if ($this->_sections['code']['total'] == 0)
        $this->_sections['code']['show'] = false;
} else
    $this->_sections['code']['total'] = 0;
if ($this->_sections['code']['show']):

            for ($this->_sections['code']['index'] = $this->_sections['code']['start'], $this->_sections['code']['iteration'] = 1;
                 $this->_sections['code']['iteration'] <= $this->_sections['code']['total'];
                 $this->_sections['code']['index'] += $this->_sections['code']['step'], $this->_sections['code']['iteration']++):
$this->_sections['code']['rownum'] = $this->_sections['code']['iteration'];
$this->_sections['code']['index_prev'] = $this->_sections['code']['index'] - $this->_sections['code']['step'];
$this->_sections['code']['index_next'] = $this->_sections['code']['index'] + $this->_sections['code']['step'];
$this->_sections['code']['first']      = ($this->_sections['code']['iteration'] == 1);
$this->_sections['code']['last']       = ($this->_sections['code']['iteration'] == $this->_sections['code']['total']);
?>
<?php echo $this->_tpl_vars['code'][$this->_sections['code']['index']]; ?>

<?php endfor; endif; ?>
<?php endforeach; endif; ?>