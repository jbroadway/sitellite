<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:33
         compiled from basicindex.tpl */ ?>
<?php if (isset($this->_sections['letter'])) unset($this->_sections['letter']);
$this->_sections['letter']['name'] = 'letter';
$this->_sections['letter']['loop'] = is_array($this->_tpl_vars['letters']) ? count($this->_tpl_vars['letters']) : max(0, (int)$this->_tpl_vars['letters']);
$this->_sections['letter']['show'] = true;
$this->_sections['letter']['max'] = $this->_sections['letter']['loop'];
$this->_sections['letter']['step'] = 1;
$this->_sections['letter']['start'] = $this->_sections['letter']['step'] > 0 ? 0 : $this->_sections['letter']['loop']-1;
if ($this->_sections['letter']['show']) {
    $this->_sections['letter']['total'] = $this->_sections['letter']['loop'];
    if ($this->_sections['letter']['total'] == 0)
        $this->_sections['letter']['show'] = false;
} else
    $this->_sections['letter']['total'] = 0;
if ($this->_sections['letter']['show']):

            for ($this->_sections['letter']['index'] = $this->_sections['letter']['start'], $this->_sections['letter']['iteration'] = 1;
                 $this->_sections['letter']['iteration'] <= $this->_sections['letter']['total'];
                 $this->_sections['letter']['index'] += $this->_sections['letter']['step'], $this->_sections['letter']['iteration']++):
$this->_sections['letter']['rownum'] = $this->_sections['letter']['iteration'];
$this->_sections['letter']['index_prev'] = $this->_sections['letter']['index'] - $this->_sections['letter']['step'];
$this->_sections['letter']['index_next'] = $this->_sections['letter']['index'] + $this->_sections['letter']['step'];
$this->_sections['letter']['first']      = ($this->_sections['letter']['iteration'] == 1);
$this->_sections['letter']['last']       = ($this->_sections['letter']['iteration'] == $this->_sections['letter']['total']);
?>
	<a href="<?php echo $this->_tpl_vars['indexname']; ?>
.html#<?php echo $this->_tpl_vars['letters'][$this->_sections['letter']['index']]['letter']; ?>
"><?php echo $this->_tpl_vars['letters'][$this->_sections['letter']['index']]['letter']; ?>
</a>
<?php endfor; endif; ?>
<table>
<?php if (isset($this->_sections['index'])) unset($this->_sections['index']);
$this->_sections['index']['name'] = 'index';
$this->_sections['index']['loop'] = is_array($this->_tpl_vars['index']) ? count($this->_tpl_vars['index']) : max(0, (int)$this->_tpl_vars['index']);
$this->_sections['index']['show'] = true;
$this->_sections['index']['max'] = $this->_sections['index']['loop'];
$this->_sections['index']['step'] = 1;
$this->_sections['index']['start'] = $this->_sections['index']['step'] > 0 ? 0 : $this->_sections['index']['loop']-1;
if ($this->_sections['index']['show']) {
    $this->_sections['index']['total'] = $this->_sections['index']['loop'];
    if ($this->_sections['index']['total'] == 0)
        $this->_sections['index']['show'] = false;
} else
    $this->_sections['index']['total'] = 0;
if ($this->_sections['index']['show']):

            for ($this->_sections['index']['index'] = $this->_sections['index']['start'], $this->_sections['index']['iteration'] = 1;
                 $this->_sections['index']['iteration'] <= $this->_sections['index']['total'];
                 $this->_sections['index']['index'] += $this->_sections['index']['step'], $this->_sections['index']['iteration']++):
$this->_sections['index']['rownum'] = $this->_sections['index']['iteration'];
$this->_sections['index']['index_prev'] = $this->_sections['index']['index'] - $this->_sections['index']['step'];
$this->_sections['index']['index_next'] = $this->_sections['index']['index'] + $this->_sections['index']['step'];
$this->_sections['index']['first']      = ($this->_sections['index']['iteration'] == 1);
$this->_sections['index']['last']       = ($this->_sections['index']['iteration'] == $this->_sections['index']['total']);
?>
<tr><td colspan = "2"><a name="<?php echo $this->_tpl_vars['index'][$this->_sections['index']['index']]['letter']; ?>
">&nbsp; </a>
<a href="#top">top</a><br>
<TABLE CELLPADDING='3' CELLSPACING='0' WIDTH='100%' CLASS="border">
	<TR CLASS='TableHeadingColor'>
		<TD>
			<FONT SIZE='+2'><B><?php echo $this->_tpl_vars['index'][$this->_sections['index']['index']]['letter']; ?>
</B></FONT>
		</TD>
	</TR>
</TABLE>
</td></tr>
	<?php if (isset($this->_sections['contents'])) unset($this->_sections['contents']);
$this->_sections['contents']['name'] = 'contents';
$this->_sections['contents']['loop'] = is_array($this->_tpl_vars['index'][$this->_sections['index']['index']]['index']) ? count($this->_tpl_vars['index'][$this->_sections['index']['index']]['index']) : max(0, (int)$this->_tpl_vars['index'][$this->_sections['index']['index']]['index']);
$this->_sections['contents']['show'] = true;
$this->_sections['contents']['max'] = $this->_sections['contents']['loop'];
$this->_sections['contents']['step'] = 1;
$this->_sections['contents']['start'] = $this->_sections['contents']['step'] > 0 ? 0 : $this->_sections['contents']['loop']-1;
if ($this->_sections['contents']['show']) {
    $this->_sections['contents']['total'] = $this->_sections['contents']['loop'];
    if ($this->_sections['contents']['total'] == 0)
        $this->_sections['contents']['show'] = false;
} else
    $this->_sections['contents']['total'] = 0;
if ($this->_sections['contents']['show']):

            for ($this->_sections['contents']['index'] = $this->_sections['contents']['start'], $this->_sections['contents']['iteration'] = 1;
                 $this->_sections['contents']['iteration'] <= $this->_sections['contents']['total'];
                 $this->_sections['contents']['index'] += $this->_sections['contents']['step'], $this->_sections['contents']['iteration']++):
$this->_sections['contents']['rownum'] = $this->_sections['contents']['iteration'];
$this->_sections['contents']['index_prev'] = $this->_sections['contents']['index'] - $this->_sections['contents']['step'];
$this->_sections['contents']['index_next'] = $this->_sections['contents']['index'] + $this->_sections['contents']['step'];
$this->_sections['contents']['first']      = ($this->_sections['contents']['iteration'] == 1);
$this->_sections['contents']['last']       = ($this->_sections['contents']['iteration'] == $this->_sections['contents']['total']);
?>
	<tr><td><b><?php echo $this->_tpl_vars['index'][$this->_sections['index']['index']]['index'][$this->_sections['contents']['index']]['name']; ?>
</b></td><td width="100%" align="left" valign="top"><?php echo $this->_tpl_vars['index'][$this->_sections['index']['index']]['index'][$this->_sections['contents']['index']]['listing']; ?>
</td></tr>
	<?php endfor; endif; ?>
<?php endfor; endif; ?>
</table>
