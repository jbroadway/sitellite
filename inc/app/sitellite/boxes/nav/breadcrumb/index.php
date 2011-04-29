<?php

loader_box ('sitellite/nav/init');

global $menu, $page;

if ($page->id != 'index') {

	if (! empty ($parameters['caption'])) {
		$caption = $parameters['caption'] . ': ';
	} else {
		$caption = '';
	}

	if ($parameters['home_link'] == 'no') {
		$home = false;
	} elseif (! empty ($parameters['home_link'])) {
		$home = $parameters['home_link'];
	} else {
		$home = true;
	}

	if ($page->below_page && ! isset ($menu->{'items_' . $page->id})) {
		$menu->addItem ($page->id, $page->title, $page->below_page);
		$added = true;
	} else {
		$added = false;
	}

	if ($menu->{'items_' . $page->id}) {
		echo '<p class="breadcrumb">'
			. '<span class="caption">' . $caption . '</span>'
			. $menu->trail ($page->id, 'nav/breadcrumb/link.spt', $home, ' / ')
			. '</p>' . NEWLINEx2;
	} elseif (! empty ($page->title)) {
		echo '<p class="breadcrumb">'
			. '<span class="caption">' . $caption . '</span>'
			. template_simple ('nav/breadcrumb/link_home.spt', array ('id' => $home, 'title' => intl_get ('Home')))
			. ' / '
			. $page->title
			. '</p>' . NEWLINEx2;
	}

	if ($added) {
		unset ($menu->{'items_' . $page->id});
	}
}

?>