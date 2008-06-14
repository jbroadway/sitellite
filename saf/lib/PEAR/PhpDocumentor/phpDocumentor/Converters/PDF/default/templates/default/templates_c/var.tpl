<?php /* Smarty version 2.5.0, created on 2003-07-27 19:52:48
         compiled from var.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'var.tpl', 6, false),)); ?><?php ob_start(); ?>Var <?php echo $this->_tpl_vars['name']; ?>
<?php $this->_smarty_vars['capture']['vlink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['class']; ?>
::<?php echo $this->_tpl_vars['name']; ?>
|||<?php echo $this->_tpl_vars['sdesc']; ?>
<?php $this->_smarty_vars['capture']['vindex'] = ob_get_contents(); ob_end_clean(); ?>
<pdffunction:addDestination arg="<?php echo $this->_tpl_vars['dest']; ?>
" arg="FitH" arg=$this->y />
<text size="10" justification="left"><b><?php echo $this->_tpl_vars['class']; ?>
::<?php echo $this->_tpl_vars['name']; ?>
</b>
<C:indent:25>
<i><?php echo $this->_tpl_vars['type']; ?>
</i> = <?php echo $this->_tpl_vars['value']; ?>
 <i>[line <?php if ($this->_tpl_vars['slink']): ?><?php echo $this->_tpl_vars['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['linenumber']; ?>
<?php endif; ?>]</i><C:rf:3<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['vlink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['vindex']); ?>
>
<C:indent:-25></text>