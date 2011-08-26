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
// #174 CMS cancel.
//

global $cgi;

set_time_limit(0);
ini_set ('memory_limit', '24M');

loader_import ('saf.File');
loader_import ('sitetemplate.Filters');
loader_import ('cms.Versioning.Rex');

class Sitemailer2MsgForm extends MailForm {
	function Sitemailer2MsgForm () {
		parent::MailForm ();

		global $page, $cgi;

        page_add_script (site_prefix () . '/js/prompt.js');
        page_add_script ('
            function sm2_prompt (f) {
                xed_copy_value (f, \'body\');
                prompt (
                    \'Email address to send test message to:\',
                    document.forms[0].elements.test_email.value, 
                    function (email) {
                        document.forms[0].elements.test_email.value = email;

			// save the original action and target
			a = document.forms[0].action;
			t = document.forms[0].target;

                        document.forms[0].action = \'' . site_prefix () . '/index/sitemailer2-testemail-action\';
                        document.forms[0].target = \'_BLANK\';
                        document.forms[0].submit ();

			// reset the original action and target
			document.forms[0].action = a;
			document.forms[0].target = t;
                    }
                );
                
                return false;
                
            }
        ');
        
        //if add is true, we're creating a template/msg, otherwise we're editing a template/msg
		$add = true;
		if (isset ($cgi->_key) && ! empty($cgi->_key)) {
			$add = false;
		}
        
        //this message already exists
		if (! $add) {
            
            $document = db_single ('select * from sitemailer2_message where id = ?', $cgi->_key);
            
            //special display
            if ($document->status == 'done' || $document->status == 'running') {
                
                $this->parseSettings ('inc/app/sitemailer2/forms/msg/settingsstatic.php');
                
                $newsletter = db_shift ('select name from sitemailer2_newsletter where id = ?', $document->newsletter);
                
                $this->widgets['recur']->setValue ($document->recurring);
                
                $this->widgets['newsletter']->setValue ($newsletter);
                
                $tpl = db_single ('select title from sitemailer2_template where id = ?' , $document->template);
                $this->widgets['template']->setValue ($tpl->title);
                
                $this->widgets['start']->setValues ($document->start);
                
                $cancel = site_prefix () . '/index/cms-browse-action?collection=sitemailer2_message';
                
                $this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href=\'' . $cancel . '\'; return false"';
                
                if ($document != 'running') unset ($this->widgets['submit_button']->buttons[0]); 
                
            //standard display
            } else {

                $this->parseSettings ('inc/app/sitemailer2/forms/msg/settings.php');
                $this->widgets['template']->setValue ($document->template);
                
                $settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
                $this->widgets['test_email']->setValue ($settings['test_email']);
                
                $dt = explode (' ', $document->start);
                
                $this->widgets['date']->setValue ($dt[0]);
                $this->widgets['time']->setValue ($dt[1]);
                
                $newsletters = db_shift_array ('select newsletter from sitemailer2_message_newsletter where message = ?', $cgi->_key);
                
                $this->widgets['newsletter']->setValue ($newsletters);
                
                $this->widgets['recur']->setValues (assocify (array ('no', 'weekly', 'twice-montly', 'monthly'))); 
                
                $this->widgets['recur']->setValue ($document->recurring);
                
            }
            
            //display for all cases
			page_title ('SiteMailer 2 - ' . intl_get ('Edit Message'));
           
            $this->widgets['title']->setValue ($document->title);
            $this->widgets['status']->setValue (ucfirst ($document->status));
            $this->widgets['from_name']->setValue ($document->fromname);
            $this->widgets['from_email']->setValue ($document->fromemail);
            $this->widgets['body']->setValue ($document->mbody);
            $this->widgets['subject']->setValue ($document->subject);
            
            //how many messages are queued?
            if ($document->numrec != 0 && $document->status == 'running') {
                $total = db_shift ('select count(id) from sitemailer2_q where message = ?', $cgi->_key);
                $total = $document->numrec - $total;
                
                $this->widgets['complete']->setValue ($total . '/' . $document->numrec . ' (' . $total/$document->numrec*100 . "%)");
                
            } elseif ($document->numrec != 0 && $document->status == 'done') {
                
                $this->widgets['complete']->setValue ($document->numsent . '/' . $document->numrec . ' (' . $document->numsent/$document->numrec*100 . "%)");
                
            } else {
                
                $this->widgets['complete']->setValue ('0' . '/' . $document->numrec . ' (0%)');
            }
        
        //else this message is new
		} else {

            $this->parseSettings ('inc/app/sitemailer2/forms/msg/settings.php');
            
            $settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
            $this->widgets['test_email']->setValue ($settings['test_email']);
            
            page_title ('SiteMailer 2 - ' . intl_get ('New Message'));
            $this->widgets['status']->setValue ('Draft');
            unset ($this->widgets['complete']);
            
            $this->widgets['recur']->setValues (assocify (array ('no', 'weekly', 'twice-monthly', 'monthly'))); 

            loader_import ('saf.Date');
            
            $this->widgets['date']->setValue (date('Y-m-d'));
            $this->widgets['time']->setValue (Date::roundTime(date('H:i:s'), 30));
            
			if (is_array ($cgi->newsletter) && count ($cgi->newsletter) == 1) {
				$res = db_single ('select * from sitemailer2_newsletter where id = ?', $cgi->newsletter[0]);
				$this->widgets['from_name']->setValue ($res->from_name);
				$this->widgets['from_email']->setValue ($res->from_email);
				$this->widgets['template']->setValue ($res->template);
				$this->widgets['subject']->setValue ($res->subject);
			}
		}
        
        //unset buttons based on context
        if ($document->status != 'done' && $document->status != 'running') {
            //done only has a back button
        
            unset ($this->widgets['submit_button']->buttons[2]); 
            
            if ($document->status == 'draft' || $add) { 
                unset ($this->widgets['complete']);
            }
        }
        
        $cancel = site_prefix () . '/index/sitemailer2-app';
        
        foreach ($this->widgets['submit_button']->buttons as $k=>$button) {
            if ($button->value == intl_get ('Send Test Message')) {
                $this->widgets['submit_button']->buttons[$k]->extra = 'onclick="return sm2_prompt(this.form)"';
            }
            
            if ($button->value == intl_get ('Cancel')) {
                $this->widgets['submit_button']->buttons[$k]->extra = 'onclick="window.location.href=\'' . $cancel . '\'; return false"';
            }
            
        }
	}

	function onSubmit ($vals) {
        
        //info ($vals);exit;
        
        $id = $vals['_key'];
        
        $newsletters = explode (',', $vals['newsletter']);
        
        if($vals['submit_button'] == intl_get ('Stop Sending')) {
            
            //untested
            $sent = db_shift ('select count(id) from sitemailer2_q where message = ?', $id);
            
            $curr = db_single ('select * from sitemailer2_message where id = ?', $id);
            
            $sent = $curr->numrec - $sent;  
            
            db_execute ('delete from sitemailer2_q where message = ?', $id);
            
            db_execute ('update sitemailer2_message set status = "done", numsent = ? where id = ?', $sent, $id);
            
            header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=stopped');
            exit;
        }
        
        if (empty ($vals['_key'])) { //create
             
            db_execute ('insert into sitemailer2_message 
                (id, title, date, template, status, fromname, fromemail, mbody, subject, numrec, numsent, confirmed_views, start, recurring) 
                values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $vals['title'], //title
                date ('Y-m-d H:i:s'), //date
                $vals['template'], //template
                'draft', //status
                $vals['from_name'], //fromname 
                $vals['from_email'], //fromemail
                $vals['body'], //mbody
                $vals['subject'], //subject
                0, 0, 0, //numrec, numsent, confirmed_views
                $vals['date'] . ' ' . $vals['time'],//start
                $vals['recur']); 
                
            $id = db_lastid ();
            
            foreach ($newsletters as $n) {
               db_execute ('insert into sitemailer2_message_newsletter (id, message, newsletter) values (null, ?, ?)', $id, $n);
            }
         
        } else { //update
         
             db_execute ('update sitemailer2_message set 
                title=?, template=?, status=?, fromname=?, fromemail=?, mbody=?, subject=?, start=?, recurring=?  where id = ?',
                $vals['title'], //title
                $vals['template'], //template
                'draft', //status
                $vals['from_name'], //fromname 
                $vals['from_email'], //fromemail
                $vals['body'], //mbody
                $vals['subject'], //subject
                $vals['date'] . ' ' . $vals['time'],
                $vals['recur'], 
                $id); //start
            
            db_execute ('delete from sitemailer2_message_newsletter where message = ?', $id);
                
            foreach ($newsletters as $n) {
               db_execute ('insert into sitemailer2_message_newsletter (id, message, newsletter) values (null, ?, ?)', $id, $n);
            }
        }
        
        //sending messages to q
        if ($vals['submit_button'] == intl_get ('Send Message')) { //run the mailing list
            
            set_time_limit (0);
            $added = 0;
            
            if ($vals['date'] == 'SITEEVENT_TODAY') { 
                $date = date ('Y-m-d');
            } else {
                $date = $vals['date'];
            }

            //get a list of recipients
            $q = db_query ('select distinct recipient from sitemailer2_recipient_in_newsletter 
                                        where newsletter in(' . $vals['newsletter'] . ') and status="subscribed"');
            if ($q->execute ()) {
            	while ($r = $q->fetch ()) {
            		if (! db_execute ('insert into sitemailer2_q (id, recipient, message, attempts, created, last_attempt, last_error, next_attempt) values (null, ?, ?, 0, now(), "", "", ?)', $r->recipient, $id, $date . ' ' . $vals['time'])) {
            			echo '<p>Error adding recipient to the queue.</p>';
            			exit;
            		} else {
            			$added++;
            		}
            	}
            } else {
            	echo '<p>No recipients found.</p>';
            	exit;
            }

			/*
            $res = db_shift_array ('select distinct recipient from sitemailer2_recipient_in_newsletter 
                                        where newsletter in(' . $vals['newsletter'] . ') and status="subscribed"');
                                        
            //add all emails in $res to the q with the neccesary info
            $added = 0;
            
            if ($vals['date'] == 'SITEEVENT_TODAY') { 
                $date = date ('Y-m-d');
            } else {
                $date = $vals['date'];
            }
            
            foreach ($res as $r) {
                
                if (! db_execute ('insert into sitemailer2_q (id, recipient, message, attempts, created, last_attempt, last_error, next_attempt) values (null, ?, ?, 0, now(), "", "", ?)', $r, $id, $date . ' ' . $vals['time'])) {
                    echo '<p>Error adding message to q.</p>';
                    exit;
                } else {
                    $added++;
                }
            }
            */
            
            db_execute ('update sitemailer2_message set numrec='. $added . ', status="running" where id = ?', $id);
            
            //set up recurrences, if needed
            if ($vals['recur'] != 'no') {
                //find year, month, day
                //hour, minute, second
                
                list ($year, $month, $day) = explode ('-', $date);

                list ($hour, $minute, $second) = explode (':', $time);
                
                //set next send time
                if ($vals['recur'] == 'daily') {
                    $day += 1;
                } else if ($vals['recur'] == 'weekly') {
                    $day += 7;
                } else if ($vals['recur'] == 'twice-monthly') {
                    $day += 15;
                } else if ($vals['recur'] == 'monthly') {
                    $month += 1;
                } else {
                    echo 'form error! invalid recurrence type';exit;
                }
                
                $next_recurrence = date ('Y-m-d H:i:s', mktime ($hour, $minute, $second, $month, $day, $year));
                
                if (! db_execute ('update sitemailer2_message set next_recurrence=? where id=?', $next_recurrence, $id)) {
                    echo 'update of next_recurrence failed';
                    exit;
                }
            }
            
            header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=sending');
	        exit;
	    } else {
	        	header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=draft');
	        exit;
	    }
	}
}

?>