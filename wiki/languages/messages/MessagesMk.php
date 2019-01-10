<?php
/** Macedonian (македонски)
 *
 * To improve a translation please visit https://translatewiki.net
 *
 * @ingroup Language
 * @file
 *
 * @author Bjankuloski06
 * @author Brainmachine
 * @author Brest
 * @author Brest2008
 * @author FlavrSavr
 * @author Glupav
 * @author INkubusse
 * @author Kaganer
 * @author Misos
 * @author Rancher
 * @author Spacebirdy
 * @author Urhixidur
 * @author לערי ריינהארט
 */

$namespaceNames = array(
	NS_MEDIA            => 'Медиум',
	NS_SPECIAL          => 'Специјална',
	NS_TALK             => 'Разговор',
	NS_USER             => 'Корисник',
	NS_USER_TALK        => 'Разговор_со_корисник',
	NS_PROJECT_TALK     => 'Разговор_за_$1',
	NS_FILE             => 'Податотека',
	NS_FILE_TALK        => 'Разговор_за_податотека',
	NS_MEDIAWIKI        => 'МедијаВики',
	NS_MEDIAWIKI_TALK   => 'Разговор_за_МедијаВики',
	NS_TEMPLATE         => 'Шаблон',
	NS_TEMPLATE_TALK    => 'Разговор_за_шаблон',
	NS_HELP             => 'Помош',
	NS_HELP_TALK        => 'Разговор_за_помош',
	NS_CATEGORY         => 'Категорија',
	NS_CATEGORY_TALK    => 'Разговор_за_категорија',
);

$namespaceAliases = array(
	'Медија'            => NS_MEDIA,
	'Специјални'        => NS_SPECIAL,
	'Слика'             => NS_FILE,
	'Разговор_за_слика' => NS_FILE_TALK,
);


$datePreferences = array(
	'default',
	'dmy mk',
	'ymd mk',
	'ymdt mk',
	'mdy',
	'dmy',
	'ymd',
	'ISO 8601',
);

$defaultDateFormat = 'dmy or mdy';

$dateFormats = array(
	'dmy mk time' => 'H:i',
	'dmy mk date' => 'j.m.Y',
	'dmy mk both' => 'H:i, j.m.Y',

	'ymd mk time' => 'H:i',
	'ymd mk date' => 'Y.m.j',
	'ymd mk both' => 'H:i, Y.m.j',

	'ymdt mk time' => 'H:i:s',
	'ymdt mk date' => 'Y.m.j',
	'ymdt mk both' => 'Y.m.j, H:i:s',

	'mdy time' => 'H:i',
	'mdy date' => 'F j, Y',
	'mdy both' => 'H:i, F j, Y',

	'dmy time' => 'H:i',
	'dmy date' => 'j F Y',
	'dmy both' => 'H:i, j F Y',

	'ymd time' => 'H:i',
	'ymd date' => 'Y F j',
	'ymd both' => 'H:i, Y F j',

	'ISO 8601 time' => 'xnH:xni:xns',
	'ISO 8601 date' => 'xnY-xnm-xnd',
	'ISO 8601 both' => 'xnY-xnm-xnd"T"xnH:xni:xns',
);

$specialPageAliases = array(
	'Activeusers'               => array( 'АктивниКорисници' ),
	'Allmessages'               => array( 'СитеПораки' ),
	'AllMyUploads'              => array( 'СитеМоиПодигања' ),
	'Allpages'                  => array( 'СитеСтраници' ),
	'ApiHelp'                   => array( 'ИзвршникПомош' ),
	'Ancientpages'              => array( 'НајстариСтраници' ),
	'Badtitle'                  => array( 'Лошнаслов' ),
	'Blankpage'                 => array( 'ПразнаСтраница' ),
	'Block'                     => array( 'Блокирање', 'БлокIP', 'БлокирајКорисник' ),
	'Booksources'               => array( 'ПечатенИзвор' ),
	'BrokenRedirects'           => array( 'ПрекинатиПренасочувања' ),
	'Categories'                => array( 'Категории' ),
	'ChangeEmail'               => array( 'СмениЕ-пошта' ),
	'ChangePassword'            => array( 'СмениЛозинка' ),
	'ComparePages'              => array( 'СпоредиСтраници' ),
	'Confirmemail'              => array( 'Потврди_е-пошта' ),
	'Contributions'             => array( 'Придонеси' ),
	'CreateAccount'             => array( 'СоздајКорисничкаСметка' ),
	'Deadendpages'              => array( 'СлепиСтраници' ),
	'DeletedContributions'      => array( 'ИзбришаниПридонеси' ),
	'Diff'                      => array( 'Разлики' ),
	'DoubleRedirects'           => array( 'ДвојниПренасочувања' ),
	'EditWatchlist'             => array( 'УредиНабљудувања' ),
	'Emailuser'                 => array( 'Пиши_е-пошта_на_корисникот' ),
	'ExpandTemplates'           => array( 'ПрошириШаблони' ),
	'Export'                    => array( 'Извоз' ),
	'Fewestrevisions'           => array( 'НајмалкуПреработки' ),
	'FileDuplicateSearch'       => array( 'ПребарувањеДупликатПодатотека' ),
	'Filepath'                  => array( 'ПатДоПодатотека' ),
	'Import'                    => array( 'Увоз' ),
	'Invalidateemail'           => array( 'ПогрешнаЕпошта' ),
	'JavaScriptTest'            => array( 'ПробаНаJavaСкрипта' ),
	'BlockList'                 => array( 'СписокНаБлокираниIP' ),
	'LinkSearch'                => array( 'ПребарајВрска' ),
	'Listadmins'                => array( 'СписокНаАдминистратори' ),
	'Listbots'                  => array( 'СписокНаБотови' ),
	'Listfiles'                 => array( 'СписокНаПодатотеки', 'СписокНаСлики' ),
	'Listgrouprights'           => array( 'СписокНаГрупниПрава' ),
	'Listredirects'             => array( 'СписокНаПренасочувања' ),
	'ListDuplicatedFiles'       => array( 'ИспишиДуплираниПодатотеки' ),
	'Listusers'                 => array( 'СписокНаКорисници', 'СписокКорисници' ),
	'Lockdb'                    => array( 'ЗаклучиБаза' ),
	'Log'                       => array( 'Дневник', 'Дневници' ),
	'Lonelypages'               => array( 'ОсамениСтраници', 'СтранциСирачиња' ),
	'Longpages'                 => array( 'ДолгиСтраници' ),
	'MediaStatistics'           => array( 'МедиумскиСтатистики' ),
	'MergeHistory'              => array( 'СпојувањеИсторија' ),
	'MIMEsearch'                => array( 'MIMEПребарување' ),
	'Mostcategories'            => array( 'НајмногуКатегории' ),
	'Mostimages'                => array( 'НајмногуСлики', 'НајмногуПодатотеки', 'ПодатотекиСоНајмногуВрски' ),
	'Mostinterwikis'            => array( 'НајмногуМеѓувикија' ),
	'Mostlinked'                => array( 'СоНајмногуВрски', 'СтранициСоНајмногуВрски' ),
	'Mostlinkedcategories'      => array( 'НајупотребуваниКатегории' ),
	'Mostlinkedtemplates'       => array( 'НајупотребуваниШаблони' ),
	'Mostrevisions'             => array( 'НајмногуПреработки' ),
	'Movepage'                  => array( 'ПреместиСтраница' ),
	'Mycontributions'           => array( 'МоиПридонеси' ),
	'MyLanguage'                => array( 'МојЈазик' ),
	'Mypage'                    => array( 'МојаСтраница' ),
	'Mytalk'                    => array( 'МојРазговор', 'МоиРазговори' ),
	'Myuploads'                 => array( 'МоиПодигања' ),
	'Newimages'                 => array( 'НовиСлики', 'НовиПодатотеки' ),
	'Newpages'                  => array( 'НовиСтраници' ),
	'PagesWithProp'             => array( 'СтранициСоСвојство' ),
	'PageLanguage'              => array( 'ЈазикНаСтраницата' ),
	'PasswordReset'             => array( 'ПроменаНаЛозинка' ),
	'PermanentLink'             => array( 'ПостојанаВрска' ),

	'Preferences'               => array( 'Нагодувања' ),
	'Prefixindex'               => array( 'ИндексНаПретставки' ),
	'Protectedpages'            => array( 'ЗаштитениСтраници' ),
	'Protectedtitles'           => array( 'ЗаштитениНаслови' ),
	'Randompage'                => array( 'Случајна', 'СлучајнаСтраница' ),
	'RandomInCategory'          => array( 'СлучајнаВоКатегорија' ),
	'Randomredirect'            => array( 'СлучајноПренасочување' ),
	'Recentchanges'             => array( 'СкорешниПромени' ),
	'Recentchangeslinked'       => array( 'ПоврзаниПромени' ),
	'Redirect'                  => array( 'Пренасочување' ),
	'ResetTokens'               => array( 'ВратиОдновоЗнаци' ),
	'Revisiondelete'            => array( 'БришењеПреработка' ),
	'RunJobs'                   => array( 'ПуштиЗадачи' ),
	'Search'                    => array( 'Барај' ),
	'Shortpages'                => array( 'КраткиСтраници' ),
	'Specialpages'              => array( 'СлужбениСтраници' ),
	'Statistics'                => array( 'Статистики' ),
	'Tags'                      => array( 'Oзнаки', 'Приврзоци' ),
	'TrackingCategories'        => array( 'КатегорииЗаСледење' ),
	'Unblock'                   => array( 'Одблокирај' ),
	'Uncategorizedcategories'   => array( 'НекатегоризираниКатегории' ),
	'Uncategorizedimages'       => array( 'НекатегоризираниСлики' ),
	'Uncategorizedpages'        => array( 'НекатегоризираниСтраници' ),
	'Uncategorizedtemplates'    => array( 'НекатегоризираниШаблони' ),
	'Undelete'                  => array( 'Врати' ),
	'Unlockdb'                  => array( 'ОтклучиБаза' ),
	'Unusedcategories'          => array( 'НеискористениКатегории' ),
	'Unusedimages'              => array( 'НеискористениСлики', 'НеискористениПодатотеки' ),
	'Unusedtemplates'           => array( 'НеискористениШаблони' ),
	'Unwatchedpages'            => array( 'НенабљудуваниСтраници' ),
	'Upload'                    => array( 'Подигање' ),
	'UploadStash'               => array( 'СкриениПодигања' ),
	'Userlogin'                 => array( 'Најавување' ),
	'Userlogout'                => array( 'Одјавување' ),
	'Userrights'                => array( 'КорисничкиПрава' ),
	'Version'                   => array( 'Верзија' ),
	'Wantedcategories'          => array( 'ПотребниКатегории' ),
	'Wantedfiles'               => array( 'ПотребниПодатотеки' ),
	'Wantedpages'               => array( 'ПотребниСтраници' ),
	'Wantedtemplates'           => array( 'ПотребниШаблони' ),
	'Watchlist'                 => array( 'СписокНаНабљудувања' ),
	'Whatlinkshere'             => array( 'ШтоВодиОвде' ),
	'Withoutinterwiki'          => array( 'БезМеѓувики' ),
);

$magicWords = array(
	'redirect'                  => array( '0', '#пренасочување', '#види', '#Пренасочување', '#ПРЕНАСОЧУВАЊЕ', '#REDIRECT' ),
	'notoc'                     => array( '0', '__БЕЗСОДРЖИНА__', '__NOTOC__' ),
	'nogallery'                 => array( '0', '__БЕЗГАЛЕРИЈА__', '__NOGALLERY__' ),
	'forcetoc'                  => array( '0', '__СОСОДРЖИНА__', '__FORCETOC__' ),
	'toc'                       => array( '0', '__СОДРЖИНА__', '__TOC__' ),
	'noeditsection'             => array( '0', '__БЕЗ_УРЕДУВАЊЕ_НА_ПОДНАСЛОВИ__', '__NOEDITSECTION__' ),
	'currentmonth'              => array( '1', 'ТЕКОВЕНМЕСЕЦ', 'CURRENTMONTH', 'CURRENTMONTH2' ),
	'currentmonth1'             => array( '1', 'ТЕКОВЕНМЕСЕЦ1', 'CURRENTMONTH1' ),
	'currentmonthname'          => array( '1', 'ТЕКОВЕНМЕСЕЦИМЕ', 'CURRENTMONTHNAME' ),
	'currentmonthnamegen'       => array( '1', 'ТЕКОВЕНМЕСЕЦИМЕРОД', 'CURRENTMONTHNAMEGEN' ),
	'currentmonthabbrev'        => array( '1', 'ТЕКОВЕНМЕСЕЦСКР', 'CURRENTMONTHABBREV' ),
	'currentday'                => array( '1', 'ТЕКОВЕНДЕН', 'CURRENTDAY' ),
	'currentday2'               => array( '1', 'ТЕКОВЕНДЕН2', 'CURRENTDAY2' ),
	'currentdayname'            => array( '1', 'ТЕКОВЕНДЕНИМЕ', 'CURRENTDAYNAME' ),
	'currentyear'               => array( '1', 'ТЕКОВНАГОДИНА', 'CURRENTYEAR' ),
	'currenttime'               => array( '1', 'ТЕКОВНОВРЕМЕ', 'CURRENTTIME' ),
	'currenthour'               => array( '1', 'ТЕКОВЕНЧАС', 'CURRENTHOUR' ),
	'localmonth'                => array( '1', 'МЕСЕЦ_ЛОКАЛНО', 'LOCALMONTH', 'LOCALMONTH2' ),
	'localmonth1'               => array( '1', 'МЕСЕЦ_ЛОКАЛНО1', 'LOCALMONTH1' ),
	'localmonthname'            => array( '1', 'МЕСЕЦИМЕ_ЛОКАЛНО', 'LOCALMONTHNAME' ),
	'localmonthnamegen'         => array( '1', 'МЕСЕЦИМЕ_ЛОКАЛНО_ГЕНИТИВ', 'LOCALMONTHNAMEGEN' ),
	'localmonthabbrev'          => array( '1', 'МЕСЕЦИМЕ_ЛОКАЛНО_КРАТЕНКА', 'LOCALMONTHABBREV' ),
	'localday'                  => array( '1', 'ДЕН_ЛОКАЛНО', 'LOCALDAY' ),
	'localday2'                 => array( '1', 'ДЕН2_ЛОКАЛНО', 'LOCALDAY2' ),
	'localdayname'              => array( '1', 'ИМЕНАДЕН_ЛОКАЛНО', 'LOCALDAYNAME' ),
	'localyear'                 => array( '1', 'ГОДИНА_ЛОКАЛНО', 'LOCALYEAR' ),
	'localtime'                 => array( '1', 'ВРЕМЕ_ЛОКАЛНО', 'LOCALTIME' ),
	'localhour'                 => array( '1', 'ЧАС_ЛОКАЛНО', 'LOCALHOUR' ),
	'numberofpages'             => array( '1', 'БРОЈНАСТРАНИЦИ', 'NUMBEROFPAGES' ),
	'numberofarticles'          => array( '1', 'БРОЈСТАТИИ', 'БРОЈНАСТАТИИ', 'NUMBEROFARTICLES' ),
	'numberoffiles'             => array( '1', 'БРОЈНАПОДАТОТЕКИ', 'NUMBEROFFILES' ),
	'numberofusers'             => array( '1', 'БРОЈНАКОРИСНИЦИ', 'NUMBEROFUSERS' ),
	'numberofactiveusers'       => array( '1', 'БРОЈНААКТИВНИКОРИСНИЦИ', 'NUMBEROFACTIVEUSERS' ),
	'numberofedits'             => array( '1', 'БРОЈНАУРЕДУВАЊА', 'NUMBEROFEDITS' ),
	'pagename'                  => array( '1', 'СТРАНИЦА', 'PAGENAME' ),
	'pagenamee'                 => array( '1', 'СТРАНИЦАИ', 'PAGENAMEE' ),
	'namespace'                 => array( '1', 'ИМЕПРОСТОР', 'ИМЕНСКИПРОСТОР', 'NAMESPACE' ),
	'talkspace'                 => array( '1', 'РАЗГОВОРПРОСТОР', 'TALKSPACE' ),
	'fullpagename'              => array( '1', 'ЦЕЛОСНОИМЕНАСТРАНИЦА', 'FULLPAGENAME' ),
	'subpagename'               => array( '1', 'ПОТСТРАНИЦА', 'SUBPAGENAME' ),
	'basepagename'              => array( '1', 'ОСНОВНАСТРАНИЦА', 'BASEPAGENAME' ),
	'talkpagename'              => array( '1', 'СТРАНИЦАЗАРАЗГОВОР', 'TALKPAGENAME' ),
	'subjectpagename'           => array( '1', 'ИМЕНАСТАТИЈА', 'SUBJECTPAGENAME', 'ARTICLEPAGENAME' ),
	'msg'                       => array( '0', 'ПОР:', 'MSG:' ),
	'subst'                     => array( '0', 'ЗАМЕНИ:', 'SUBST:' ),
	'safesubst'                 => array( '0', 'БЕЗБЗАМЕНИ', 'SAFESUBST:' ),
	'msgnw'                     => array( '0', 'ИЗВЕШТNW:', 'MSGNW:' ),
	'img_thumbnail'             => array( '1', 'мини', 'мини-слика', 'thumbnail', 'thumb' ),
	'img_manualthumb'           => array( '1', 'мини-слика=$1', 'мини=$1', 'thumbnail=$1', 'thumb=$1' ),
	'img_right'                 => array( '1', 'десно', 'д', 'right' ),
	'img_left'                  => array( '1', 'лево', 'л', 'left' ),
	'img_none'                  => array( '1', 'н', 'нема', 'none' ),
	'img_width'                 => array( '1', '$1пкс', '$1п', '$1px' ),
	'img_center'                => array( '1', 'центар', 'ц', 'center', 'centre' ),
	'img_framed'                => array( '1', 'рамка', 'ворамка', 'framed', 'enframed', 'frame' ),
	'img_frameless'             => array( '1', 'безрамка', 'frameless' ),
	'img_lang'                  => array( '1', 'јаз=$1', 'lang=$1' ),
	'img_page'                  => array( '1', 'страница=$1', 'страница_$1', 'page=$1', 'page $1' ),
	'img_upright'               => array( '1', 'исправено', 'исправено=$1', 'исправено_$1', 'upright', 'upright=$1', 'upright $1' ),
	'img_border'                => array( '1', 'граничник', 'граница', 'border' ),
	'img_baseline'              => array( '1', 'основналинија', 'baseline' ),
	'img_sub'                   => array( '1', 'долениндекс', 'дол', 'sub' ),
	'img_super'                 => array( '1', 'горениндекс', 'гор', 'super', 'sup' ),
	'img_top'                   => array( '1', 'врв', 'најгоре', 'top' ),
	'img_text_top'              => array( '1', 'текст-врв', 'текст-најгоре', 'text-top' ),
	'img_middle'                => array( '1', 'средина', 'middle' ),
	'img_bottom'                => array( '1', 'дно', 'најдолу', 'bottom' ),
	'img_text_bottom'           => array( '1', 'текст-дно', 'текст-најдолу', 'text-bottom' ),
	'img_link'                  => array( '1', 'врска=$1', 'link=$1' ),
	'img_alt'                   => array( '1', 'алт=$1', 'alt=$1' ),
	'img_class'                 => array( '1', 'класа=$1', 'class=$1' ),
	'sitename'                  => array( '1', 'ИМЕНАМРЕЖНОМЕСТО', 'SITENAME' ),
	'localurl'                  => array( '0', 'ЛОКАЛНААДРЕСА:', 'LOCALURL:' ),
	'localurle'                 => array( '0', 'ЛОКАЛНААДРЕСАИ:', 'LOCALURLE:' ),
	'articlepath'               => array( '0', 'ПАТЕКАНАСТАТИЈА', 'ARTICLEPATH' ),
	'pageid'                    => array( '0', 'НАЗНАКАНАСТРАНИЦА', 'PAGEID' ),
	'server'                    => array( '0', 'ОПСЛУЖУВАЧ', 'SERVER' ),
	'servername'                => array( '0', 'ИМЕНАОПСЛУЖУВАЧ', 'SERVERNAME' ),
	'scriptpath'                => array( '0', 'ПАТЕКАНАСКРИПТА', 'SCRIPTPATH' ),
	'stylepath'                 => array( '0', 'СТИЛСКАПАТЕКА', 'STYLEPATH' ),
	'grammar'                   => array( '0', 'ГРАМАТИКА:', 'GRAMMAR:' ),
	'gender'                    => array( '0', 'ПОЛ:', 'GENDER:' ),
	'notitleconvert'            => array( '0', '__БЕЗПРЕТВОРАЊЕНАСЛОВ__', '__NOTITLECONVERT__', '__NOTC__' ),
	'nocontentconvert'          => array( '0', '__БЕЗПРЕТВОРАЊЕСОДРЖИНА__', '__NOCONTENTCONVERT__', '__NOCC__' ),
	'currentweek'               => array( '1', 'ТЕКОВНАСЕДМИЦА', 'CURRENTWEEK' ),
	'currentdow'                => array( '1', 'ТЕКОВЕНДЕНВОСЕДМИЦАТА', 'CURRENTDOW' ),
	'localweek'                 => array( '1', 'СЕДМИЦА_ЛОКАЛНО', 'LOCALWEEK' ),
	'localdow'                  => array( '1', 'ЛОКАЛЕНДЕНВОСЕДМИЦАТА', 'LOCALDOW' ),
	'revisionid'                => array( '1', 'НАЗНАКАНАПРЕРАБОТКА', 'REVISIONID' ),
	'revisionday'               => array( '1', 'ДЕННАПРЕРАБОТКА', 'REVISIONDAY' ),
	'revisionday2'              => array( '1', 'ДЕННАПРЕРАБОТКА2', 'REVISIONDAY2' ),
	'revisionmonth'             => array( '1', 'МЕСЕЦНАПРЕРАБОТКА', 'REVISIONMONTH' ),
	'revisionmonth1'            => array( '1', 'МЕСЕЦНАПРЕРАБОТКА1', 'REVISIONMONTH1' ),
	'revisionyear'              => array( '1', 'ГОДИНАНАПРЕРАБОТКА', 'REVISIONYEAR' ),
	'revisiontimestamp'         => array( '1', 'ВРЕМЕНАПРЕРАБОТКА', 'REVISIONTIMESTAMP' ),
	'revisionuser'              => array( '1', 'КОРИСНИКНАНАПРЕРАБОТКА', 'REVISIONUSER' ),
	'revisionsize'              => array( '1', 'ГОЛЕМИНАНАПРЕРАБОТКА', 'REVISIONSIZE' ),
	'plural'                    => array( '0', 'МНОЖИНА:', 'PLURAL:' ),
	'fullurl'                   => array( '0', 'ПОЛНАURL:', 'FULLURL:' ),
	'fullurle'                  => array( '0', 'ПОЛНАURLE:', 'FULLURLE:' ),
	'canonicalurl'              => array( '0', 'КАНОНСКАURL:', 'CANONICALURL:' ),
	'canonicalurle'             => array( '0', 'КАНОНСКАURLE:', 'CANONICALURLE:' ),
	'lcfirst'                   => array( '0', 'ПРВОМБ', 'LCFIRST:' ),
	'ucfirst'                   => array( '0', 'ПРВОГБ', 'UCFIRST:' ),
	'lc'                        => array( '0', 'МБ', 'LC:' ),
	'uc'                        => array( '0', 'ГБ', 'UC:' ),
	'raw'                       => array( '0', 'СИРОВО:', 'RAW:' ),
	'displaytitle'              => array( '1', 'ПРИКАЖИНАСЛОВ', 'DISPLAYTITLE' ),
	'rawsuffix'                 => array( '1', 'П', 'R' ),
	'nocommafysuffix'           => array( '0', 'БЕЗПОДЕЛ', 'NOSEP' ),
	'newsectionlink'            => array( '1', '__ВРСКАНОВПОДНАСЛОВ__', '__NEWSECTIONLINK__' ),
	'nonewsectionlink'          => array( '1', '__БЕЗВРСКАНОВПОДНАСЛОВ__', '__NONEWSECTIONLINK__' ),
	'currentversion'            => array( '1', 'ТЕКОВНАВЕРЗИЈА', 'CURRENTVERSION' ),
	'urlencode'                 => array( '0', 'ШИФРИРАЈURL:', 'URLENCODE:' ),
	'anchorencode'              => array( '0', 'ШИФРИРАЈКОТВА', 'ANCHORENCODE' ),
	'currenttimestamp'          => array( '1', 'ОЗНАЧЕНОТЕКОВНОВРЕМЕ', 'CURRENTTIMESTAMP' ),
	'localtimestamp'            => array( '1', 'ОЗНАЧЕНОЛОКАЛНОВРЕМЕ', 'LOCALTIMESTAMP' ),
	'directionmark'             => array( '1', 'ОЗНАКАЗАНАСОКА', 'DIRECTIONMARK', 'DIRMARK' ),
	'language'                  => array( '0', '#ЈАЗИК:', '#LANGUAGE:' ),
	'contentlanguage'           => array( '1', 'ЈАЗИКНАСОДРЖИНАТА', 'CONTENTLANGUAGE', 'CONTENTLANG' ),
	'pagesinnamespace'          => array( '1', 'СТРАНИЦИВОИМЕНСКИПРОСТОР', 'PAGESINNAMESPACE:', 'PAGESINNS:' ),
	'numberofadmins'            => array( '1', 'БРОЈНААДМИНИСТРАТОРИ', 'NUMBEROFADMINS' ),
	'formatnum'                 => array( '0', 'ФОРМАТБРОЈ', 'FORMATNUM' ),
	'padleft'                   => array( '0', 'ПОСТАВИЛЕВО', 'PADLEFT' ),
	'padright'                  => array( '0', 'ПОСТАВИДЕСНО', 'PADRIGHT' ),
	'special'                   => array( '0', 'службена', 'службени', 'special' ),
	'defaultsort'               => array( '1', 'ОСНОВНОПОДРЕДУВАЊЕ:', 'DEFAULTSORT:', 'DEFAULTSORTKEY:', 'DEFAULTCATEGORYSORT:' ),
	'filepath'                  => array( '0', 'ПОДАТОТЕЧНАПАТЕКА:', 'FILEPATH:' ),
	'tag'                       => array( '0', 'ознака', 'tag' ),
	'hiddencat'                 => array( '1', '__СКРИЕНАКАТ__', '__СКРИЕНАКАТЕГОРИЈА__', '__HIDDENCAT__' ),
	'pagesincategory'           => array( '1', 'СТРАНИЦИВОКАТЕГОРИЈА', 'СТРАНИЦИВОКАТ', 'PAGESINCATEGORY', 'PAGESINCAT' ),
	'pagesize'                  => array( '1', 'ГОЛЕМИНА_НА_СТРАНИЦА', 'PAGESIZE' ),
	'index'                     => array( '1', '__ИНДЕКС__', '__INDEX__' ),
	'noindex'                   => array( '1', '__БЕЗИНДЕКС__', '__NOINDEX__' ),
	'numberingroup'             => array( '1', 'БРОЈВОГРУПА', 'NUMBERINGROUP', 'NUMINGROUP' ),
	'staticredirect'            => array( '1', '__СТАТИЧНОПРЕНАСОЧУВАЊЕ__', '__STATICREDIRECT__' ),
	'protectionlevel'           => array( '1', 'НИВОНАЗАШТИТА', 'PROTECTIONLEVEL' ),
	'cascadingsources'          => array( '1', 'КАСКАДНИИЗВОРИ', 'CASCADINGSOURCES' ),
	'formatdate'                => array( '0', 'форматдатум', 'formatdate', 'dateformat' ),
	'url_path'                  => array( '0', 'ПАТЕКА', 'PATH' ),
	'url_wiki'                  => array( '0', 'ВИКИ', 'WIKI' ),
	'url_query'                 => array( '0', 'БАРАЊЕ', 'QUERY' ),
	'defaultsort_noerror'       => array( '0', 'безгрешки', 'noerror' ),
	'defaultsort_noreplace'     => array( '0', 'беззамена', 'noreplace' ),
	'displaytitle_noerror'      => array( '0', 'безгрешка', 'noerror' ),
	'displaytitle_noreplace'    => array( '0', 'незаменувај', 'noreplace' ),
	'pagesincategory_all'       => array( '0', 'сите', 'all' ),
	'pagesincategory_pages'     => array( '0', 'страници', 'pages' ),
	'pagesincategory_subcats'   => array( '0', 'поткатегории', 'subcats' ),
	'pagesincategory_files'     => array( '0', 'податотеки', 'files' ),
);

$linkTrail = '/^([a-zабвгдѓежзѕијклљмнњопрстќуфхцчџш]+)(.*)$/sDu';
$separatorTransformTable = array( ',' => '.', '.' => ',' );

