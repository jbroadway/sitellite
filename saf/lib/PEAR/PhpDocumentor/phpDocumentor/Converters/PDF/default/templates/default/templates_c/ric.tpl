<?php /* Smarty version 2.5.0, created on 2003-07-27 05:39:08
         compiled from ric.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'ric.tpl', 3, false),
array('modifier', 'htmlentities', 'ric.tpl', 7, false),)); ?><?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
<?php $this->_smarty_vars['capture']['tlink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
|||<?php $this->_smarty_vars['capture']['tindex'] = ob_get_contents(); ob_end_clean(); ?>
<text size="20" justification="centre"><C:rf:3<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['tlink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['tindex']); ?>
><?php echo $this->_tpl_vars['name']; ?>


</text>
<text size="10" justification="left">
<?php echo $this->_run_mod_handler('htmlentities', true, $this->_tpl_vars['contents']); ?>
</text>