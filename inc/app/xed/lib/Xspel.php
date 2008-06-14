<?php

loader_import ('xed.Aspell');
loader_import ('xed.Functions');

class Xspel {
	var $pspell;
	var $lang;
	var $personal = array ();
	var $checked = array ();

	function Xspel ($lang = 'en') {
		$this->lang = $lang;
		ob_start ();
		$this->pspell = pspell_new ($lang);
		$str = ob_get_contents ();
		ob_end_clean ();
		if (! empty ($str)) {
			$this->error = $str;
		}
		$this->getPersonal ();
	}

	function getWords ($text) {
		$words = str_word_count ($text, 2);
		$o = 0;
		$i = strpos ($text, '<', $o);
		while ($i !== false) {
			$o = $i;
			$e = strpos ($text, '>', $o);
			if ($e === false) {
				break;
			}
			foreach ($words as $k => $w) {
				if ($k > $i && $k < $e) {
					unset ($words[$k]);
				}
			}

			$o = $e;
			$i = strpos ($text, '<', $o);
		}

		return $words;
	}

	function checkSpelling ($text) {
		$list = array ();
		$this->checked = array ();
		$words = $this->getWords ($text);
		foreach ($words as $k => $w) {

			// don't check the same word twice
			if (isset ($this->checked[$w])) {
				if (is_array ($this->checked[$w])) {
					$list[] = array (
						'word' => $this->escape ($w),
						'offset' => $k,
						'length' => strlen ($w),
						'suggestions' => $this->escape ($this->checked[$w]),
					);
				}
				continue;
			}

			// if it's in the personal dictionary, it's valid
			if ($this->checkPersonal ($w)) {
				$this->checked[$w] = true;
				continue;
			}

			// if it's in the suggestions table, we can avoid pspell
			$sug = $this->checkSuggestions ($w);

			// if it's an array, the word is incorrect
			if (is_array ($sug)) {
				$list[] = array (
					'word' => $this->escape ($w),
					'offset' => $k,
					'length' => strlen ($w),
					'suggestions' => $this->escape ($sug),
				);
				$this->checked[$w] = $sug;
				continue;

			// if it's a boolean true, the word is correct
			} elseif ($sug === true) {
				$this->checked[$w] = true;
				continue;
			}

			// finally, we check with pspell...

			// if it comes up false, it's a mistake
			if (! pspell_check ($this->pspell, $w)) {
				$sug = pspell_suggest ($this->pspell, $w);
				$list[] = array (
					'word' => $this->escape ($w),
					'offset' => $k,
					'length' => strlen ($w),
					'suggestions' => $this->escape ($sug),
				);
				$this->addSuggestion ($w, $sug);
				$this->checked[$w] = $sug;

			// the word is correct, let's have Xspel learn it
			} else {
				$this->addSuggestion ($w);
				$this->checked[$w] = true;
			}
		}
		return $list;
	}

	function escape ($list) {
		if (is_array ($list)) {
			foreach ($list as $k => $v) {
				$list[$k] = str_replace (array ("\r", "\n", "'"), array ('\\r', '\\n', '\\\''), $v);
			}
		} else {
			$list = str_replace (array ("\r", "\n", "'"), array ('\\r', '\\n', '\\\''), $list);
		}
		return $list;
	}

	function checkSuggestions ($word) {
		$sug = db_shift (
			'select suggestions from xed_speling_suggestions where word = ? and lang = ?',
			$word,
			$this->lang
		);
		if (! empty ($sug)) {
			return unserialize ($sug);
		}
		return false;
	}

	function addSuggestion ($word, $suggestions = true) {
		if (! appconf ('pspell_save_suggestions')) {
			return true;
		}
		return db_execute (
			'insert into xed_speling_suggestions (word, lang, suggestions) values (?, ?, ?)',
			$word,
			$this->lang,
			serialize ($suggestions)
		);
	}

	function getPersonal () {
		if (! session_valid ()) {
			$this->personal = array ();
		}
		$this->personal = db_shift_array (
			'select word from xed_speling_personal where username = ? order by word asc',
			session_username ()
		);
	}

	function checkPersonal ($word) {
		return in_array ($word, $this->personal);
	}

	function addPersonal ($word) {
		if (! session_valid ()) {
			return false;
		}

		return db_execute (
			'insert into xed_speling_personal (id, username, word) values (null, ?, ?)',
			session_username (),
			$word
		);
	}

	function removePersonal ($word) {
		if (! session_valid ()) {
			return false;
		}

		return db_execute (
			'delete from xed_speling_personal where username = ? and word = ?',
			session_username (),
			$word
		);
	}
}

?>