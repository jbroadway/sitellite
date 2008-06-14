<?php /* Smarty version 2.5.0, created on 2003-07-27 19:40:57
         compiled from index.tpl */ ?>
<pdffunction:ezInsertMode arg="0" />
<newpage />
<text size="26" justification="centre"><C:rf:1Index>Index
</text>
<?php if (count((array)$this->_tpl_vars['indexcontents'])):
    foreach ((array)$this->_tpl_vars['indexcontents'] as $this->_tpl_vars['letter'] => $this->_tpl_vars['contents']):
?>
<text size="26"><C:IndexLetter:<?php echo $this->_tpl_vars['letter']; ?>
></text>
<?php if (count((array)$this->_tpl_vars['contents'])):
    foreach ((array)$this->_tpl_vars['contents'] as $this->_tpl_vars['arr']):
?>
<text size="11" aright="520"><c:ilink:toc<?php echo $this->_tpl_vars['arr'][3]; ?>
><?php echo $this->_tpl_vars['arr'][0]; ?>
</c:ilink><C:dots:4<?php echo $this->_tpl_vars['arr'][2]; ?>
></text>
<?php if ($this->_tpl_vars['arr'][1]): ?>
<text size="11" left="50"><i><?php echo $this->_tpl_vars['arr'][1]; ?>
</i></text>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php endforeach; endif; ?>
