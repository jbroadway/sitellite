<?php /* Smarty version 2.5.0, created on 2003-07-27 05:12:40
         compiled from include.tpl */ ?>
<text size="10" justification="left"><?php echo $this->_tpl_vars['name']; ?>
 <b><?php echo $this->_tpl_vars['value']; ?>
</b> <i>[line <?php if ($this->_tpl_vars['slink']): ?><?php echo $this->_tpl_vars['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['linenumber']; ?>
<?php endif; ?>]</i>

</text>