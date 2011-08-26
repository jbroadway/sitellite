<?php

global $page, $cgi;

if (! session_admin ()) {
	return;
}

if (! isset ($parameters['collection'])) {
	$parameters['collection'] = 'sitellite_page';
}

if (session_role () == 'translator') {
	loader_import ('cms.Versioning.Rex');
	$rex = new Rex ($parameters['collection']);
	$parameters['type'] = intl_get ($rex->info['Collection']['singular']);
	if (empty ($parameters['id'])) {
		$parameters['id'] = $cgi->page;
	}
	echo template_simple ('buttons/translator.spt', $parameters);
	return;
}

if (session_is_resource ($parameters['collection']) && ! session_allowed ($parameters['collection'], 'rw', 'resource')) {
	return;
}

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($parameters['collection']);

if (! $rex->collection) {
	return;
}

if (isset ($parameters['object'])) {
	$obj = (array) $parameters['object'];
	if (isset ($obj['sitellite_status'])) {
		$parameters['status'] = $obj['sitellite_status'];
	}
	if (isset ($obj['sitellite_access'])) {
		$parameters['access'] = $obj['sitellite_access'];
	}
	if (isset ($obj['sitellite_team'])) {
		$parameters['team'] = $obj['sitellite_team'];
	}
	if (isset ($obj[$rex->key])) {
		$parameters['id'] = $obj[$rex->key];
	}
}

if ($parameters['collection'] == 'sitellite_page') {

	global $type, $page;

	if ($type != 'document') {
		$parameters['failed'] = 'type';
		$parameters['editable'] = false;
	} elseif (		! session_allowed ($page->sitellite_access, 'w', 'access')
	) {
		$parameters['failed'] = 'access';
		$parameters['editable'] = false;
	} elseif (
					! session_allowed ($page->sitellite_team, 'w', 'team')
	) {
		$parameters['failed'] = 'team';
		$parameters['editable'] = false;
	} else {
		if (			! session_allowed ($page->sitellite_status, 'w', 'status')
//					||	! session_allowed ($page->sitellite_team, 'w', 'team')
		) {
			$parameters['failed'] = 'status';
			$parameters['deletable'] = false;
			$parameters['editable'] = true;
		} else {
			$parameters['deletable'] = true;
			$parameters['editable'] = true;
		}
//		$parameters['editable'] = true;
	}

	$parameters['id'] = $page->id;

} else {

	if (! isset ($parameters['id'])) {
		$parameters['id'] = $parameters[$rex->key];
	}

	if (! $parameters['id']) {
		return;
	}

	if (
				! isset ($parameters['access'])
			||	! isset ($parameters['status'])
			||	! isset ($parameters['team'])
	) {
		$parameters['failed'] = 'data';
		$parameters['editable'] = false;
	} elseif (
				! session_allowed ($parameters['access'], 'w', 'access')
	) {
		$parameters['failed'] = 'access';
		$parameters['editable'] = false;
	} elseif (
				! session_allowed ($parameters['team'], 'w', 'team')
	) {
		$parameters['failed'] = 'team';
		$parameters['editable'] = false;
	} else {
		if (		! session_allowed ($parameters['status'], 'w', 'status')
//				||	! session_allowed ($parameters['team'], 'w', 'team')
		) {
			$parameters['failed'] = 'status';
			$parameters['deletable'] = false;
			$parameters['editable'] = true;
		} else {
			$parameters['deletable'] = true;
			$parameters['editable'] = true;
		}
//		$parameters['editable'] = true;
	}

}

if (! isset ($parameters['add'])) {
	$parameters['add'] = true;
} elseif ($parameters['add'] === 'false') {
	$parameters['add'] = false;
} elseif ($parameters['add'] === 'true') {
	$parameters['add'] = true;
}

if (! session_allowed ('add', 'rw', 'resource')) {
	$parameters['add'] = false;
}

if (! isset ($parameters['extras'])) {
	$parameters['extras'] = true;
} elseif ($parameters['extras'] === 'false') {
	$parameters['extras'] = false;
} elseif ($parameters['extras'] === 'true') {
	$parameters['extras'] = true;
}

if ($parameters['editable'] && $parameters['extras'] && $rex->info['Collection']['edit_extras']) {
	$parameters['extras'] = true;
} else {
	$parameters['extras'] = false;
}

if (! isset ($parameters['float'])) {
	$parameters['float'] = false;
} elseif ($parameters['float'] === 'false') {
	$parameters['float'] = false;
} elseif ($parameters['float'] === 'true') {
	$parameters['float'] = true;
}

if (! isset ($parameters['align'])) {
	$parameters['align'] = 'right';
} elseif ($parameters['align'] == 'false') {
	$parameters['align'] = false;
}

if (! isset ($parameters['inline'])) {
	$parameters['inline'] = false;
} else {
	$parameters['inline'] = true;
}

if (! isset ($parameters['return']) && $parameters['collection'] == 'sitellite_page') {
	$parameters['return'] = site_current ();
}

$parameters['return_v1'] = site_current ();

loader_import ('cms.Workflow.Lock');

lock_init ();

if (lock_exists ($parameters['collection'], $parameters['id'])) {
	$parameters['editable'] = false;
	$lock_info = lock_info ($parameters['collection'], $parameters['id']);
	$parameters['lock_owner'] = $lock_info->user;
	$parameters['lock_expires'] = $lock_info->expires;
	loader_import ('cms.Filters');
}

if (session_is_resource ('delete') && ! session_allowed ('delete', 'rw', 'resource')) {
	$parameters['deletable'] = false;
}

if ($rex->isVersioned && $parameters['editable']) { //session_allowed ('approved', 'w', 'status')) {
	$parameters['history'] = true;
} else {
	$parameters['history'] = false;
}

if ($parameters['collection'] == 'sitellite_page') {
	$c = $rex->getCurrent ($parameters['id']);
	if ($c->sitellite_status == 'draft' || $c->sitellite_status == 'pending') {
		//$parameters['status'] = $c->sitellite_status;
		$p = $rex->getSource ($parameters['id']);
		if ($p == $c) {
			$parameters['draft'] = false;
		} else {
			$parameters['draft'] = true;

			// access the XT register and "adjust" the body field by adding
			// a wrapper around it that allows the buttons.spt template to
			// toggle between approved and draft versions of it.
			global $_xte;
			$_xte->register['object']->{$rex->info['Collection']['body_field']} = '<div id="scm-approved">'
				. $_xte->register['object']->{$rex->info['Collection']['body_field']}
				. '</div><div id="scm-draft" style="display: none">'
				. template_parse_body ($c->{$rex->info['Collection']['body_field']})
				. '</div>';
		}
	} else {
		$parameters['draft'] = false;
	}
} elseif ($parameters['collection'] == 'sitellite_sidebar') {
	$c = $rex->getCurrent ($parameters['id']);
	if ($c->sitellite_status == 'draft' || $c->sitellite_status == 'pending') {
		//$parameters['status'] = $c->sitellite_status;
		$p = $rex->getSource ($parameters['id']);
		if ($p == $c) {
			$parameters['draft'] = false;
		} else {
			$parameters['draft'] = true;

			// access the XT register and "adjust" the body field by adding
			// a wrapper around it that allows the buttons.spt template to
			// toggle between approved and draft versions of it.
			global $scm_sidebar_body;
			$scm_sidebar_body = '<div id="scm-' . str_replace ('_', '-', $c->id) . '-approved">'
				. $scm_sidebar_body
				. '</div><div id="scm-' . str_replace ('_', '-', $c->id) . '-draft" style="display: none">'
				. template_parse_body ($c->{$rex->info['Collection']['body_field']})
				. '</div>';
			$parameters['sidebar_id'] = str_replace ('_', '-', $c->id);
		}
	} else {
		$parameters['draft'] = false;
	}
} else {
	$parameters['draft'] = false;
}

if (! $parameters['draft'] && isset ($c) && $c->sitellite_status != 'approved') {
	$parameters['status'] = $c->sitellite_status;
}

$parameters['type'] = intl_get ($rex->info['Collection']['singular']);

if (! function_exists ('cms_filter_parallel')) {
	function cms_filter_parallel ($id) {
		$goal = db_shift ('select goal from sitellite_parallel where page = ?', $id);
		if (empty ($goal)) {
			return 'Set Goal';
		}
		return 'View Stats';
	}
}

if ($parameters['collection'] == 'sitellite_page') {
	if ($parameters['id'] == 'index' || preg_match ('/-(app|action|form)$/', $parameters['id'])) {
		$parameters['below'] = false;
	} else {
		$parameters['below'] = true;
	}
} else {
	$parameters['below'] = false;
}

echo template_simple ('buttons.spt', $parameters);

echo loader_box ('cms/alert');

?>