<?php /* Smarty version 2.5.0, created on 2003-04-23 16:13:32
         compiled from tutorial_tree.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'strip_tags', 'tutorial_tree.tpl', 1, false),)); ?>  var <?php echo $this->_tpl_vars['name']; ?>
tree = new WebFXTree<?php if ($this->_tpl_vars['subtree']): ?>Item<?php endif; ?>('<?php echo $this->_run_mod_handler('strip_tags', true, $this->_tpl_vars['main']['title']); ?>
','<?php echo $this->_tpl_vars['main']['link']; ?>
');
<?php if (! $this->_tpl_vars['subtree']): ?>  <?php echo $this->_tpl_vars['name']; ?>
tree.setBehavior('classic');
<?php endif; ?>  <?php echo $this->_tpl_vars['name']; ?>
tree.openIcon = 'media/images/msgInformation.gif';
  <?php echo $this->_tpl_vars['name']; ?>
tree.icon = 'media/images/<?php if ($this->_tpl_vars['subtree']): ?>msgInformation.gif<?php else: ?>FolderClosed.gif<?php endif; ?>';
<?php if ($this->_tpl_vars['kids']): ?>
<?php echo $this->_tpl_vars['kids']; ?>


<?php endif; ?><?php if ($this->_tpl_vars['subtree']): ?>  <?php echo $this->_tpl_vars['parent']; ?>
tree.add(<?php echo $this->_tpl_vars['name']; ?>
tree);
<?php else: ?>
  document.write(<?php echo $this->_tpl_vars['name']; ?>
tree);
<?php endif; ?>

