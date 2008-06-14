<?php /* Smarty version 2.5.0, created on 2003-04-23 14:46:51
         compiled from tutorial_nav.tpl */ ?>
<table class="tutorial-nav-box">
	<tr>
		<td style="width: 30%">
			<?php if ($this->_tpl_vars['prev']): ?>
				<a href="<?php echo $this->_tpl_vars['prev']; ?>
" class="nav-button">Previous</a>
			<?php else: ?>
				<span class="nav-button-disabled">Previous</span>
			<?php endif; ?>
		</td>
		<td style="text-align: center">
			<?php if ($this->_tpl_vars['up']): ?>
				<a href="<?php echo $this->_tpl_vars['up']; ?>
" class="nav-button">Up</a>
			<?php endif; ?>
		</td>
		<td style="text-align: right; width: 30%">
			<?php if ($this->_tpl_vars['next']): ?>
				<a href="<?php echo $this->_tpl_vars['next']; ?>
" class="nav-button">Next</a>
			<?php else: ?>
				<span class="nav-button-disabled">Next</span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td style="width: 30%">
			<?php if ($this->_tpl_vars['prevtitle']): ?>
				<span class="detail"><?php echo $this->_tpl_vars['prevtitle']; ?>
</span>
			<?php endif; ?>
		</td>
		<td style="text-align: center">
			<?php if ($this->_tpl_vars['uptitle']): ?>
				<span class="detail"><?php echo $this->_tpl_vars['uptitle']; ?>
</span>
			<?php endif; ?>
		</td>
		<td style="text-align: right; width: 30%">
			<?php if ($this->_tpl_vars['nexttitle']): ?>
				<span class="detail"><?php echo $this->_tpl_vars['nexttitle']; ?>
</span>
			<?php endif; ?>
		</td>
	</tr>
</table>
	