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
// #171 nolist error_mode.
//

if (! class_exists ('sitelliteutilcustomform')) {

loader_import ('saf.MailForm');

class SitelliteUtilCustomForm extends MailForm {
	function SitelliteUtilCustomForm ($parameters) {
		parent::MailForm ();
// Start: SEMIAS #171 nolist error_mode.
		$this->error_mode = 'nolist';
// END: SEMIAS
		$this->verify_sender = 'yes';
		$this->clean_input = 'yes';

		foreach ($parameters as $k => $v) {
			$parameters[$k] = urldecode ($v);
		}

		$this->parameters = $parameters;

		//$this->title = $parameters['title'];

		foreach (explode (',', $parameters['fields']) as $field) {
			switch ($field) {
				case 'Account #':
					$w =& $this->addWidget ('text', 'account_number');
					$w->alt = intl_get ('Account') . ' #';
					$w->addRule ('not empty', intl_get ('You must enter your account #'));
					$w->extra = 'maxlength="72"';
					break;
				case 'Pass phrase':
					$w =& $this->addWidget ('password', 'pass_phrase');
					$w->alt = intl_get ('Pass Phrase');
					$w->addRule ('not empty', intl_get ('You must enter your pass phrase'));
					$w->extra = 'maxlength="72"';
					break;
				case 'Salutation':
					$w =& $this->addWidget ('select', 'salutation');
					$w->alt = intl_get ('Salutation');
					$w->setValues (assocify (array ('- SELECT -', 'mr', 'mrs', 'ms', 'miss', 'mdm', 'dr', 'sir')));
					$w->extra = 'maxlength="12"';
					break;
				case 'First Name':
					$w =& $this->addWidget ('text', 'first_name');
					$w->alt = intl_get ('First Name');
					$w->addRule ('not empty', intl_get ('You must enter your first name'));
					$w->addRule ('header', intl_get ('Your first name contains invalid characters'));
					$w->extra = 'maxlength="72"';
					break;
				case 'Last Name':
					$w =& $this->addWidget ('text', 'last_name');
					$w->alt = intl_get ('Last Name');
					$w->addRule ('not empty', intl_get ('You must enter your last name'));
					$w->addRule ('header', intl_get ('Your last name contains invalid characters'));
					$w->extra = 'maxlength="72"';
					break;
				case 'Email Address':
					$w =& $this->addWidget ('text', 'email_address');
					$w->alt = intl_get ('Email Address');
					$w->addRule ('email', intl_get ('Your email address does not appear to be valid'));
					$w->extra = 'maxlength="72"';
					break;
				case 'Birthday':
					$w =& $this->addWidget ('date', 'birthday');
					$w->alt = intl_get ('Birthday');
					$w->highest_year = date ('Y') - 4;
					$w->lowest_year = date ('Y') - 100;
					break;
				case 'Gender':
					$w =& $this->addWidget ('select', 'gender');
					$w->alt = intl_get ('Gender');
					$w->setValues (assocify (array ('- SELECT -', 'male', 'female')));
					break;
				case 'Address (incl City/State/Country)':
					$w =& $this->addWidget ('text', 'address_line1');
					$w->alt = intl_get ('Mailing Address');
					$w->extra = 'maxlength="72"';
					$w =& $this->addWidget ('text', 'address_line2');
					$w->alt = intl_get ('(Line 2)');
					$w->extra = 'maxlength="72"';
					$w =& $this->addWidget ('text', 'city');
					$w->alt = intl_get ('City');
					$w->extra = 'maxlength="72"';
					$w =& $this->addWidget ('select', 'state');
					$w->setValues ($this->getStates ());
					$w->alt = intl_get ('State');
					$w =& $this->addWidget ('select', 'country');
					$w->setValues ($this->getCountries ());
					$w->alt = intl_get ('Country');
					$w =& $this->addWidget ('text', 'zip');
					$w->alt = intl_get ('Zip/Postal Code');
					$w->extra = 'maxlength="24"';
					break;
				case 'Company':
					$w =& $this->addWidget ('text', 'company');
					$w->alt = intl_get ('Company');
					$w->extra = 'maxlength="72"';
					break;
				case 'Job Title':
					$w =& $this->addWidget ('text', 'job_title');
					$w->alt = intl_get ('Job Title');
					$w->extra = 'maxlength="72"';
					break;
				case 'Phone Number':
					$w =& $this->addWidget ('text', 'phone_number');
					$w->alt = intl_get ('Phone Number');
					$w->extra = 'maxlength="72"';
					break;
				case 'Daytime Phone':
					$w =& $this->addWidget ('text', 'daytime_phone');
					$w->alt = intl_get ('Daytime Phone');
					$w->extra = 'maxlength="72"';
					break;
				case 'Evening Phone':
					$w =& $this->addWidget ('text', 'evening_phone');
					$w->alt = intl_get ('Evening Phone');
					$w->extra = 'maxlength="72"';
					break;
				case 'Mobile Phone':
					$w =& $this->addWidget ('text', 'mobile_phone');
					$w->alt = intl_get ('Mobile Phone');
					$w->extra = 'maxlength="72"';
					break;
				case 'Fax Number':
					$w =& $this->addWidget ('text', 'fax_number');
					$w->alt = intl_get ('Fax Number');
					$w->extra = 'maxlength="72"';
					break;
				case 'Preferred method of contact':
					$w =& $this->addWidget ('select', 'preferred_method_of_contact');
					$w->alt = intl_get ('Preferred Method of Contact');
					$w->setValues (assocify (array ('- SELECT -', 'phone', 'email')));
					break;
				case 'Best time to reach you':
					$w =& $this->addWidget ('select', 'best_time');
					$w->alt = intl_get ('Best Time to Reach You');
					$w->setValues (assocify (array ('- SELECT -', 'morning', 'afternoon', 'evening')));
					break;
				case 'May we contact you':
					$w =& $this->addWidget ('select', 'may_we_contact_you');
					$w->alt = intl_get ('May We Contact You');
					$w->setValues (assocify (array ('- SELECT -', 'yes', 'no')));
					break;
				case 'Comments':
					$w =& $this->addWidget ('textarea', 'comments');
					$w->alt = intl_get ('Comments');
					break;
			}
		}

// SEMIAS: START. #188 - form captcha improvements.
        $w =& $this->addWidget ('security', 'Security');

        $ps = new phpSniff ();
        $version = substr(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'),5,1);
        if ($ps->property ('browser') == 'ie' && $version <= 7){
          $w->verify_method = "figlet";
        }
        else
          $w->verify_method = "turing";
        // $w->verify_method = "turing";
       	// $w->verify_method = "recaptcha";
// SEMIAS: END.

		if ($parameters['cc'] == 'optional') {
			$w =& $this->addWidget ('checkbox', 'cc');
			$w->fieldset = false;
			$w->setValues (array ('yes' => intl_get ('Please send a copy of this form to my email address')));
		}

		$w =& $this->addWidget ('submit', 'submit_button');
		$w->setValues (intl_get ('Send'));

		//$this->action = site_prefix () . '/index/sitellite-util-contact-action';
		//global $cgi;
		//$cgi->email = $send_to;
		//$cgi->param[] = 'email';
	}

	function onSubmit ($vals) {
		//info ($vals);
		//return;

		if ($vals['salutation'] == '- SELECT -') {
			unset ($vals['salutation']);
		}
		if ($vals['gender'] == '- SELECT -') {
			unset ($vals['gender']);
		}
		if ($vals['state'] == '- SELECT -') {
			unset ($vals['state']);
		}
		if ($vals['country'] == '- SELECT -') {
			unset ($vals['country']);
		}
		if ($vals['preferred_method_of_contact'] == '- SELECT -') {
			unset ($vals['preferred_method_of_contact']);
		}
		if ($vals['best_time'] == '- SELECT -') {
			unset ($vals['best_time']);
		}
		if ($vals['may_we_contact_you'] == '- SELECT -') {
			unset ($vals['may_we_contact_you']);
		}

		if ($this->parameters['save'] == 'yes') {
			// save to sitellite_form_submission table
			db_execute (
				'insert into sitellite_form_submission values (null, ?, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				$this->parameters['form_type'],
				$this->parameters['title'],
				$_SERVER['REMOTE_ADDR'],
				$vals['account_number'],
				$vals['pass_phrase'],
				$vals['salutation'],
				$vals['first_name'],
				$vals['last_name'],
				$vals['email_address'],
				$vals['birthday'],
				$vals['gender'],
				$vals['address_line1'],
				$vals['address_line2'],
				$vals['city'],
				$vals['state'],
				$vals['country'],
				$vals['zip'],
				$vals['company'],
				$vals['job_title'],
				$vals['phone_number'],
				$vals['daytime_phone'],
				$vals['evening_phone'],
				$vals['mobile_phone'],
				$vals['fax_number'],
				$vals['preferred_method_of_contact'],
				$vals['best_time'],
				$vals['may_we_contact_you'],
				$vals['comments']
			);
		}

		if ($this->parameters['cc'] == 'yes' || $vals['cc'] == 'yes') {
			$cc = "\r\nCC: " . $vals['email_address'];
		} else {
			$cc = '';
		}

		if (! @mail (
			$this->parameters['email'],
			'[' . site_domain () . '] ' . $this->parameters['title'],
			template_simple ('util_custom_email.spt', $vals),
			'From: ' . $vals['first_name'] . ' ' . $vals['last_name'] . ' <' . $vals['email_address'] . '>' . $cc
		)) {
			page_title (intl_get ('An Error Occurred'));
			echo '<p>' . intl_get ('Our apologies, your form failed to be submitted.  Please try again later.') . '</p>';
			return;
		}

		if ($this->parameters['context'] == 'action') {
			page_title (intl_get ('Thank You'));
		} else {
			echo '<h1>' . intl_get ('Thank You') . '</h1>';
		}
		echo template_simple ('util_custom_thanks.spt');
	}

	function getCountries () {
		return array (
			'- SELECT -' => '-- SELECT --',
			'Afghanistan' => 'Afghanistan',
			'Albania' => 'Albania',
			'Algeria' => 'Algeria',
			'American Samoa' => 'American Samoa',
			'Andorra' => 'Andorra',
			'Angola' => 'Angola',
			'Anguilla' => 'Anguilla',
			'Antarctica' => 'Antarctica',
			'Antigua And Barbuda' => 'Antigua And Barbuda',
			'Argentina' => 'Argentina',
			'Armenia' => 'Armenia',
			'Aruba' => 'Aruba',
			'Australia' => 'Australia',
			'Austria' => 'Austria',
			'Azerbaijan' => 'Azerbaijan',
			'Bahamas' => 'Bahamas',
			'Bahrain' => 'Bahrain',
			'Bangladesh' => 'Bangladesh',
			'Barbados' => 'Barbados',
			'Belarus' => 'Belarus',
			'Belgium' => 'Belgium',
			'Belize' => 'Belize',
			'Benin' => 'Benin',
			'Bermuda' => 'Bermuda',
			'Bhutan' => 'Bhutan',
			'Bolivia' => 'Bolivia',
			'Bosnia And Herzegovina' => 'Bosnia And Herzegovina',
			'Botswana' => 'Botswana',
			'Bouvet Island' => 'Bouvet Island',
			'Brazil' => 'Brazil',
			'British Indian Ocean Territory' => 'British Indian Ocean Territory',
			'Brunei Darussalam' => 'Brunei Darussalam',
			'Bulgaria' => 'Bulgaria',
			'Burkina Faso' => 'Burkina Faso',
			'Burundi' => 'Burundi',
			'Cambodia' => 'Cambodia',
			'Cameroon' => 'Cameroon',
			'Canada' => 'Canada',
			'Cape Verde' => 'Cape Verde',
			'Cayman Islands' => 'Cayman Islands',
			'Central African Republic' => 'Central African Republic',
			'Chad' => 'Chad',
			'Chile' => 'Chile',
			'China' => 'China',
			'Christmas Island' => 'Christmas Island',
			'Cocos (keeling) Islands' => 'Cocos (keeling) Islands',
			'Colombia' => 'Colombia',
			'Comoros' => 'Comoros',
			'Congo' => 'Congo',
			'Congo, The Democratic Republic Of The' => 'Congo, The Democratic Republic Of The',
			'Cook Islands' => 'Cook Islands',
			'Costa Rica' => 'Costa Rica',
			'Cote D\'ivoire' => 'Cote D\'ivoire',
			'Croatia' => 'Croatia',
			'Cuba' => 'Cuba',
			'Cyprus' => 'Cyprus',
			'Czech Republic' => 'Czech Republic',
			'Denmark' => 'Denmark',
			'Djibouti' => 'Djibouti',
			'Dominica' => 'Dominica',
			'Dominican Republic' => 'Dominican Republic',
			'Ecuador' => 'Ecuador',
			'Egypt' => 'Egypt',
			'El Salvador' => 'El Salvador',
			'Equatorial Guinea' => 'Equatorial Guinea',
			'Eritrea' => 'Eritrea',
			'Estonia' => 'Estonia',
			'Ethiopia' => 'Ethiopia',
			'Falkland Islands (malvinas)' => 'Falkland Islands (malvinas)',
			'Faroe Islands' => 'Faroe Islands',
			'Fiji' => 'Fiji',
			'Finland' => 'Finland',
			'France' => 'France',
			'French Guiana' => 'French Guiana',
			'French Polynesia' => 'French Polynesia',
			'French Southern Territories' => 'French Southern Territories',
			'Gabon' => 'Gabon',
			'Gambia' => 'Gambia',
			'Georgia' => 'Georgia',
			'Germany' => 'Germany',
			'Ghana' => 'Ghana',
			'Gibraltar' => 'Gibraltar',
			'Greece' => 'Greece',
			'Greenland' => 'Greenland',
			'Grenada' => 'Grenada',
			'Guadeloupe' => 'Guadeloupe',
			'Guam' => 'Guam',
			'Guatemala' => 'Guatemala',
			'Guinea' => 'Guinea',
			'Guinea-bissau' => 'Guinea-bissau',
			'Guyana' => 'Guyana',
			'Haiti' => 'Haiti',
			'Heard Island And Mcdonald Islands' => 'Heard Island And Mcdonald Islands',
			'Holy See (Vatican City State)' => 'Holy See (Vatican City State)',
			'Honduras' => 'Honduras',
			'Hong Kong' => 'Hong Kong',
			'Hungary' => 'Hungary',
			'Iceland' => 'Iceland',
			'India' => 'India',
			'Indonesia' => 'Indonesia',
			'Iran, Islamic Republic Of' => 'Iran, Islamic Republic Of',
			'Iraq' => 'Iraq',
			'Ireland' => 'Ireland',
			'Israel' => 'Israel',
			'Italy' => 'Italy',
			'Jamaica' => 'Jamaica',
			'Japan' => 'Japan',
			'Jordan' => 'Jordan',
			'Kazakhstan' => 'Kazakhstan',
			'Kenya' => 'Kenya',
			'Kiribati' => 'Kiribati',
			'Korea, Democratic People\'s Republic Of' => 'Korea, Democratic People\'s Republic Of',
			'Korea, Republic Of' => 'Korea, Republic Of',
			'Kuwait' => 'Kuwait',
			'Kyrgyzstan' => 'Kyrgyzstan',
			'Lao People\'s Democratic Republic' => 'Lao People\'s Democratic Republic',
			'Latvia' => 'Latvia',
			'Lebanon' => 'Lebanon',
			'Lesotho' => 'Lesotho',
			'Liberia' => 'Liberia',
			'Libyan Arab Jamahiriya' => 'Libyan Arab Jamahiriya',
			'Liechtenstein' => 'Liechtenstein',
			'Lithuania' => 'Lithuania',
			'Luxembourg' => 'Luxembourg',
			'Macao' => 'Macao',
			'Macedonia, The Former Yugoslav Republic Of' => 'Macedonia, The Former Yugoslav Republic Of',
			'Madagascar' => 'Madagascar',
			'Malawi' => 'Malawi',
			'Malaysia' => 'Malaysia',
			'Maldives' => 'Maldives',
			'Mali' => 'Mali',
			'Malta' => 'Malta',
			'Marshall Islands' => 'Marshall Islands',
			'Martinique' => 'Martinique',
			'Mauritania' => 'Mauritania',
			'Mauritius' => 'Mauritius',
			'Mayotte' => 'Mayotte',
			'Mexico' => 'Mexico',
			'Micronesia, Federated States Of' => 'Micronesia, Federated States Of',
			'Moldova, Republic Of' => 'Moldova, Republic Of',
			'Monaco' => 'Monaco',
			'Mongolia' => 'Mongolia',
			'Montserrat' => 'Montserrat',
			'Morocco' => 'Morocco',
			'Mozambique' => 'Mozambique',
			'Myanmar' => 'Myanmar',
			'Namibia' => 'Namibia',
			'Nauru' => 'Nauru',
			'Nepal' => 'Nepal',
			'Netherlands' => 'Netherlands',
			'Netherlands Antilles' => 'Netherlands Antilles',
			'New Caledonia' => 'New Caledonia',
			'New Zealand' => 'New Zealand',
			'Nicaragua' => 'Nicaragua',
			'Niger' => 'Niger',
			'Nigeria' => 'Nigeria',
			'Niue' => 'Niue',
			'Norfolk Island' => 'Norfolk Island',
			'Northern Mariana Islands' => 'Northern Mariana Islands',
			'Norway' => 'Norway',
			'Oman' => 'Oman',
			'Pakistan' => 'Pakistan',
			'Palau' => 'Palau',
			'Palestinian Territory, Occupied' => 'Palestinian Territory, Occupied',
			'Panama' => 'Panama',
			'Papua New Guinea' => 'Papua New Guinea',
			'Paraguay' => 'Paraguay',
			'Peru' => 'Peru',
			'Philippines' => 'Philippines',
			'Pitcairn' => 'Pitcairn',
			'Poland' => 'Poland',
			'Portugal' => 'Portugal',
			'Puerto Rico' => 'Puerto Rico',
			'Qatar' => 'Qatar',
			'Reunion' => 'Reunion',
			'Romania' => 'Romania',
			'Russian Federation' => 'Russian Federation',
			'Rwanda' => 'Rwanda',
			'Saint Helena' => 'Saint Helena',
			'Saint Kitts And Nevis' => 'Saint Kitts And Nevis',
			'Saint Lucia' => 'Saint Lucia',
			'Saint Pierre And Miquelon' => 'Saint Pierre And Miquelon',
			'Saint Vincent And The Grenadines' => 'Saint Vincent And The Grenadines',
			'Samoa' => 'Samoa',
			'San Marino' => 'San Marino',
			'Sao Tome And Principe' => 'Sao Tome And Principe',
			'Saudi Arabia' => 'Saudi Arabia',
			'Senegal' => 'Senegal',
			'Serbia And Montenegro' => 'Serbia And Montenegro',
			'Seychelles' => 'Seychelles',
			'Sierra Leone' => 'Sierra Leone',
			'Singapore' => 'Singapore',
			'Slovakia' => 'Slovakia',
			'Slovenia' => 'Slovenia',
			'Solomon Islands' => 'Solomon Islands',
			'Somalia' => 'Somalia',
			'South Africa' => 'South Africa',
			'South Georgia And The South Sandwich Islands' => 'South Georgia And The South Sandwich Islands',
			'Spain' => 'Spain',
			'Sri Lanka' => 'Sri Lanka',
			'Sudan' => 'Sudan',
			'Suriname' => 'Suriname',
			'Svalbard And Jan Mayen' => 'Svalbard And Jan Mayen',
			'Swaziland' => 'Swaziland',
			'Sweden' => 'Sweden',
			'Switzerland' => 'Switzerland',
			'Syrian Arab Republic' => 'Syrian Arab Republic',
			'Taiwan' => 'Taiwan',
			'Tajikistan' => 'Tajikistan',
			'Tanzania, United Republic Of' => 'Tanzania, United Republic Of',
			'Thailand' => 'Thailand',
			'Timor-leste' => 'Timor-leste',
			'Togo' => 'Togo',
			'Tokelau' => 'Tokelau',
			'Tonga' => 'Tonga',
			'Trinidad And Tobago' => 'Trinidad And Tobago',
			'Tunisia' => 'Tunisia',
			'Turkey' => 'Turkey',
			'Turkmenistan' => 'Turkmenistan',
			'Turks And Caicos Islands' => 'Turks And Caicos Islands',
			'Tuvalu' => 'Tuvalu',
			'Uganda' => 'Uganda',
			'Ukraine' => 'Ukraine',
			'United Arab Emirates' => 'United Arab Emirates',
			'United Kingdom' => 'United Kingdom',
			'United States' => 'United States',
			'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
			'Uruguay' => 'Uruguay',
			'Uzbekistan' => 'Uzbekistan',
			'Vanuatu' => 'Vanuatu',
			'Venezuela' => 'Venezuela',
			'Viet Nam' => 'Viet Nam',
			'Virgin Islands, British' => 'Virgin Islands, British',
			'Virgin Islands, U.S.' => 'Virgin Islands, U.S.',
			'Wallis And Futuna' => 'Wallis And Futuna',
			'Western Sahara' => 'Western Sahara',
			'Yemen' => 'Yemen',
			'Zambia' => 'Zambia',
			'Zimbabwe' => 'Zimbabwe',
		);
	}
	
	function getStates () {
		return array (
			'- SELECT -' => '- SELECT -',
			'Alabama' => 'Alabama',
			'Alaska' => 'Alaska',
			'Arizona' => 'Arizona',
			'Arkansas' => 'Arkansas',
			'Armed Forces America' => 'Armed Forces America',
			'Armed Forces Europe' => 'Armed Forces Europe',
			'Armed Forces Pacific' => 'Armed Forces Pacific',
			'California' => 'California',
			'Colorado' => 'Colorado',
			'Connecticut' => 'Connecticut',
			'Delaware' => 'Delaware',
			'District Of Columbia' => 'District Of Columbia',
			'Florida' => 'Florida',
			'Georgia' => 'Georgia',
			'Hawaii' => 'Hawaii',
			'Idaho' => 'Idaho',
			'Illinois' => 'Illinois',
			'Indiana' => 'Indiana',
			'Iowa' => 'Iowa',
			'Kansas' => 'Kansas',
			'Kentucky' => 'Kentucky',
			'Louisiana' => 'Louisiana',
			'Maine' => 'Maine',
			'Maryland' => 'Maryland',
			'Massachusetts' => 'Massachusetts',
			'Michigan' => 'Michigan',
			'Minnesota' => 'Minnesota',
			'Mississippi' => 'Mississippi',
			'Missouri' => 'Missouri',
			'Montana' => 'Montana',
			'Nebraska' => 'Nebraska',
			'Nevada' => 'Nevada',
			'New Hampshire' => 'New Hampshire',
			'New Jersey' => 'New Jersey',
			'New Mexico' => 'New Mexico',
			'New York' => 'New York',
			'North Carolina' => 'North Carolina',
			'North Dakota' => 'North Dakota',
			'Ohio' => 'Ohio',
			'Oklahoma' => 'Oklahoma',
			'Oregon' => 'Oregon',
			'Pennsylvania' => 'Pennsylvania',
			'Rhode Island' => 'Rhode Island',
			'South Carolina' => 'South Carolina',
			'South Dakota' => 'South Dakota',
			'Tennessee' => 'Tennessee',
			'Texas' => 'Texas',
			'Utah' => 'Utah',
			'Vermont' => 'Vermont',
			'Virginia' => 'Virginia',
			'Washington' => 'Washington',
			'West Virginia' => 'West Virginia',
			'Wisconsin' => 'Wisconsin',
			'Wyoming' => 'Wyoming',
			'Alberta' => 'Alberta',
			'British Columbia' => 'British Columbia',
			'Manitoba' => 'Manitoba',
			'New Brunswick' => 'New Brunswick',
			'Newfoundland And Labrador' => 'Newfoundland And Labrador',
			'Northwest Territories' => 'Northwest Territories',
			'Nova Scotia' => 'Nova Scotia',
			'Nunavut' => 'Nunavut',
			'Ontario' => 'Ontario',
			'Prince Edward Island' => 'Prince Edward Island',
			'Quebec' => 'Quebec',
			'Saskatchewan' => 'Saskatchewan',
			'Yukon' => 'Yukon',
		);
	}
}

}

$parameters['context'] = $context;
$form = new SitelliteUtilCustomForm ($parameters);
echo $form->run ();

?>