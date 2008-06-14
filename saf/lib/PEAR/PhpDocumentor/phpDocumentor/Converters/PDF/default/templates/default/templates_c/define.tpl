<?php /* Smarty version 2.5.0, created on 2003-07-29 03:09:07
         compiled from define.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'define.tpl', 4, false),)); ?><?php ob_start(); ?>Define <?php echo $this->_tpl_vars['name']; ?>
<?php $this->_smarty_vars['capture']['dlink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
|||<?php echo $this->_tpl_vars['sdesc']; ?>
<?php $this->_smarty_vars['capture']['dindex'] = ob_get_contents(); ob_end_clean(); ?>
<pdffunction:addDestination arg="<?php echo $this->_tpl_vars['dest']; ?>
" arg="FitH" arg=$this->y />
<text size="10" justification="left"><?php echo $this->_tpl_vars['name']; ?>
 = <?php echo $this->_tpl_vars['value']; ?>
 <i>[line <?php if ($this->_tpl_vars['slink']): ?><?php echo $this->_tpl_vars['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['linenumber']; ?>
<?php endif; ?>]</i><C:rf:3<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['dlink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['dindex']); ?>
></text>