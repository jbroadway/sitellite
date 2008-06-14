<?php /* Smarty version 2.5.0, created on 2003-04-23 13:36:21
         compiled from tutorial_tree.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'strip_tags', 'tutorial_tree.tpl', 1, false),)); ?><div><img class="tree-icon" src="<?php echo $this->_tpl_vars['subdir']; ?>
media/images/tutorial.png" alt="Tutorial"><a href="<?php echo $this->_tpl_vars['main']['link']; ?>
" target="right"><?php echo $this->_run_mod_handler('strip_tags', true, $this->_tpl_vars['main']['title']); ?>
</a></div>
<?php if ($this->_tpl_vars['haskids']): ?>
<div style="margin-left: 19px">
	<?php echo $this->_tpl_vars['kids']; ?>

</div>
<?php endif; ?>