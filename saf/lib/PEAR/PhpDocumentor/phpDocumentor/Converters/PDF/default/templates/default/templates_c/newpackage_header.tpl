<?php /* Smarty version 2.5.0, created on 2003-07-27 00:24:14
         compiled from newpackage_header.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'newpackage_header.tpl', 3, false),)); ?><newpage />
<?php if ($this->_tpl_vars['ppage']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("packagepage.tpl", array('package' => $this->_tpl_vars['package'],'plink' => $this->_smarty_vars['capture']['plink'],'ppage' => $this->_tpl_vars['ppage']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
<text size="26" justification="centre">Package <?php echo $this->_tpl_vars['package']; ?>
 <?php if ($this->_tpl_vars['isclass']): ?>Classes<?php else: ?>Procedural Elements<?php endif; ?><C:rf:1<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['classeslink']); ?>
>


</text>