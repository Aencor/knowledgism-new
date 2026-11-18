<?php
/**
 * Block Name: Glossary / Glosario
 * Description: Interactive Glossary of Terms and Meanings automatically sorted and grouped by letters (A-Z).
 *
 * @package Knowledgism
 */

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'glossary';
$block_class = !empty($block['className']) ? $block['className'] : '';

$kicker = get_field('kicker') ?: __('Terminology & Principles', 'knowledgism');
$title = get_field('title') ?: __('Glossary of Knowledgism', 'knowledgism');
$lede = get_field('lede') ?: __('Key definitions, operating axioms, and distinctions formulated by Alan C. Walter.', 'knowledgism');
$show_search = get_field('show_search');
if ($show_search === null)
  $show_search = true;
$show_alphabet_nav = get_field('show_alphabet_nav');
if ($show_alphabet_nav === null)
  $show_alphabet_nav = true;

$raw_terms = get_field('terms');

// Curated default Knowledgism terms if no repeater rows are set yet
if (empty($raw_terms) || !is_array($raw_terms)) {
  $raw_terms = [
    [
      'word' => 'Area of Play',
      'meaning' => 'The specific sphere, game, or dimension of life in which an individual chooses to operate, create, and achieve mastery.',
      'zone' => '',
      'notes' => 'Foundational concept in high-performance life architecture.',
    ],
    [
      'word' => 'Axiom',
      'meaning' => 'A self-evident truth or established natural rule that serves as an immovable premise for understanding human ability and mind.',
      'zone' => '',
      'notes' => 'Alan C. Walter research papers (1988–2009).',
    ],
    [
      'word' => 'Beingness',
      'meaning' => 'The state or condition of existing as a conscious, spiritual entity possessing native intent, purpose, and certainty.',
      'zone' => 'green',
      'notes' => 'Differentiated from doingness and havingness.',
    ],
    [
      'word' => 'Certainty',
      'meaning' => 'The state of knowing without reservation, doubt, or external dependency. The natural condition of individuals operating in high Zones.',
      'zone' => 'gold',
      'notes' => 'The opposite of subconscious doubt or anxiety.',
    ],
    [
      'word' => 'Charge',
      'meaning' => 'Stored emotional, mental, or spiritual energy attached to past painful experiences that blinds current perception and saps vitality.',
      'zone' => 'red',
      'notes' => 'Processed and dissolved through Knowledgism facilitation.',
    ],
    [
      'word' => 'Eras of Knowledge',
      'meaning' => 'The three evolutionary epochs of human society: The Warrior Era (coercion and physical force), The Literacy Era (reading, memory, and bureaucracy), and The Knowledge Era (spiritual certainty and applied truth).',
      'zone' => '',
      'notes' => 'First formulated in 1994.',
    ],
    [
      'word' => 'Fixed Mindset',
      'meaning' => 'A defensive, calcified orientation born of past uninspected failures and charge, where an individual refuses to perceive new data or expand.',
      'zone' => 'red',
      'notes' => 'A major barrier to higher-zone living.',
    ],
    [
      'word' => 'Gold Zone',
      'meaning' => 'The highest operating band in the Zones of Life—the Golden Magic Zone characterized by effortless manifestation, joyful play, pure sovereignty, and total abundance.',
      'zone' => 'gold',
      'notes' => 'Level 4 mastery materials.',
    ],
    [
      'word' => 'Green Zone',
      'meaning' => 'The operating band of expansion, high competence, ethical leadership, strategic clarity, and joyful accomplishment.',
      'zone' => 'green',
      'notes' => 'Level 3 advanced materials.',
    ],
    [
      'word' => 'Knowledge Era',
      'meaning' => 'The emerging epoch in which conscious individuals discover who they are, dissolve subconscious limitations, and operate as sovereign creators rather than pawns.',
      'zone' => '',
      'notes' => 'The central mission of Knowledgism.',
    ],
    [
      'word' => 'Knowledgism',
      'meaning' => 'The comprehensive body of research, philosophy, and applied spiritual technology developed by Alan C. Walter to unlock human potential, power, and wealth.',
      'zone' => '',
      'notes' => 'Formally codified from 1980 to 2009.',
    ],
    [
      'word' => 'Life Force',
      'meaning' => 'The fundamental spiritual vitality that animates existence. The fuel behind intention, creativity, charisma, and personal presence.',
      'zone' => 'green',
      'notes' => 'Directly linked to one\'s current operating Zone.',
    ],
    [
      'word' => 'Literacy Era',
      'meaning' => 'The civilization period preceding the Knowledge Era, founded upon written legalisms, rote memorization, and institutional dependence.',
      'zone' => '',
      'notes' => 'Transition era between brute force and true knowing.',
    ],
    [
      'word' => 'Omni-Sovereignty',
      'meaning' => 'Total, uncompromised spiritual self-governance and harmonious command over one\'s thoughts, purposes, and relationship to the universe.',
      'zone' => 'gold',
      'notes' => 'Level 4 Mastery state.',
    ],
    [
      'word' => 'Operating State',
      'meaning' => 'The specific energetic frequency, emotional posture, and clarity level from which a person perceives, decides, and acts.',
      'zone' => 'amber',
      'notes' => 'Measured and addressed via the Zone Profile.',
    ],
    [
      'word' => 'Presence',
      'meaning' => 'The degree of pure conscious attention focused in the exact present moment, undistracted by past charge or future anxiety.',
      'zone' => 'green',
      'notes' => 'A primary multiplier of power and communication.',
    ],
    [
      'word' => 'Red Zone',
      'meaning' => 'The survival operating band characterized by fear, scarcity, reactive emotion, low energy, and feeling controlled by circumstances.',
      'zone' => 'red',
      'notes' => 'Level 1 entry-level books address exiting this zone.',
    ],
    [
      'word' => 'Sovereignty',
      'meaning' => 'The state of complete spiritual self-ownership, immune to manipulation, false guilt, or subconscious programming.',
      'zone' => 'gold',
      'notes' => 'The ultimate outcome of genuine facilitation.',
    ],
    [
      'word' => 'Spiritual Literacy',
      'meaning' => 'The developed capacity to accurately recognize, read, and understand the non-physical spiritual and psychological currents driving human life.',
      'zone' => 'amber',
      'notes' => 'Intermediate study track.',
    ],
    [
      'word' => 'Warrior Era',
      'meaning' => 'The earliest evolutionary phase of human society, dominated by physical violence, tribal conquest, brute survival, and domination.',
      'zone' => 'red',
      'notes' => 'The baseline from which civilization evolved.',
    ],
    [
      'word' => 'Word of the Week',
      'meaning' => 'The educational dispatch created to clarify and restore the exact power and meaning of misunderstood words in the English language.',
      'zone' => '',
      'notes' => 'Weekly newsletter published by Alan C. Walter.',
    ],
    [
      'word' => 'Zone',
      'meaning' => 'A distinct operational level of consciousness (Red, Yellow, Green, or Gold) that dictates the reality, emotions, and outcomes an individual experiences.',
      'zone' => '',
      'notes' => 'The foundational paradigm of Knowledgism.',
    ],
    [
      'word' => 'Zone Profile',
      'meaning' => 'The diagnostic 14-question assessment that pinpoints the exact Zone from which an individual operates and reveals the specific barriers holding them back.',
      'zone' => '',
      'notes' => 'Free diagnostic at app.zonesoflife.com.',
    ],
  ];
}

// Group terms by first letter (A-Z)
$grouped_terms = [];
$total_terms_count = 0;

foreach ($raw_terms as $item) {
  $word = trim($item['word'] ?? '');
  if (empty($word))
    continue;

  $first_char = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');

  // Transliterate accented letters (Á -> A, É -> E, Ñ -> N, etc.)
  $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $first_char);
  $letter = (!empty($normalized) && ctype_alpha($normalized[0])) ? strtoupper($normalized[0]) : (ctype_alpha($first_char) ? $first_char : '#');

  if (!isset($grouped_terms[$letter])) {
    $grouped_terms[$letter] = [];
  }
  $grouped_terms[$letter][] = $item;
  $total_terms_count++;
}

// Sort letter groups alphabetically (# at the end)
uksort($grouped_terms, function ($a, $b) {
  if ($a === '#')
    return 1;
  if ($b === '#')
    return -1;
  return strcmp($a, $b);
});

// Sort terms within each letter group alphabetically
foreach ($grouped_terms as $letter => &$items_in_letter) {
  usort($items_in_letter, function ($a, $b) {
    return strcasecmp(trim($a['word'] ?? ''), trim($b['word'] ?? ''));
  });
}
unset($items_in_letter);

// Alphabet for navigation bar
$alphabet = range('A', 'Z');
if (isset($grouped_terms['#'])) {
  $alphabet[] = '#';
}
?>

<div id="<?php echo esc_attr($block_id); ?>" class="block glossary-block <?php echo esc_attr($block_class); ?>">
  <div class="wrap">
    <!-- Section Header -->
    <div class="glossary-header">
      <div class="glossary-header-content">
        <?php if ($kicker): ?>
          <div class="kicker"><?php echo esc_html($kicker); ?></div>
        <?php endif; ?>
        <h2 class="mt8"
          style="font-size:clamp(30px, 3.5vw, 42px);font-family:var(--display);font-weight:700;line-height:1.2;">
          <?php echo esc_html($title); ?>
        </h2>
        <?php if ($lede): ?>
          <p class="lede mt16" style="max-width:65ch;color:#4A5764;font-size:17px;line-height:1.65;">
            <?php echo esc_html($lede); ?>
          </p>
        <?php endif; ?>
      </div>

      <!-- Live Search Box -->
      <?php if ($show_search): ?>
        <div class="glossary-search-wrap">
          <div class="glossary-search-box">
            <svg class="ic glossary-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
            <input type="search" class="glossary-search-input" id="glossarySearchInput-<?php echo esc_attr($block_id); ?>"
              placeholder="<?php esc_attr_e('Search words or definitions...', 'knowledgism'); ?>"
              aria-label="<?php esc_attr_e('Search glossary', 'knowledgism'); ?>" />
            <button type="button" class="glossary-search-clear"
              id="glossarySearchClear-<?php echo esc_attr($block_id); ?>" style="display:none;"
              title="<?php esc_attr_e('Clear search', 'knowledgism'); ?>">&times;</button>
          </div>
          <div class="glossary-counter-badge" id="glossaryCounter-<?php echo esc_attr($block_id); ?>">
            <?php echo sprintf(esc_html__('Showing %d terms', 'knowledgism'), $total_terms_count); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Alphabet Quick-Jump Navigation Bar -->
    <?php if ($show_alphabet_nav): ?>
      <nav class="glossary-nav-bar" aria-label="<?php esc_attr_e('Glossary alphabet jump', 'knowledgism'); ?>">
        <div class="glossary-nav-inner">
          <button type="button" class="glossary-nav-pill all-pill active" data-target-letter="all">
            <?php esc_html_e('All', 'knowledgism'); ?>
          </button>
          <?php foreach ($alphabet as $let):
            $has_terms = isset($grouped_terms[$let]);
            $count = $has_terms ? count($grouped_terms[$let]) : 0;
            ?>
            <?php if ($has_terms): ?>
              <a href="#letter-<?php echo esc_attr($let); ?>-<?php echo esc_attr($block_id); ?>"
                class="glossary-nav-pill has-terms" data-target-letter="<?php echo esc_attr($let); ?>"
                title="<?php echo sprintf(esc_attr__('Letter %s (%d terms)', 'knowledgism'), $let, $count); ?>">
                <span><?php echo esc_html($let); ?></span>
                <span class="pill-count"><?php echo $count; ?></span>
              </a>
            <?php else: ?>
              <span class="glossary-nav-pill no-terms" data-target-letter="<?php echo esc_attr($let); ?>">
                <?php echo esc_html($let); ?>
              </span>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </nav>
    <?php endif; ?>

    <!-- Glossary Letter Sections Container -->
    <div class="glossary-content-wrap" id="glossaryContent-<?php echo esc_attr($block_id); ?>">
      <?php if (!empty($grouped_terms)): ?>
        <?php foreach ($grouped_terms as $letter => $items): ?>
          <section class="glossary-letter-group"
            id="letter-<?php echo esc_attr($letter); ?>-<?php echo esc_attr($block_id); ?>"
            data-letter="<?php echo esc_attr($letter); ?>">
            <!-- Letter Divider & Header -->
            <div class="glossary-letter-header">
              <div class="glossary-letter-badge"><?php echo esc_html($letter); ?></div>
              <div class="glossary-letter-line"></div>
              <div class="glossary-letter-meta">
                <span class="letter-count-num"><?php echo count($items); ?></span>
                <span class="tiny"
                  style="color:var(--muted);"><?php echo count($items) === 1 ? esc_html__('word', 'knowledgism') : esc_html__('words', 'knowledgism'); ?></span>
              </div>
            </div>

            <!-- Grid of Word & Meaning Cards for this letter -->
            <div class="glossary-cards-grid">
              <?php foreach ($items as $item):
                $term_word = trim($item['word'] ?? '');
                $term_meaning = trim($item['meaning'] ?? '');
                $term_zone = trim($item['zone'] ?? '');
                $term_notes = trim($item['notes'] ?? ($item['example'] ?? ''));

                $zone_badge_bg = '#1F3A5F';
                $zone_badge_name = '';
                if ($term_zone === 'red') {
                  $zone_badge_bg = 'var(--red)';
                  $zone_badge_name = __('Red Zone', 'knowledgism');
                } elseif ($term_zone === 'amber') {
                  $zone_badge_bg = 'var(--amber)';
                  $zone_badge_name = __('Yellow Zone', 'knowledgism');
                } elseif ($term_zone === 'green') {
                  $zone_badge_bg = 'var(--green)';
                  $zone_badge_name = __('Green Zone', 'knowledgism');
                } elseif ($term_zone === 'gold') {
                  $zone_badge_bg = '#C9A44B';
                  $zone_badge_name = __('Gold Zone', 'knowledgism');
                }
                ?>
                <article class="card glossary-term-card" data-word="<?php echo esc_attr(mb_strtolower($term_word)); ?>"
                  data-meaning="<?php echo esc_attr(mb_strtolower(wp_strip_all_tags($term_meaning))); ?>">
                  <div class="glossary-term-header">
                    <h3 class="glossary-term-title"><?php echo esc_html($term_word); ?></h3>
                    <?php if ($zone_badge_name): ?>
                      <span class="glossary-zone-chip" style="background:<?php echo esc_attr($zone_badge_bg); ?>;">
                        <?php echo esc_html($zone_badge_name); ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <div class="glossary-term-meaning">
                    <?php echo wpautop(wp_kses_post($term_meaning)); ?>
                  </div>

                  <?php if ($term_notes): ?>
                    <div class="glossary-term-notes">
                      <span class="tiny"
                        style="color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;"><?php esc_html_e('Reference / Context', 'knowledgism'); ?></span>
                      <p class="xs mt4" style="color:#5A6875;line-height:1.5;"><?php echo esc_html($term_notes); ?></p>
                    </div>
                  <?php endif; ?>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endforeach; ?>

        <!-- Empty Search State -->
        <div class="card pad-lg center glossary-empty-state" id="glossaryEmptyState-<?php echo esc_attr($block_id); ?>"
          style="display:none;padding:50px 24px;text-align:center;">
          <svg class="ic" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
          </svg>
          <h4 style="font-size:18px;color:var(--ink);"><?php esc_html_e('No glossary terms found', 'knowledgism'); ?></h4>
          <p class="sm mt8" style="color:var(--muted);">
            <?php esc_html_e('We couldn\'t find any term or definition matching your search.', 'knowledgism'); ?></p>
          <button type="button" class="btn btn-ghost btn-sm mt16"
            id="glossaryResetSearchBtn-<?php echo esc_attr($block_id); ?>" style="margin-top:16px;">
            <?php esc_html_e('Reset Search', 'knowledgism'); ?>
          </button>
        </div>
      <?php else: ?>
        <div class="card pad-lg center">
          <p class="sm"><?php esc_html_e('No glossary words added yet. Add terms in the block editor.', 'knowledgism'); ?>
          </p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function () {
    const blockId = '<?php echo esc_js($block_id); ?>';
    const root = document.getElementById(blockId);
    if (!root) return;

    const searchInput = document.getElementById('glossarySearchInput-' + blockId);
    const searchClear = document.getElementById('glossarySearchClear-' + blockId);
    const counterBadge = document.getElementById('glossaryCounter-' + blockId);
    const emptyState = document.getElementById('glossaryEmptyState-' + blockId);
    const resetBtn = document.getElementById('glossaryResetSearchBtn-' + blockId);
    const navPills = root.querySelectorAll('.glossary-nav-pill');
    const letterGroups = root.querySelectorAll('.glossary-letter-group');
    const termCards = root.querySelectorAll('.glossary-term-card');
    const totalCount = <?php echo intval($total_terms_count); ?>;

    function filterGlossary() {
      const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
      if (searchClear) {
        searchClear.style.display = query.length > 0 ? 'inline-block' : 'none';
      }

      let visibleTermsCount = 0;

      letterGroups.forEach(group => {
        let visibleInGroup = 0;
        const cardsInGroup = group.querySelectorAll('.glossary-term-card');

        cardsInGroup.forEach(card => {
          const word = card.getAttribute('data-word') || '';
          const meaning = card.getAttribute('data-meaning') || '';

          const matches = (query === '' || word.includes(query) || meaning.includes(query));
          card.style.display = matches ? 'flex' : 'none';

          if (matches) {
            visibleInGroup++;
            visibleTermsCount++;
          }
        });

        // Show/hide entire letter section
        group.style.display = visibleInGroup > 0 ? 'block' : 'none';

        // Update count in letter header
        const countEl = group.querySelector('.letter-count-num');
        if (countEl) countEl.textContent = visibleInGroup;

        // Update nav pill status
        const letter = group.getAttribute('data-letter');
        const pill = root.querySelector('.glossary-nav-pill[data-target-letter="' + letter + '"]');
        if (pill) {
          if (visibleInGroup > 0) {
            pill.classList.remove('dimmed-search');
            const pillCount = pill.querySelector('.pill-count');
            if (pillCount) pillCount.textContent = visibleInGroup;
          } else {
            pill.classList.add('dimmed-search');
          }
        }
      });

      if (counterBadge) {
        counterBadge.textContent = 'Showing ' + visibleTermsCount + ' ' + (visibleTermsCount === 1 ? 'term' : 'terms');
      }

      if (emptyState) {
        emptyState.style.display = visibleTermsCount === 0 ? 'block' : 'none';
      }
    }

    if (searchInput) {
      searchInput.addEventListener('input', filterGlossary);
    }

    if (searchClear) {
      searchClear.addEventListener('click', function () {
        if (searchInput) {
          searchInput.value = '';
          searchInput.focus();
          filterGlossary();
        }
      });
    }

    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        if (searchInput) {
          searchInput.value = '';
          filterGlossary();
        }
      });
    }

    // Smooth scroll and active state for alphabet nav
    navPills.forEach(pill => {
      pill.addEventListener('click', function (e) {
        const targetLetter = this.getAttribute('data-target-letter');

        navPills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');

        if (targetLetter === 'all') {
          if (searchInput && searchInput.value) {
            searchInput.value = '';
            filterGlossary();
          }
          window.scrollTo({
            top: root.getBoundingClientRect().top + window.pageYOffset - 80,
            behavior: 'smooth'
          });
          return;
        }

        const targetGroup = document.getElementById('letter-' + targetLetter + '-' + blockId);
        if (targetGroup) {
          e.preventDefault();
          const navHeight = 90;
          const targetPos = targetGroup.getBoundingClientRect().top + window.pageYOffset - navHeight;
          window.scrollTo({
            top: targetPos,
            behavior: 'smooth'
          });
        }
      });
    });
  })();
</script>