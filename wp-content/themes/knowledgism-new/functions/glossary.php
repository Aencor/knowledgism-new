<?php
/**
 * Glossary Functions
 * Provides parsing, data retrieval, and helpers for the Training Glossary.
 *
 * @package Knowledgism
 */

function knowledgism_get_glossary_terms() {
	static $cached_terms = null;
	if ($cached_terms !== null) {
		return $cached_terms;
	}

	$all_terms = [];

	// 1. Curated foundational Knowledgism terms & axioms
	$curated = [
		[
			'word'    => 'AREA OF PLAY',
			'meaning' => 'The specific sphere, game, or dimension of life in which an individual chooses to operate, create, and achieve mastery.',
			'zone'    => '',
		],
		[
			'word'    => 'AXIOM',
			'meaning' => 'A self-evident truth or established natural rule that serves as an immovable premise for understanding human ability and mind.',
			'zone'    => '',
		],
		[
			'word'    => 'BEINGNESS',
			'meaning' => 'The state or condition of existing as a conscious, spiritual entity possessing native intent, purpose, and certainty.',
			'zone'    => 'green',
		],
		[
			'word'    => 'CERTAINTY',
			'meaning' => 'The state of knowing without reservation, doubt, or external dependency. The natural condition of individuals operating in high Zones.',
			'zone'    => 'gold',
		],
		[
			'word'    => 'CHARGE',
			'meaning' => 'Stored emotional, mental, or spiritual energy attached to past painful experiences that blinds current perception and saps vitality.',
			'zone'    => 'red',
		],
		[
			'word'    => 'ERAS OF KNOWLEDGE',
			'meaning' => 'The three evolutionary epochs of human society: The Warrior Era (coercion and physical force), The Literacy Era (reading, memory, and bureaucracy), and The Knowledge Era (spiritual certainty and applied truth).',
			'zone'    => '',
		],
		[
			'word'    => 'FIXED MINDSET',
			'meaning' => 'A defensive, calcified orientation born of past uninspected failures and charge, where an individual refuses to perceive new data or expand.',
			'zone'    => 'red',
		],
		[
			'word'    => 'GOLD ZONE',
			'meaning' => 'The highest operating band in the Zones of Life—the Golden Magic Zone characterized by effortless manifestation, joyful play, pure sovereignty, and total abundance.',
			'zone'    => 'gold',
		],
		[
			'word'    => 'GREEN ZONE',
			'meaning' => 'The operating band of expansion, high competence, ethical leadership, strategic clarity, and joyful accomplishment.',
			'zone'    => 'green',
		],
		[
			'word'    => 'KNOWLEDGE ERA',
			'meaning' => 'The emerging epoch in which conscious individuals discover who they are, dissolve subconscious limitations, and operate as sovereign creators rather than pawns.',
			'zone'    => '',
		],
		[
			'word'    => 'KNOWLEDGIS|M',
			'meaning' => 'The comprehensive body of research, philosophy, and applied spiritual technology developed by Alan C. Walter to unlock human potential, power, and wealth.',
			'zone'    => '',
		],
		[
			'word'    => 'LIFE FORCE',
			'meaning' => 'The fundamental spiritual vitality that animates existence. The fuel behind intention, creativity, charisma, and personal presence.',
			'zone'    => 'green',
		],
		[
			'word'    => 'LITERACY ERA',
			'meaning' => 'The civilization period preceding the Knowledge Era, founded upon written legalisms, rote memorization, and institutional dependence.',
			'zone'    => '',
		],
		[
			'word'    => 'OMNI-SOVEREIGNTY',
			'meaning' => 'Total, uncompromised spiritual self-governance and harmonious command over one\'s thoughts, purposes, and relationship to the universe.',
			'zone'    => 'gold',
		],
		[
			'word'    => 'OPERATING STATE',
			'meaning' => 'The specific energetic frequency, emotional posture, and clarity level from which a person perceives, decides, and acts.',
			'zone'    => 'amber',
		],
		[
			'word'    => 'PRESENCE',
			'meaning' => 'The degree of pure conscious attention focused in the exact present moment, undistracted by past charge or future anxiety.',
			'zone'    => 'green',
		],
		[
			'word'    => 'RED ZONE',
			'meaning' => 'The survival operating band characterized by fear, scarcity, reactive emotion, low energy, and feeling controlled by circumstances.',
			'zone'    => 'red',
		],
		[
			'word'    => 'SOVEREIGNTY',
			'meaning' => 'The state of complete spiritual self-ownership, immune to manipulation, false guilt, or subconscious programming.',
			'zone'    => 'gold',
		],
		[
			'word'    => 'SPIRITUAL LITERACY',
			'meaning' => 'The developed capacity to accurately recognize, read, and understand the non-physical spiritual and psychological currents driving human life.',
			'zone'    => 'amber',
		],
		[
			'word'    => 'WARRIOR ERA',
			'meaning' => 'The earliest evolutionary phase of human society, dominated by physical violence, tribal conquest, brute survival, and domination.',
			'zone'    => 'red',
		],
		[
			'word'    => 'WORD OF THE WEEK',
			'meaning' => 'The educational dispatch created to clarify and restore the exact power and meaning of misunderstood words in the English language.',
			'zone'    => '',
		],
		[
			'word'    => 'ZONE',
			'meaning' => 'A distinct operational level of consciousness (Red, Yellow, Green, or Gold) that dictates the reality, emotions, and outcomes an individual experiences.',
			'zone'    => '',
		],
		[
			'word'    => 'ZONE PROFILE',
			'meaning' => 'The diagnostic 14-question assessment that pinpoints the exact Zone from which an individual operates and reveals the specific barriers holding them back.',
			'zone'    => '',
		],
	];

	foreach ($curated as $item) {
		$key = strtoupper(trim($item['word']));
		$all_terms[$key] = [
			'word'    => $key,
			'meaning' => $item['meaning'],
			'zone'    => $item['zone'] ?? '',
			'slug'    => sanitize_title($key),
			'letter'  => strtoupper(substr($key, 0, 1)),
		];
	}

	// 2. Extract terms from post 32 content if published
	$glossary_page = get_page_by_path('glossary');
	if (!$glossary_page) {
		$glossary_page = get_post(32);
	}

	if ($glossary_page && !empty($glossary_page->post_content)) {
		$content = $glossary_page->post_content;
		if (preg_match_all('/<b>([^<:]+):?<\/b>(.*?)(?=<b>|$)/is', $content, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $m) {
				$raw_word = trim(strip_tags($m[1]));
				$raw_word = rtrim($raw_word, ": \t\n\r\0\x0B");
				$key = strtoupper($raw_word);

				$raw_def = trim(strip_tags($m[2], '<i><em>'));
				// Remove part-of-speech prefix (e.g. "n. 1.", "tr. v.")
				$raw_def = preg_replace('/^\s*(?:n\.|tr\.\s*v\.|v\.|adj\.|adv\.)\s*/i', '', $raw_def);
				$raw_def = trim($raw_def);

				if (!empty($key) && !empty($raw_def) && !isset($all_terms[$key])) {
					$all_terms[$key] = [
						'word'    => $key,
						'meaning' => $raw_def,
						'zone'    => '',
						'slug'    => sanitize_title($key),
						'letter'  => strtoupper(substr($key, 0, 1)),
					];
				}
			}
		}
	}

	// 3. Merge custom ACF repeater terms if configured
	if ($glossary_page && function_exists('get_field')) {
		$acf_terms = get_field('terms', $glossary_page->ID);
		if (!empty($acf_terms) && is_array($acf_terms)) {
			foreach ($acf_terms as $row) {
				$w = strtoupper(trim($row['word'] ?? ''));
				$m = trim($row['meaning'] ?? '');
				if (!empty($w) && !empty($m)) {
					$all_terms[$w] = [
						'word'    => $w,
						'meaning' => $m,
						'zone'    => $row['zone'] ?? '',
						'slug'    => sanitize_title($w),
						'letter'  => strtoupper(substr($w, 0, 1)),
					];
				}
			}
		}
	}

	// Sort alphabetically by word
	ksort($all_terms);

	$cached_terms = array_values($all_terms);
	return $cached_terms;
}
