<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Hadith Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Hadith Collection');
define('META_DESCRIPTION', 'Explore the authentic Hadith collection. Search, save favorites, and share hadiths with others.');

include __DIR__ . '/includes/header.php';

// Hadith data
$hadiths = [
    [
        'id' => 1,
        'text' => 'Actions are judged by intentions, and everyone will be rewarded according to their intention.',
        'narrator' => 'Umar ibn Al-Khattab',
        'source' => 'Sahih Bukhari & Muslim',
        'category' => 'faith',
        'book' => 'Sahih Bukhari, Book 1, Hadith 1'
    ],
    [
        'id' => 2,
        'text' => 'The best of you are those who are best to their families, and I am the best to my family.',
        'narrator' => 'Aisha (RA)',
        'source' => 'Sunan Tirmidhi',
        'category' => 'family',
        'book' => 'Tirmidhi, Book 47, Hadith 3895'
    ],
    [
        'id' => 3,
        'text' => 'Whoever believes in Allah and the Last Day, let him speak good or remain silent.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Bukhari & Muslim',
        'category' => 'character',
        'book' => 'Sahih Bukhari, Book 78, Hadith 47'
    ],
    [
        'id' => 4,
        'text' => 'None of you truly believes until he loves for his brother what he loves for himself.',
        'narrator' => 'Anas ibn Malik',
        'source' => 'Sahih Bukhari & Muslim',
        'category' => 'faith',
        'book' => 'Sahih Bukhari, Book 2, Hadith 12'
    ],
    [
        'id' => 5,
        'text' => 'The strong is not the one who overcomes people by his strength, but the strong is the one who controls himself while in anger.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Bukhari & Muslim',
        'category' => 'character',
        'book' => 'Sahih Bukhari, Book 78, Hadith 105'
    ],
    [
        'id' => 6,
        'text' => 'Whoever does not show mercy to the young nor honor the elders is not one of us.',
        'narrator' => 'Abdullah ibn Amr',
        'source' => 'Sunan Tirmidhi',
        'category' => 'family',
        'book' => 'Tirmidhi, Book 27, Hadith 1920'
    ],
    [
        'id' => 7,
        'text' => 'The best charity is that which is given when one is in need, striving to earn a living, and fearing poverty.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Bukhari',
        'category' => 'charity',
        'book' => 'Sahih Bukhari, Book 24, Hadith 48'
    ],
    [
        'id' => 8,
        'text' => 'Make things easy and do not make things difficult. Give glad tidings and do not repel people.',
        'narrator' => 'Anas ibn Malik',
        'source' => 'Sahih Bukhari',
        'category' => 'character',
        'book' => 'Sahih Bukhari, Book 69, Hadith 11'
    ],
    [
        'id' => 9,
        'text' => 'The best of you is the one who learns the Quran and teaches it.',
        'narrator' => 'Uthman ibn Affan',
        'source' => 'Sahih Bukhari',
        'category' => 'quran',
        'book' => 'Sahih Bukhari, Book 66, Hadith 15'
    ],
    [
        'id' => 10,
        'text' => 'A good word is charity.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Bukhari & Muslim',
        'category' => 'charity',
        'book' => 'Sahih Bukhari, Book 56, Hadith 52'
    ],
    [
        'id' => 11,
        'text' => 'Whoever relieves a believer\'s distress of the distressful aspects of this world, Allah will rescue him from the difficulties of the Hereafter.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Muslim',
        'category' => 'faith',
        'book' => 'Sahih Muslim, Book 32, Hadith 6250'
    ],
    [
        'id' => 12,
        'text' => 'The heaviest thing to be placed in the balance of a believer on the Day of Resurrection is good character.',
        'narrator' => 'Abu Darda',
        'source' => 'Sunan Tirmidhi',
        'category' => 'character',
        'book' => 'Tirmidhi, Book 27, Hadith 2002'
    ],
    [
        'id' => 13,
        'text' => 'When a person dies, his deeds come to an end except for three: ongoing charity, beneficial knowledge, or a righteous child who prays for him.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Muslim',
        'category' => 'charity',
        'book' => 'Sahih Muslim, Book 25, Hadith 4223'
    ],
    [
        'id' => 14,
        'text' => 'The prayer of a person who does not recite the Quran is incomplete.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sahih Muslim',
        'category' => 'quran',
        'book' => 'Sahih Muslim, Book 4, Hadith 773'
    ],
    [
        'id' => 15,
        'text' => 'The most perfect of believers in faith are those with the best character.',
        'narrator' => 'Abu Hurairah',
        'source' => 'Sunan Tirmidhi',
        'category' => 'faith',
        'book' => 'Tirmidhi, Book 27, Hadith 1162'
    ]
];

$categories = [
    'all' => 'All Hadiths',
    'faith' => 'Faith & Belief',
    'character' => 'Character & Manners',
    'family' => 'Family & Society',
    'charity' => 'Charity & Deeds',
    'quran' => 'Quran & Knowledge'
];
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Hadith Collection</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Hadith</span>
            </nav>
        </div>
    </div>
</section>

<section class="hadith-page">
    <div class="container">
        <!-- Search -->
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="hadithSearch" placeholder="Search hadiths..." aria-label="Search hadiths">
        </div>
        
        <!-- Categories -->
        <div class="hadith-categories">
            <?php foreach ($categories as $key => $label): ?>
            <button class="hadith-category-btn <?php echo $key === 'all' ? 'active' : ''; ?>" data-category="<?php echo $key; ?>">
                <?php echo sanitizeOutput($label); ?>
            </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Hadith Cards -->
        <div class="hadith-list" id="hadithList">
            <?php foreach ($hadiths as $hadith): ?>
            <div class="hadith-card" data-category="<?php echo $hadith['category']; ?>" data-id="<?php echo $hadith['id']; ?>">
                <div class="hadith-text">"<?php echo sanitizeOutput($hadith['text']); ?>"</div>
                <div class="hadith-narrator">— Narrated by <?php echo sanitizeOutput($hadith['narrator']); ?></div>
                <div class="d-flex" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div class="hadith-source">
                        <i class="fas fa-book"></i>
                        <?php echo sanitizeOutput($hadith['source']); ?>
                    </div>
                    <div class="verse-actions">
                        <button class="verse-action-btn hadith-favorite-btn" data-id="<?php echo $hadith['id']; ?>" title="Save to favorites">
                            <i class="far fa-heart"></i>
                        </button>
                        <button class="verse-action-btn hadith-share-btn" data-text="<?php echo sanitizeOutput($hadith['text']); ?>" title="Share">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <button class="verse-action-btn" onclick="copyToClipboard('<?php echo sanitizeOutput($hadith['text']); ?>')" title="Copy">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/hadith.js'];
include __DIR__ . '/includes/footer.php';
?>
