<?php /* Smarty version 2.5.0, created on 2003-04-23 18:09:50
         compiled from tutorial.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'strip_tags', 'tutorial.tpl', 1, false),
array('modifier', 'urlencode', 'tutorial.tpl', 5, false),
array('modifier', 'rawurlencode', 'tutorial.tpl', 6, false),)); ?><?php ob_start(); ?><?php echo $this->_run_mod_handler('strip_tags', true, $this->_tpl_vars['title']); ?>
<?php $this->_smarty_vars['capture']['tlink'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php echo $this->_run_mod_handler('strip_tags', true, $this->_tpl_vars['title']); ?>
|||<?php $this->_smarty_vars['capture']['tindex'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>tutorial<?php echo $this->_tpl_vars['package']; ?>
<?php echo $this->_tpl_vars['subpackage']; ?>
<?php echo $this->_tpl_vars['element']->name; ?>
<?php $this->_smarty_vars['capture']['dest'] = ob_get_contents(); ob_end_clean(); ?>
<newpage />
<pdffunction:addDestination arg="<?php echo $this->_run_mod_handler('urlencode', true, $this->_smarty_vars['capture']['dest']); ?>
" arg="FitH" arg=$this->y />
<text size="26" justification="centre"><?php echo $this->_tpl_vars['title']; ?>
<C:rf:<?php if ($this->_tpl_vars['hasparent']): ?>3<?php elseif ($this->_tpl_vars['child']): ?>2<?php else: ?>1<?php endif; ?><?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['tlink']); ?>
><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['tindex']); ?>
>
</text><?php echo $this->_tpl_vars['contents']; ?>