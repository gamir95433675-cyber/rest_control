<?php
require_once 'config.php';
if(!isset($_SESSION['user'])||$_SESSION['user']['role']!=='admin') redirect('index.php');
$displayNames = [
    'akramsadat.m' => 'اکرم سادات مظلوم',
    'ali.mohammadho' => 'علی محمدحسین پور',
    'alireza.kheir' => 'علیرضا خیر خواهان',
    'alireza.kou' => 'علی کوهستانی',
    'alireza.mome' => 'علیرضا مومن زاده',
    'alireza.rashv' => 'علیرضا رشوند',
    'alireza.shab' => 'علیرضا شبیه زاده',
    'amirhossein.ghola' => 'امیر حسین غلامی بیگلویی',
    'amirmehdi.p' => 'امیر مهدی پیش بین پستکی',
    'amirreza.kim' => 'امیر رضا کیمیایی',
    'arad.d' => 'آراد داس دار',
    'arezou.aj' => 'آرزو عجم حاج محمدی تلک آیادی',
    'arezou.d' => 'آرزو دهقانی',
    'atena.b' => 'آتنا برقبانی',
    'atieh.bi' => 'عطیه بیدگل',
    'azadeh.az' => 'آزاده اظهری',
    'banafsheh.po' => 'بنفشه پورصناعی',
    'delnia abbasi' => 'دلنیا عباسی',
    'elaheh.ghod' => 'الهه قدسی',
    'erfan.ho' => 'عرفان حسین علیزاده',
    'faezeh.haj' => 'فایزه حاج کتابی تلک آبادی',
    'faezeh.sam' => 'صمدی صمدی',
    'faranak.an' => 'فرانک انصاریان مهابادی',
    'fatemeh emam yari' => 'فاطمه امامیاری',
    'fatemeh sheikh' => 'فاطمه شیخ',
    'fatemeh.amer' => 'فاطمه عامری اردستانی',
    'fatemeh.aminz' => 'فاطمه امین زاده مولان',
    'fatemeh.at' => 'فاطمه عاطفی',
    'fatemeh.bagher' => 'فاطمه باقری',
    'fatemeh.em' => 'فاطمه امامیاری',
    'fatemeh.jafarik' => 'فاطمه جعفری کرمانی پور',
    'fatemeh.khaj' => 'فاطمه خواجه مهابادی',
    'fatemeh.khams' => 'فاطمه خمسه مهابادی',
    'fatemeh.khorsh' => 'فاطمه خورشیدی',
    'fatemeh.maj' => 'فاطمه مجیدی نسب',
    'fatemeh.moaze' => 'فاطمه موذنی',
    'fatemeh.ramez' => 'فاطمه رمضانی',
    'fatemeh.shaf' => 'فاطمه شفیعی',
    'fatemeh.sheikh1' => 'فاطمه شیخ',
    'fetemeh.at' => 'فاطمه عاطفی',
    'gelareh.sh' => 'گلاره شیرزادی',
    'hadis.kar' => 'حدیث کارگر',
    'hadiseh.a' => 'حدیثه علیرضایی',
    'hadiseh.gh' => 'حدیثه قلی زاده گزور',
    'hadiseh.k' => 'حدیثه کارگریان',
    'hadiseh.ya' => 'حدیثه یاری',
    'hananeh.f' => 'حنانه فرخزاد',
    'hanieh.hal' => 'هانیه حلاج زاده',
    'hanieh.ne' => 'هانیه نژادمحمد',
    'hossein.arab' => 'حسین عرب سرخی تلک آبادی',
    'iman.sei' => 'ایمان سیف پناهی',
    'jamileh.y' => 'جمیه یغمائیان',
    'kimia.j' => 'کیمیا جوادی',
    'kosar.ra' => 'کوثر رجبی',
    'maedeh.as' => 'مائده اسدی',
    'maedeh.nan' => 'مائده نعنائی زواره',
    'mahdieh.ha' => 'مهدیه حاجی جباری',
    'mahsa.eb' => 'مهسا ابراهیمی لاسیبی',
    'mahsa.has' => 'مهسا حسنلو',
    'mahsa.mora' => 'مهسا مرادی',
    'mahya.h' => 'محیا حجازی',
    'maryam.boz' => 'مریم بزرگمهر',
    'maryam.feiz' => 'مریم فیضی ده نایبی',
    'maryam.mirs' => 'مریم میرشفیعی',
    'maryam.vah' => 'مریم واحد یوسفی',
    'marzieh.kha' => 'مرضیه خزایی',
    'marzieh.ze' => 'مرضیه زراعتیان',
    'matin.mou' => 'سید متین موسوی',
    'mehdi.ghasemio' => 'مهدی قاسمی اوزن بلاغ',
    'mehdi.shav' => 'مهدیه شواخی زواره',
    'mehrdad.bi' => 'مهرداد بیزری',
    'melika.fa' => 'ملیکا فاتحی',
    'melika.t' => 'ملیکا تنومند',
    'mina.raj' => 'مینا رجبی',
    'mobina.rah' => 'مبینا رحمتی',
    'mobina.sab' => 'مبینا صابری',
    'mohadeseh.abe' => 'محدثه عابدینی اکار زواره',
    'mohammad.hajik' => 'محمد حاجی کتابی تلک آیادی',
    'mohammadali.saj' => 'محمدعلی سجادی',
    'mohammadamin.zam' => 'محمد ایمن زمانی قدیمی',
    'mohsen.azim' => 'محسن عظیمیان زواره',
    'nafiseh.ba' => 'نفیسه براتی',
    'nafiseh.l' => 'نفیسه لباف قاسمی زواره',
    'nastaran.p' => 'نسترن پیلور',
    'nazanin.po' => 'نازنین پولادی',
    'nazanin.so' => 'نازنین سلطانی راد',
    'nazanin.za' => 'نازنین زاویسی',
    'neda.kal' => 'ندا کلانی مهابادی',
    'neda.sam' => 'ندا ثمری',
    'negin.ge' => 'نگین گراوند',
    'pantea.g' => 'پانته آ گل پرور',
    'pardis.p' => 'پردیس پارچه باف شوقی',
    'parvin.a' => 'پروین احمدی',
    'radin.g' => 'رادین قاضی',
    'reihaneh.f' => 'ریحانه فیضی',
    'roya.male' => 'رویا ملکی',
    'saeid.zama' => 'سعید زمانی',
    'saeidreza.h' => 'سعیدرضا حمامی',
    'saghar.k' => 'ساغر کرمی',
    'sahar.khaje' => 'سحر خواجوند صالحانی',
    'sahar.khod' => 'سحر خداوردی',
    'sajedeh.mos' => 'ساجده مسلمی امیرانی',
    'sajjad.pouy' => 'سجاد پویان منش',
    'sama.t' => 'سما تقی پور نیاسری',
    'samaneh.mosl' => 'سمانه مسلمی امیرانی',
    'sara.moht' => 'سارا محتشم',
    'shabnam.g' => 'شبنم غفوری',
    'shakiba.b' => 'شکیبا بهرامی',
    'shayan.b' => 'شایان بهارلو',
    'shirin.mok' => 'شیرین مختارزاده اردستانی',
    'sogol.e' => 'سوگل ایلیات',
    'sorour.k' => 'سرور کاکایی',
    'tahereh.fa' => 'ظاهره فنی',
    'yasaman.ja' => 'یاسمن جعفری',
    'yeganeh.sar' => 'یگانه سرباز قره بلاغی',
    'zahra.bara' => 'زهرا باراد',
    'zahra.fall' => 'زهرا فلاح شفیعی',
    'zahra.hes' => 'زهرا حسامی نیکران',
    'zahra.jav' => 'زهره جاورسجین',
    'zahra.karg' => 'زهرا کارگریان مروستی',
    'zahra.mosle' => 'زهرا مسلمی امیرانی',
    'zeinab.ke' => 'زینب کشاورز',
    'zeinab.kham' => 'زینب خمسه مهابادی',
    'zeinab.moh' => 'زینب محمدی',
    'zeinab.rav' => 'زینب روانیخش',
    'zohreh.fa' => 'زهره فلاح نژاد',
    'zohreh.kh' => 'زهره خیرآبادی',
];
$deleteUsers = ['mohammadmehdi.he','mehdi.saei','amirhossein.ghade','ali.asga','nima.mal','elham.agh'];
$updatedExperts=0; $updatedUsers=0; $deleted=0;
try {
    $pdo->beginTransaction();
    // Remove the requested identities from chat/history first, so they disappear everywhere.
    foreach($deleteUsers as $du) {
        $st=$pdo->prepare("SELECT id FROM chat_conversations WHERE created_by=?"); $st->execute([$du]);
        $convIds=$st->fetchAll(PDO::FETCH_COLUMN);
        if($convIds) {
            $marks=implode(',',array_fill(0,count($convIds),'?'));
            $pdo->prepare("DELETE FROM chat_messages WHERE conversation_id IN ($marks)")->execute($convIds);
            $pdo->prepare("DELETE FROM chat_members WHERE conversation_id IN ($marks)")->execute($convIds);
            $pdo->prepare("DELETE FROM chat_conversations WHERE id IN ($marks)")->execute($convIds);
        }
        $st=$pdo->prepare("SELECT conversation_id FROM chat_members WHERE username=?"); $st->execute([$du]);
        $memberConv=$st->fetchAll(PDO::FETCH_COLUMN);
        if($memberConv) {
            $marks=implode(',',array_fill(0,count($memberConv),'?'));
            $pdo->prepare("DELETE FROM chat_messages WHERE conversation_id IN ($marks)")->execute($memberConv);
            $pdo->prepare("DELETE FROM chat_members WHERE conversation_id IN ($marks)")->execute($memberConv);
            $pdo->prepare("DELETE FROM chat_conversations WHERE id IN ($marks)")->execute($memberConv);
        }
        // Delete their rest history and dependent rows.
        $st=$pdo->prepare("SELECT id FROM rest_requests WHERE expert_username=? OR supervisor_username=?"); $st->execute([$du,$du]);
        $rr=$st->fetchAll(PDO::FETCH_COLUMN);
        if($rr) {
            $marks=implode(',',array_fill(0,count($rr),'?'));

            $pdo->prepare("DELETE FROM overtime_alerts WHERE rest_request_id IN ($marks)")->execute($rr);
            $pdo->prepare("DELETE FROM rest_requests WHERE id IN ($marks)")->execute($rr);
        }
        $st=$pdo->prepare("DELETE FROM experts WHERE username=?"); $st->execute([$du]); $deleted += $st->rowCount();
        $st=$pdo->prepare("DELETE FROM supervisor_permissions WHERE supervisor_username=?"); $st->execute([$du]); $deleted += $st->rowCount();
        // If Nima had any remaining experts assigned to him, move them to the admin before deleting Nima.
        if($du==='nima.mal') {
            $st=$pdo->prepare("UPDATE experts SET supervisor_username='maryam.ahma' WHERE supervisor_username=?"); $st->execute([$du]);
        }
        $st=$pdo->prepare("DELETE FROM users WHERE username=?"); $st->execute([$du]); $deleted += $st->rowCount();
    }
    $st=$pdo->prepare("UPDATE experts SET display_name=? WHERE username=?");
    foreach($displayNames as $u=>$name) { $st->execute([$name,$u]); $updatedExperts += $st->rowCount(); }
    $st=$pdo->prepare("UPDATE users SET display_name=? WHERE username=? AND role='supervisor'");
    foreach($displayNames as $u=>$name) { $st->execute([$name,$u]); $updatedUsers += $st->rowCount(); }
    $pdo->commit();
    echo '<!doctype html><meta charset="utf-8"><body dir="rtl" style="font-family:tahoma;padding:30px"><h2>نام‌ها و کاربران با موفقیت اصلاح شدند.</h2><p>کارشناسان: '.(int)$updatedExperts.' مورد</p><p>سرپرست‌ها: '.(int)$updatedUsers.' مورد</p><p>کاربران حذف‌شده: '.(int)$deleted.' رکورد</p><p>۶ کاربر درخواستی از کاربران، تیم‌ها، تاریخچه و چت حذف شدند.</p><p><a href="supervisor.php">بازگشت به پنل</a></p></body>';
} catch(Throwable $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    echo '<pre>'.h($e->getMessage()).'</pre>';
}
?>