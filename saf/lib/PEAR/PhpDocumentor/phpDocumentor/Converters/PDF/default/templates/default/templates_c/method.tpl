<?php /* Smarty version 2.5.0, created on 2003-07-27 19:52:48
         compiled from method.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'method.tpl', 4, false),)); ?><?php ob_start(); ?><?php if ($this->_tpl_vars['constructor']): ?>Constructor <?php else: ?>Method <?php endif; ?><?php echo $this->_tpl_vars['intricatefunctioncall']['name']; ?>
<?php $this->_smarty_vars['capture']['mlink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php if ($this->_tpl_vars['constructor']): ?>constructor <?php endif; ?><?php echo $this->_tpl_vars['class']; ?>
::<?php echo $this->_tpl_vars['intricatefunctioncall']['name']; ?>
()|||<?php echo $this->_tpl_vars['sdesc']; ?>
<?php $this->_smarty_vars['capture']['mindex'] = ob_get_contents(); ob_end_clean(); ?>
<pdffunction:addDestination arg="<?php echo $this->_tpl_vars['dest']; ?>
" arg="FitH" arg=$this->y />
<text size="10" justification="left"><?php if ($this->_tpl_vars['constructor']): ?>Constructor <?php else: ?><?php endif; ?><i><?php echo $this->_tpl_vars['return']; ?>
</i> function <?php echo $this->_tpl_vars['class']; ?>
::<?php echo $this->_tpl_vars['intricatefunctioncall']['name']; ?>
(<?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
$this->_sections['params']['name'] = 'params';
$this->_sections['params']['loop'] = is_array($this->_tpl_vars['intricatefunctioncall']['params']) ? count($this->_tpl_vars['intricatefunctioncall']['params']) : max(0, (int)$this->_tpl_vars['intricatefunctioncall']['params']);
$this->_sections['params']['show'] = true;
$this->_sections['params']['max'] = $this->_sections['params']['loop'];
$this->_sections['params']['step'] = 1;
$this->_sections['params']['start'] = $this->_sections['params']['step'] > 0 ? 0 : $this->_sections['params']['loop']-1;
if ($this->_sections['params']['show']) {
    $this->_sections['params']['total'] = $this->_sections['params']['loop'];
    if ($this->_sections['params']['total'] == 0)
        $this->_sections['params']['show'] = false;
} else
    $this->_sections['params']['total'] = 0;
if ($this->_sections['params']['show']):

            for ($this->_sections['params']['index'] = $this->_sections['params']['start'], $this->_sections['params']['iteration'] = 1;
                 $this->_sections['params']['iteration'] <= $this->_sections['params']['total'];
                 $this->_sections['params']['index'] += $this->_sections['params']['step'], $this->_sections['params']['iteration']++):
$this->_sections['params']['rownum'] = $this->_sections['params']['iteration'];
$this->_sections['params']['index_prev'] = $this->_sections['params']['index'] - $this->_sections['params']['step'];
$this->_sections['params']['index_next'] = $this->_sections['params']['index'] + $this->_sections['params']['step'];
$this->_sections['params']['first']      = ($this->_sections['params']['iteration'] == 1);
$this->_sections['params']['last']       = ($this->_sections['params']['iteration'] == $this->_sections['params']['total']);
?><?php if ($this->_sections['params']['index'] > 0): ?>, <?php endif; ?><?php if ($this->_tpl_vars['intricatefunctioncall']['params'][$this->_sections['params']['index']]['default'] != ''): ?>[<?php endif; ?><?php echo $this->_tpl_vars['intricatefunctioncall']['params'][$this->_sections['params']['index']]['name']; ?>
<?php if ($this->_tpl_vars['intricatefunctioncall']['params'][$this->_sections['params']['index']]['default'] != ''): ?> = <?php echo $this->_tpl_vars['intricatefunctioncall']['params'][$this->_sections['params']['index']]['default']; ?>
]<?php endif; ?><?php endfor; endif; ?>) <i>[line <?php if ($this->_tpl_vars['slink']): ?><?php echo $this->_tpl_vars['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['linenumber']; ?>
<?php endif; ?>]</i><C:rf:3<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['mlink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['mindex']); ?>
></text>