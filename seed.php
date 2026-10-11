<?php
require_once 'config.php';
/*
 * اجرای این فایل اطلاعات نمونه/اولیه را ایجاد یا به‌روزرسانی می‌کند.
 */
try {
 $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
 $pdo->exec("TRUNCATE TABLE chat_messages");
 $pdo->exec("TRUNCATE TABLE chat_members");
 $pdo->exec("TRUNCATE TABLE chat_conversations");
 $pdo->exec("TRUNCATE TABLE overtime_alerts");
 $pdo->exec("TRUNCATE TABLE rest_requests");
 $pdo->exec("TRUNCATE TABLE supervisor_permissions");
 $pdo->exec("TRUNCATE TABLE experts");
 $pdo->exec("TRUNCATE TABLE users");
 $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

 $password=password_hash('123456',PASSWORD_DEFAULT);
 $supervisors=[["maryam.ahma", "مریم احمدی", "admin"], ["fatemeh.khams", "فریبا خمسه", "supervisor"], ["fatemeh.khorsh", "فاطمه خورشیدی", "supervisor"], ["elaheh.ghod", "الهه قدسی", "supervisor"], ["iman.sei", "ایمان سیف پناهی", "supervisor"], ["alireza.kou", "علیرضا کوهستانی", "supervisor"], ["sorour.k", "سرور کاکایی", "supervisor"]];
 $experts=[["faranak.an", "fatemeh.khams"], ["hossein.arab", "fatemeh.khams"], ["pardis.p", "fatemeh.khorsh"], ["fatemeh.khaj", "fatemeh.khams"], ["akramsadat.m", "fatemeh.khams"], ["arezou.aj", "fatemeh.khams"], ["mohammad.hajik", "fatemeh.khams"], ["saeidreza.h", "fatemeh.khams"], ["samaneh.mosl", "fatemeh.khams"], ["neda.kal", "fatemeh.khams"], ["maryam.feiz", "fatemeh.khams"], ["shirin.mok", "fatemeh.khams"], ["jamileh.y", "fatemeh.khams"], ["mohadeseh.abe", "fatemeh.khams"], ["zahra.mosle", "fatemeh.khams"], ["zeinab.rav", "fatemeh.khams"], ["alireza.mome", "fatemeh.khams"], ["mahya.h", "fatemeh.khams"], ["mahsa.eb", "fatemeh.khams"], ["mehrdad.bi", "fatemeh.khams"], ["zeinab.ke", "fatemeh.khorsh"], ["fatemeh.moaze", "fatemeh.khams"], ["arezou.d", "fatemeh.khorsh"], ["yeganeh.sar", "maryam.ahma"], ["matin.mou", "maryam.ahma"], ["zahra.karg", "fatemeh.khorsh"], ["hadiseh.a", "maryam.ahma"], ["fatemeh.maj", "fatemeh.khorsh"], ["mehdi.shav", "fatemeh.khorsh"], ["nafiseh.l", "fatemeh.khorsh"], ["nazanin.po", "iman.sei"], ["fatemeh.ramez", "iman.sei"], ["banafsheh.po", "elaheh.ghod"], ["negin.ge", "iman.sei"], ["neda.sam", "iman.sei"], ["mina.raj", "iman.sei"], ["alireza.kou", "maryam.ahma"], ["shakiba.b", "iman.sei"], ["hanieh.ne", "iman.sei"], ["zahra.jav", "elaheh.ghod"], ["kimia.j", "elaheh.ghod"], ["fatemeh.bagher", "iman.sei"], ["sara.moht", "maryam.ahma"], ["nazanin.za", "alireza.kou"], ["mahdieh.ha", "maryam.ahma"], ["gelareh.sh", "iman.sei"], ["zahra.hes", "alireza.kou"], ["hadiseh.k", "iman.sei"], ["amirreza.kim", "maryam.ahma"], ["sogol.e", "iman.sei"], ["faezeh.sam", "maryam.ahma"], ["amirmehdi.p", "maryam.ahma"], ["erfan.ho", "alireza.kou"], ["marzieh.ze", "maryam.ahma"], ["shayan.b", "alireza.kou"], ["mobina.sab", "alireza.kou"], ["yasaman.ja", "maryam.ahma"], ["alireza.shab", "maryam.ahma"], ["azadeh.az", "maryam.ahma"], ["hadiseh.ya", "alireza.kou"], ["fatemeh.amer", "maryam.ahma"], ["amirhossein.ghola", "maryam.ahma"], ["hadis.kar", "maryam.ahma"], ["alireza.kheir", "maryam.ahma"], ["faezeh.haj", "maryam.ahma"], ["hanieh.hal", "elaheh.ghod"], ["elaheh.ghod", "maryam.ahma"], ["mobina.rah", "elaheh.ghod"], ["zahra.bara", "elaheh.ghod"], ["parvin.a", "elaheh.ghod"], ["maedeh.as", "alireza.kou"], ["zahra.fall", "alireza.kou"], ["arad.d", "fatemeh.khorsh"], ["fatemeh.shaf", "fatemeh.khorsh"], ["saeid.zama", "maryam.ahma"], ["sorour.k", "maryam.ahma"], ["fatemeh.khams", "maryam.ahma"], ["fatemeh.khorsh", "maryam.ahma"], ["marzieh.kha", "elaheh.ghod"], ["nastaran.p", "alireza.kou"], ["iman.sei", "maryam.ahma"], ["mahsa.mora", "sorour.k"], ["hadiseh.gh", "sorour.k"], ["mehdi.ghasemio", "sorour.k"], ["hananeh.f", "sorour.k"], ["maryam.boz", "sorour.k"], ["mahsa.has", "sorour.k"], ["sama.t", "sorour.k"], ["shabnam.g", "sorour.k"], ["nazanin.so", "sorour.k"], ["tahereh.fa", "sorour.k"], ["melika.t", "maryam.ahma"], ["alireza.rashv", "maryam.ahma"], ["atena.b", "maryam.ahma"], ["zohreh.kh", "alireza.kou"], ["fatemeh.jafarik", "alireza.kou"], ["mohammadamin.zam", "maryam.ahma"], ["melika.fa", "alireza.kou"], ["sajedeh.mos", "elaheh.ghod"], ["maedeh.nan", "fatemeh.khams"], ["sahar.khod", "alireza.kou"], ["ali.mohammadho", "iman.sei"], ["zeinab.kham", "fatemeh.khams"], ["zeinab.moh", "fatemeh.khams"], ["kosar.ra", "fatemeh.khams"], ["fatemeh.em", "sorour.k"], ["fatemeh.sheikh1", "fatemeh.khorsh"], ["fetemeh.at", "fatemeh.khams"], ["mohammadali.saj", "maryam.ahma"], ["atieh.bi", "sorour.k"], ["roya.male", "alireza.kou"], ["saghar.k", "iman.sei"], ["radin.g", "maryam.ahma"]];

 $u=$pdo->prepare("INSERT INTO users(username,password,role,display_name) VALUES(?,?,?,?)");
 foreach($supervisors as $s) $u->execute([$s[0],$password,$s[2],$s[1]]);
 $perm=$pdo->prepare("INSERT INTO supervisor_permissions(supervisor_username) VALUES(?)");
 foreach($supervisors as $s) if($s[2]==='supervisor') $perm->execute([$s[0]]);

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
     'elaheh.ghod' => 'الهه قدسی',
     'erfan.ho' => 'عرفان حسین علیزاده',
     'faezeh.haj' => 'فایزه حاج کتابی تلک آبادی',
     'faezeh.sam' => 'صمدی صمدی',
     'faranak.an' => 'فرانک انصاریان مهابادی',
     'fatemeh.amer' => 'فاطمه عامری اردستانی',
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
     'maryam.ahma' => 'مریم احمدی',
     'maryam.boz' => 'مریم بزرگمهر',
     'maryam.feiz' => 'مریم فیضی ده نایبی',
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
     'nafiseh.l' => 'نفیسه لباف قاسمی زواره',
     'nastaran.p' => 'نسترن پیلور',
     'nazanin.po' => 'نازنین پولادی',
     'nazanin.so' => 'نازنین سلطانی راد',
     'nazanin.za' => 'نازنین زاویسی',
     'neda.kal' => 'ندا کلانی مهابادی',
     'neda.sam' => 'ندا ثمری',
     'negin.ge' => 'نگین گراوند',
     'pardis.p' => 'پردیس پارچه باف شوقی',
     'parvin.a' => 'پروین احمدی',
     'radin.g' => 'رادین قاضی',
     'roya.male' => 'رویا ملکی',
     'saeid.zama' => 'سعید زمانی',
     'saeidreza.h' => 'سعیدرضا حمامی',
     'saghar.k' => 'ساغر کرمی',
     'sahar.khod' => 'سحر خداوردی',
     'sajedeh.mos' => 'ساجده مسلمی امیرانی',
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
     'zohreh.kh' => 'زهره خیرآبادی',
 ];
 $e=$pdo->prepare("INSERT INTO experts(username,password,supervisor_username,display_name) VALUES(?,?,?,?)");
 foreach($experts as $x) { $key=strtolower($x[0]); $e->execute([$x[0],$password,$x[1],$displayNames[$key]??$x[0]]); }

 echo '<h3 style="font-family:tahoma">✅ اطلاعات اولیه با موفقیت ثبت شد.</h3><p>ورود کاربران آماده است.</p>';
} catch(Throwable $e) {
 echo '<pre>'.h($e->getMessage()).'</pre>';
}
?>