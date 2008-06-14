<?php /* Smarty version 2.5.0, created on 2003-07-27 20:37:21
         compiled from class.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'class.tpl', 12, false),)); ?><?php ob_start(); ?>Class <?php echo $this->_tpl_vars['name']; ?>
<?php $this->_smarty_vars['capture']['clink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
|||<?php echo $this->_tpl_vars['sdesc']; ?>
<?php $this->_smarty_vars['capture']['cindex'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>Package <?php echo $this->_tpl_vars['package']; ?>
 Classes<?php $this->_smarty_vars['capture']['classeslink'] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['plink']): ?><?php ob_start(); ?>Package <?php echo $this->_tpl_vars['package']; ?>
<?php $this->_smarty_vars['capture']['plink'] = ob_get_contents(); ob_end_clean(); ?><?php endif; ?>
<?php if ($this->_tpl_vars['includeheader']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("newpackage_header.tpl", array('isclass' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
<text size="11">



</text>
<pdffunction:addDestination arg="<?php echo $this->_tpl_vars['dest']; ?>
" arg="FitH" arg=$this->y />
<text size="20" justification="centre">Class <?php echo $this->_tpl_vars['name']; ?>
 <i></text><text size="11" justification="centre">[line <?php if ($this->_tpl_vars['slink']): ?><?php echo $this->_tpl_vars['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['linenumber']; ?>
<?php endif; ?>]</i><C:rf:2<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['clink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['cindex']); ?>
></text>