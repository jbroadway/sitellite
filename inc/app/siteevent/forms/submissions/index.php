<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #198 Allow for HTML mailing templates.

if (! appconf ('submissions')) {
	header ('Location: ' . site_prefix () . '/index/news-app');
	exit;
}

class SiteeventSubmissionsForm extends MailForm {
	function SiteeventSubmissionsForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteevent/forms/submissions/settings.php');

		page_title (intl_get ('Submit An Event'));

		$this->widgets['date']->setValue (date ('Y-m-d'));
		$this->widgets['loc_city']->setDefault (appconf ('default_city'));
		$this->widgets['loc_province']->setDefault (appconf ('default_province'));
		$this->widgets['loc_country']->setDefault (appconf ('default_country'));
	}

	function onSubmit ($vals) {
		// 1. prepare vals for insertion
		if ($vals['contact_url'] == 'http://') {
			$vals['contact_url'] = '';
		}
		if ($vals['loc_map'] == 'http://') {
			$vals['loc_map'] = '';
		}
		$vals['details'] = nl2br (wordwrap (htmlentities_compat ($vals['details']), 70, "\n", true));
		if (! $vals['public'] || empty ($vals['public'])) {
			$vals['public'] = 'no';
		}
		if (! $vals['media'] || empty ($vals['media'])) {
			$vals['media'] = 'no';
		}
		if (! empty ($vals['loc_addr2'])) {
			$vals['loc_address'] .= "\n" . $vals['loc_addr2'];
		}

		$data = array (
			'title' => $vals['title'],
			'date' => $vals['date'],
			'until_date' => $vals['end_date'],
			'time' => $vals['time'],
			'until_time' => $vals['end_time'],
			'category' => $vals['category'],
			'audience' => $vals['audience'],
			'details' => $vals['details'],
			'contact' => $vals['contact'],
			'contact_email' => $vals['contact_email'],
			'contact_phone' => $vals['contact_phone'],
			'contact_url' => $vals['contact_url'],
			'loc_name' => $vals['loc_name'],
			'loc_address' => $vals['loc_address'],
			'loc_city' => $vals['loc_city'],
			'loc_province' => $vals['loc_province'],
			'loc_country' => $vals['loc_country'],
			'sponsor' => $vals['sponsor'],
			'rsvp' => $vals['rsvp'],
			'public' => $vals['public'],
			'media' => $vals['media'],
			'sitellite_status' => 'draft',
			'sitellite_access' => 'public',
		);

		if (session_valid ()) {
			$data['sitellite_owner'] = session_username ();
			$data['sitellite_team'] = session_team ();
		}

		// 2. submit event as 'draft'
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ('siteevent_event');
		$res = $rex->create (
			$data,
			'Event submission.'
		);

		$vals['id'] = $res;

		// 3. email notification
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//		@mail (appconf ('submissions'), 'Event Submission Notice', template_simple ('submission_email.spt', $vals));
//-----------------------------------------------
        site_mail (
            appconf ('submissions'),
            'Event Submission Notice',
            template_simple ('submission_email.spt', $vals),
            array ("Is_HTML" => true)
        );
//END: SEMIAS.
		// 4. thank you screen
		page_title (intl_get ('Thank You!'));
		echo template_simple ('submissions.spt');
	}
}

?>