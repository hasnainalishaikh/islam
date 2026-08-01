<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Duas Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Duas & Supplications');
define('META_DESCRIPTION', 'Collection of authentic Duas from Quran and Sunnah. Morning, evening, travel, food, and protection duas with Arabic, Urdu, and English.');

include __DIR__ . '/includes/header.php';

// Duas data organized by category
$duas = [
    'morning' => [
        ['id' => 'm1', 'arabic' => 'اللَّهُمَّ بِكَ أَصْبَحْنَا، وَبِكَ أَمْسَيْنَا، وَبِكَ نَحْيَا، وَبِكَ نَمُوتُ، وَإِلَيْكَ النُّشُورُ', 'transliteration' => 'Allahumma bika asbahna, wa bika amsayna, wa bika nahya, wa bika namutu, wa ilaykan-nushur', 'translation' => 'O Allah, by You we enter the morning and by You we enter the evening, by You we live and by You we die, and to You is the resurrection.', 'urdu' => 'اے اللہ! ہم نے تیرے ہی سہارے صبح کی اور تیرے ہی سہارے شام کی، تیرے ہی سہارے ہم جیتے ہیں اور تیرے ہی سہارے مرتے ہیں، اور تیری ہی طرف پلٹ کر جانا ہے۔', 'source' => 'Abu Dawud'],
        ['id' => 'm2', 'arabic' => 'اللَّهُمَّ إِنِّي أَسْأَلُكَ عِلْمًا نَافِعًا، وَرِزْقًا طَيِّبًا، وَعَمَلًا مُتَقَبَّلًا', 'transliteration' => 'Allahumma inni as\'aluka ilman nafi\'an, wa rizqan tayyiban, wa amalan mutaqabbalan', 'translation' => 'O Allah, I ask You for beneficial knowledge, goodly provision, and accepted deeds.', 'urdu' => 'اے اللہ! میں تجھ سے نفع بخش علم، پاکیزہ رزق اور قبول شدہ عمل کا سوال کرتا ہوں۔', 'source' => 'Ibn Majah'],
    ],
    'evening' => [
        ['id' => 'e1', 'arabic' => 'اللَّهُمَّ بِكَ أَمْسَيْنَا، وَبِكَ أَصْبَحْنَا، وَبِكَ نَحْيَا، وَبِكَ نَمُوتُ، وَإِلَيْكَ الْمَصِيرُ', 'transliteration' => 'Allahumma bika amsayna, wa bika asbahna, wa bika nahya, wa bika namutu, wa ilaykal-masir', 'translation' => 'O Allah, by You we enter the evening and by You we enter the morning, by You we live and by You we die, and to You is the final return.', 'urdu' => 'اے اللہ! ہم نے تیرے ہی سہارے شام کی اور تیرے ہی سہارے صبح کی، تیرے ہی سہارے جیتے ہیں اور تیرے ہی سہارے مرتے ہیں، اور تیری ہی طرف پلٹ کر جانا ہے۔', 'source' => 'Abu Dawud'],
        ['id' => 'e2', 'arabic' => 'أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّاتِ مِنْ شَرِّ مَا خَلَقَ', 'transliteration' => 'A\'udhu bikalimatillahit-tammati min sharri ma khalaq', 'translation' => 'I seek refuge in the perfect words of Allah from the evil of what He has created.', 'urdu' => 'میں اللہ کے مکمل کلمات کی پناہ میں آتا ہوں اس کی مخلوق کے شر سے۔', 'source' => 'Sahih Muslim'],
    ],
    'travel' => [
        ['id' => 't1', 'arabic' => 'سُبْحَانَ الَّذِي سَخَّرَ لَنَا هَذَا وَمَا كُنَّا لَهُ مُقْرِنِينَ، وَإِنَّا إِلَى رَبِّنَا لَمُنْقَلِبُونَ', 'transliteration' => 'Subhanalladhi sakhkhara lana hadha wa ma kunna lahu muqrinin, wa inna ila rabbina lamunqalibun', 'translation' => 'Glory be to Him who has subjected this to us, for we could never have done it by ourselves. And to our Lord we shall surely return.', 'urdu' => 'پاک ہے وہ ذات جس نے اسے ہمارے لیے مسخر کر دیا، اور ہم اسے قابو میں لانے والے نہ تھے، اور بے شک ہم اپنے رب کی طرف پلٹ کر جانے والے ہیں۔', 'source' => 'Surah Az-Zukhruf, 43:13-14'],
        ['id' => 't2', 'arabic' => 'اللَّهُمَّ إِنَّا نَسْأَلُكَ فِي سَفَرِنَا هَذَا الْبِرَّ وَالتَّقْوَى، وَمِنَ الْعَمَلِ مَا تَرْضَى', 'transliteration' => 'Allahumma inna nas\'aluka fi safarina hadhal-birra wat-taqwa, wa minal-amali ma tarda', 'translation' => 'O Allah, we ask You for righteousness and piety in this journey of ours, and for deeds that please You.', 'urdu' => 'اے اللہ! ہم اس سفر میں تجھ سے نیکی اور پرہیزگاری کا سوال کرتے ہیں، اور ان اعمال کا جو تجھے پسند ہوں۔', 'source' => 'Sahih Muslim'],
    ],
    'food' => [
        ['id' => 'f1', 'arabic' => 'بِسْمِ اللَّهِ', 'transliteration' => 'Bismillah', 'translation' => 'In the name of Allah.', 'urdu' => 'اللہ کے نام سے شروع کرتا ہوں۔', 'source' => 'Sahih Bukhari & Muslim'],
        ['id' => 'f2', 'arabic' => 'الْحَمْدُ لِلَّهِ الَّذِي أَطْعَمَنَا وَسَقَانَا، وَجَعَلَنَا مُسْلِمِينَ', 'transliteration' => 'Alhamdulillahil-ladhi at\'amana wa saqana, wa ja\'alana muslimin', 'translation' => 'All praise is due to Allah who has fed us and given us drink, and made us Muslims.', 'urdu' => 'تمام تعریفیں اللہ کے لیے ہیں جس نے ہمیں کھلایا اور پلایا، اور ہمیں مسلمان بنایا۔', 'source' => 'Abu Dawud'],
    ],
    'sleep' => [
        ['id' => 's1', 'arabic' => 'اللَّهُمَّ بِاسْمِكَ أَمُوتُ وَأَحْيَا', 'transliteration' => 'Allahumma bismika amutu wa ahya', 'translation' => 'O Allah, in Your name I die and I live.', 'urdu' => 'اے اللہ! تیرے ہی نام پر میں مرتا ہوں اور جیتا ہوں۔', 'source' => 'Sahih Bukhari & Muslim'],
        ['id' => 's2', 'arabic' => 'اللَّهُمَّ قِنِي عَذَابَكَ يَوْمَ تَبْعَثُ عِبَادَكَ', 'transliteration' => 'Allahumma qini azabaka yawma tab\'athu ibadak', 'translation' => 'O Allah, protect me from Your punishment on the Day You resurrect Your servants.', 'urdu' => 'اے اللہ! مجھے اپنے عذاب سے بچا لے جس دن تو اپنے بندوں کو اٹھائے گا۔', 'source' => 'Sunan Tirmidhi'],
    ],
    'protection' => [
        ['id' => 'p1', 'arabic' => 'أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّةِ مِنْ كُلِّ شَيْطَانٍ وَهَامَّةٍ، وَمِنْ كُلِّ عَيْنٍ لَامَّةٍ', 'transliteration' => 'A\'udhu bikalimatillahit-tammati min kulli shaytanin wa hammatin, wa min kulli aynin lammatin', 'translation' => 'I seek refuge in the perfect words of Allah from every devil and poisonous creature, and from every evil eye.', 'urdu' => 'میں اللہ کے مکمل کلمات کی پناہ میں آتا ہوں ہر شیطان، زہریلے جانور اور ہر نظر بد سے۔', 'source' => 'Sahih Bukhari'],
        ['id' => 'p2', 'arabic' => 'بِسْمِ اللَّهِ الَّذِي لَا يَضُرُّ مَعَ اسْمِهِ شَيْءٌ فِي الْأَرْضِ وَلَا فِي السَّمَاءِ، وَهُوَ السَّمِيعُ الْعَلِيمُ', 'transliteration' => 'Bismillahil-ladhi la yadurru ma\'asmihi shay\'un fil-ardi wa la fis-samai, wa huwas-sami\'ul-alim', 'translation' => 'In the name of Allah, with whose name nothing can harm on earth or in heaven, and He is the All-Hearing, All-Knowing.', 'urdu' => 'اللہ کے نام کے ساتھ جس کے نام کے ساتھ زمین اور آسمان میں کوئی چیز نقصان نہیں پہنچا سکتی، اور وہ سب کچھ سننے والا، جاننے والا ہے۔', 'source' => 'Abu Dawud'],
    ]
];

$categories = [
    'all' => 'All Duas',
    'morning' => 'Morning',
    'evening' => 'Evening',
    'travel' => 'Travel',
    'food' => 'Food',
    'sleep' => 'Sleep',
    'protection' => 'Protection'
];
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Duas & Supplications</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Duas</span>
            </nav>
        </div>
    </div>
</section>

<section class="duas-page">
    <div class="container">
        <!-- Categories -->
        <div class="dua-categories">
            <?php foreach ($categories as $key => $label): ?>
            <button class="dua-category-btn <?php echo $key === 'all' ? 'active' : ''; ?>" data-category="<?php echo $key; ?>">
                <?php echo sanitizeOutput($label); ?>
            </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Duas List -->
        <div class="duas-list">
            <?php foreach ($duas as $category => $categoryDuas): ?>
                <?php foreach ($categoryDuas as $dua): ?>
                <div class="dua-card" data-category="<?php echo $category; ?>">
                    <div class="dua-arabic"><?php echo $dua['arabic']; ?></div>
                    <div class="dua-transliteration"><?php echo sanitizeOutput($dua['transliteration']); ?></div>
                    <div class="dua-translation"><?php echo sanitizeOutput($dua['translation']); ?></div>
                    <div class="dua-translation urdu"><?php echo sanitizeOutput($dua['urdu']); ?></div>
                    <div class="dua-source">
                        <i class="fas fa-book"></i> <?php echo sanitizeOutput($dua['source']); ?>
                    </div>
                    <div class="verse-actions" style="margin-top: 12px;">
                        <button class="verse-action-btn" onclick="copyToClipboard('<?php echo sanitizeOutput($dua['arabic']); ?>')" title="Copy Arabic">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                        <button class="verse-action-btn" onclick="shareContent('<?php echo sanitizeOutput($dua['translation']); ?>')" title="Share">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/duas.js'];
include __DIR__ . '/includes/footer.php';
?>
