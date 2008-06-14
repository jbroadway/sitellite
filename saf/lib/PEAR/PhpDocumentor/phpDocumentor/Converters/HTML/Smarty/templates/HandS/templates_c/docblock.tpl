<?php /* Smarty version 2.5.0, created on 2003-06-02 22:34:31
         compiled from docblock.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'docblock.tpl', 2, false),)); ?><?php if ($this->_tpl_vars['sdesc'] != ''): ?>
<p align="center" class="short-description"><strong><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['sdesc'], ''); ?>

</strong></p>
<?php endif; ?>
<?php if ($this->_tpl_vars['desc'] != ''): ?><span class="description"><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['desc'], ''); ?>
</span><?php endif; ?>