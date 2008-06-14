<?php /* Smarty version 2.5.0, created on 2003-07-27 19:53:34
         compiled from page.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'page.tpl', 7, false),)); ?><?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
<?php $this->_smarty_vars['capture']['pagelink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
|||<?php echo $this->_tpl_vars['sdesc']; ?>
<?php $this->_smarty_vars['capture']['pageindex'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>Package <?php echo $this->_tpl_vars['package']; ?>
 Procedural Elements<?php $this->_smarty_vars['capture']['classeslink'] = ob_get_contents(); ob_end_clean(); ?>
<newpage />
<?php if ($this->_tpl_vars['includeheader']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("newpackage_header.tpl", array('isclass' => false));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
<pdffunction:addDestination arg="<?php echo $this->_tpl_vars['dest']; ?>
" arg="FitH" arg=$this->y />
<text size="18" justification="center"><?php echo $this->_tpl_vars['name']; ?>
<C:rf:2<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['pagelink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['pageindex']); ?>
></text>