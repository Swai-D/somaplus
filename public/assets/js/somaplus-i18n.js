/* SomaPlus – English / Kiswahili switcher (client-side, no backend needed) */
(function () {
  var D = {
    "Main":"Kuu","Academics":"Masomo","Finance":"Fedha","People":"Watu","Communication":"Mawasiliano","Administration":"Utawala","Pages":"Kurasa",
    "Dashboard":"Dashibodi","School":"Shule","Student":"Mwanafunzi","Students":"Wanafunzi","Teacher":"Mwalimu","Teachers":"Walimu","Parent":"Mzazi",
    "Guardian":"Mlezi","Guardians":"Walezi","LMS":"LMS","Classes":"Madarasa","Class":"Darasa","Subjects":"Mada","Subject":"Somo","Class Room":"Chumba cha Darasa",
    "Examinations":"Mitihani","Exam":"Mtihani","Exams":"Mitihani","Attendance":"Mahudhurio","Leaves":"Likizo","Leave":"Likizo","Certificate":"Cheti","Library":"Maktaba",
    "Fees Collection":"Ukusanyaji wa Ada","Fees":"Ada","Accounts":"Hesabu","HRM":"Rasilimali Watu","Notice Board":"Ubao wa Matangazo","Event":"Tukio","Events":"Matukio",
    "Message":"Ujumbe","Subscription Plan":"Mpango wa Usajili","Role & Access":"Majukumu na Ufikiaji","Assign Role":"Kabidhi Jukumu","Settings":"Mipangilio",
    "Authentication":"Uthibitishaji","Login":"Ingia","Sign In":"Ingia","Sign Up":"Jisajili","Register":"Jisajili","Logout":"Toka","My Profile":"Wasifu Wangu","School Admin":"Msimamizi wa Shule",
    "Search":"Tafuta","Export":"Hamisha","Filter":"Chuja","Action":"Kitendo","Status":"Hali","Active":"Hai","Inactive":"Haitumiki","Edit":"Hariri","Delete":"Futa",
    "View":"Tazama","View all":"Tazama yote","All events":"Matukio yote","Save Changes":"Hifadhi Mabadiliko","Save Change":"Hifadhi Mabadiliko","Cancel":"Ghairi","Reset":"Weka Upya",
    "Apply":"Tumia","Print":"Chapisha","Name":"Jina","Full Name":"Jina Kamili","Email":"Barua pepe","Email Address":"Anwani ya Barua pepe","Phone Number":"Namba ya Simu",
    "Mobile Number":"Namba ya Simu","Gender":"Jinsia","Male":"Kiume","Female":"Kike","Address":"Anwani","Date":"Tarehe","Date Of Birth":"Tarehe ya Kuzaliwa","Date of Birth":"Tarehe ya Kuzaliwa",
    "Admission No":"Namba ya Usajili","Roll No":"Namba ya Orodha","Roll Number":"Namba ya Orodha","Section":"Mkondo","Category":"Kundi","Grade":"Daraja","Marks":"Alama","Result":"Matokeo",
    "Pass":"Amefaulu","Fail":"Hajafaulu","Paid":"Imelipwa","Unpaid":"Haijalipwa","Pending":"Inasubiri","Partial":"Sehemu","Approved":"Imeidhinishwa","Rejected":"Imekataliwa",
    "Accepted":"Imekubaliwa","Completed":"Imekamilika","Inprogress":"Inaendelea","Amount":"Kiasi","Description":"Maelezo","Details":"Maelezo","Personal Info":"Taarifa Binafsi",
    "Today":"Leo","Yearly":"Kila Mwaka","Monthly":"Kila Mwezi","Weekly":"Kila Wiki","Upcoming Events":"Matukio Yajayo","Today's Class":"Darasa la Leo","Exam Results":"Matokeo ya Mitihani",
    "Leave Status":"Hali ya Likizo","Leave Requests":"Maombi ya Likizo","Leave Request":"Ombi la Likizo","Leave Types":"Aina za Likizo","Revenue Statistic":"Takwimu za Mapato",
    "User Overview":"Muhtasari wa Watumiaji","Income Vs Expense":"Mapato dhidi ya Matumizi","Top Teachers":"Walimu Bora","Top Students":"Wanafunzi Bora","Top Student":"Mwanafunzi Bora",
    "New Admissions":"Usajili Mpya","Total Students":"Jumla ya Wanafunzi","Total Teachers":"Jumla ya Walimu","Total Guardians":"Jumla ya Walezi","Fees Collected":"Ada Zilizokusanywa",
    "Total Revenue":"Jumla ya Mapato","Total Expenses":"Jumla ya Matumizi","Student Attendance":"Mahudhurio ya Wanafunzi","Calendar":"Kalenda","Student Marks":"Alama za Wanafunzi",
    "My Kids":"Watoto Wangu","Statistic":"Takwimu","School Dashboard":"Dashibodi ya Shule","Student Dashboard":"Dashibodi ya Mwanafunzi","Teacher Dashboard":"Dashibodi ya Mwalimu",
    "Parent Dashboard":"Dashibodi ya Mzazi","LMS Dashboard":"Dashibodi ya LMS","Notice Board ":"Ubao wa Matangazo","Edit Profile":"Hariri Wasifu","Login Details":"Maelezo ya Kuingia",
    "Add New Student":"Ongeza Mwanafunzi Mpya","Student List":"Orodha ya Wanafunzi","Suspend Student":"Simamisha Mwanafunzi","Student Categories":"Makundi ya Wanafunzi",
    "Edit Student":"Hariri Mwanafunzi","Student Details":"Maelezo ya Mwanafunzi","Add New Teacher":"Ongeza Mwalimu Mpya","Teacher List":"Orodha ya Walimu","Edit Teacher":"Hariri Mwalimu",
    "Teacher Details":"Maelezo ya Mwalimu","Teacher Timetable":"Ratiba ya Walimu","Add New Guardians":"Ongeza Mlezi Mpya","Add New Guardian":"Ongeza Mlezi Mpya","Guardians List":"Orodha ya Walezi",
    "Guardian List":"Orodha ya Walezi","Edit Guardian":"Hariri Mlezi","Guardian Details":"Maelezo ya Mlezi","Class List":"Orodha ya Madarasa","Class Room List":"Orodha ya Vyumba vya Darasa",
    "Subjects List":"Orodha ya Mada","Exam Schedule":"Ratiba ya Mitihani","Exam Result":"Matokeo ya Mtihani","Exam List":"Orodha ya Mitihani","Teacher Attendance":"Mahudhurio ya Walimu",
    "Employee Attendance":"Mahudhurio ya Wafanyakazi","Books List":"Orodha ya Vitabu","Members List":"Orodha ya Wanachama","Members Details":"Maelezo ya Wanachama","Member Details":"Maelezo ya Mwanachama",
    "Issue Return":"Kutoa na Kurudisha","Fees Collect":"Kusanya Ada","Fees Group":"Kundi la Ada","Fees Discount":"Punguzo la Ada","Fees Type":"Aina ya Ada","Income List":"Orodha ya Mapato",
    "Income Head":"Chanzo cha Mapato","Expense Head":"Kichwa cha Matumizi","Expense List":"Orodha ya Matumizi","Expense Head List":"Orodha ya Vichwa vya Matumizi","Transaction":"Muamala",
    "Payroll":"Mishahara","Employee List":"Orodha ya Wafanyakazi","Add New Employee":"Ongeza Mfanyakazi Mpya","Employee Details":"Maelezo ya Mfanyakazi","Department":"Idara","Departments":"Idara",
    "Designation":"Cheo","General":"Jumla","Notification":"Arifa","Currencies":"Sarafu","Languages":"Lugha","Message ":"Ujumbe","Admin":"Msimamizi","Super Admin":"Msimamizi Mkuu","Librarian":"Mkutubi",
    "Parent & Guardian Info":"Taarifa za Mzazi na Mlezi","Fathers Name":"Jina la Baba","Mothers Name":"Jina la Mama","Father Occupation":"Kazi ya Baba","Academic Year":"Mwaka wa Masomo",
    "Student Photo":"Picha ya Mwanafunzi","Fathers Photo":"Picha ya Baba","Mothers Photo":"Picha ya Mama","Guardian Name":"Jina la Mlezi","Guardian Email":"Barua pepe ya Mlezi",
    "Guardian Address":"Anwani ya Mlezi","Teacher Photo":"Picha ya Mwalimu","Join Date":"Tarehe ya Kujiunga","Blood Group":"Kundi la Damu","Height":"Urefu","Weight":"Uzito",
    "Current Address":"Anwani ya Sasa","Permanent Address":"Anwani ya Kudumu","Upload Documents":"Pakia Nyaraka","Bank Details":"Taarifa za Benki","Medical Details":"Taarifa za Afya",
    "Previous School Details":"Taarifa za Shule ya Awali","Hostel Details":"Taarifa za Bweni","Hostel":"Bweni","Room No":"Namba ya Chumba","Net Salary":"Mshahara Halisi","Payment Method":"Njia ya Malipo",
    "Payment Type":"Aina ya Malipo","Invoice":"Ankara","Invoice ID":"Namba ya Ankara","Due Date":"Tarehe ya Mwisho","Discount":"Punguzo","Fine":"Faini","Paid Date":"Tarehe ya Malipo",
    "Due Fees":"Ada Inayodaiwa","Paid Fees":"Ada Zilizolipwa","Total Fees":"Jumla ya Ada","Fee Discount":"Punguzo la Ada","Exam Date":"Tarehe ya Mtihani","Exam Name":"Jina la Mtihani",
    "Start Time":"Muda wa Kuanza","End Time":"Muda wa Kuisha","Duration":"Muda","Room":"Chumba","Max Marks":"Alama za Juu","Min Marks":"Alama za Chini","Marks Obtained":"Alama Alizopata",
    "Grand Total":"Jumla Kuu","Percent (%)":"Asilimia (%)","Book Name":"Jina la Kitabu","Author":"Mwandishi","Publisher":"Mchapishaji","Price":"Bei","Qty":"Idadi","Available":"Inapatikana",
    "Issue Date":"Tarehe ya Kutoa","Return Date":"Tarehe ya Kurudisha","Issue Book":"Toa Kitabu","Book Return":"Rudisha Kitabu","Leave Type":"Aina ya Likizo","Apply Date":"Tarehe ya Ombi",
    "User type":"Aina ya Mtumiaji","Capacity":"Uwezo","Code":"Msimbo","Symbol":"Alama","Country":"Nchi","City":"Mji","State":"Mkoa","Zip Code":"Msimbo wa Posta","Website":"Tovuti",
    "Password":"Nenosiri","Confirm Password":"Thibitisha Nenosiri","Select Role":"Chagua Jukumu","Remember me":"Nikumbuke","Forgot Password?":"Umesahau Nenosiri?","Log In":"Ingia",
    "or login as":"au ingia kama","Create Account":"Fungua Akaunti","Send":"Tuma","Clear All":"Futa Yote","Block":"Zuia","Suspend":"Simamisha","Activate":"Washa","View Details":"Tazama Maelezo",
    "View Student":"Tazama Mwanafunzi","View Teacher":"Tazama Mwalimu","View Guardian":"Tazama Mlezi","View Payslip":"Tazama Hati ya Mshahara","View Request":"Tazama Ombi","Collect Fees":"Kusanya Ada",
    "Proceed to pay":"Endelea Kulipa","Join Class":"Jiunge na Darasa","Earning Statistic":"Takwimu za Mapato","Upcoming Sessions":"Vipindi Vijavyo","User Activity":"Shughuli za Watumiaji",
    "Top Instructor":"Walimu Bora wa Mtandao","Recent Enrolled Courses":"Kozi za Karibuni","Course Activity":"Shughuli za Kozi","Class Routine":"Ratiba ya Darasa","Yes, Suspend":"Ndiyo, Simamisha",
    "Yes Return":"Ndiyo, Rudisha","Row Per Page":"Safu kwa Ukurasa","Entries":"Rekodi","Previous":"Iliyopita","Next":"Inayofuata","Emergency Leave":"Likizo ya Dharura","Medical Leave":"Likizo ya Ugonjwa",
    "Notification ":"Arifa","Hello":"Habari","Select a Category":"Chagua Kundi","Search...":"Tafuta...","Write message":"Andika ujumbe","Write note...":"Andika dokezo...","Overview":"Muhtasari","Yes Delete":"Ndiyo, Futa","Save Attendance":"Hifadhi Mahudhurio","Issue Book":"Toa Kitabu","Book Return":"Rudisha Kitabu","More":"Zaidi","Pdf":"PDF","Excel":"Excel","Collect Fees":"Kusanya Ada","Simple, Transparent Pricing":"Bei Rahisi na Wazi","Popular":"Maarufu","Get started":"Anza Sasa","Annually":"Kila Mwaka","What's included":"Kinachojumuishwa","/month":"/mwezi","/year":"/mwaka","Confirm subscription":"Thibitisha usajili","Confirm":"Thibitisha","Basic":"Msingi","Enterprise":"Biashara Kubwa","For individuals":"Kwa watu binafsi","For startups":"Kwa kampuni changa","For big companies":"Kwa kampuni kubwa","Subscription Plan ":"Mpango wa Usajili","S.L":"Na.","Special":"Maalum","Physically Challenged":"Mahitaji Maalum","Subject Teacher":"Mwalimu wa Somo","Principal":"Mkuu wa Shule","Vice Principal":"Makamu Mkuu wa Shule","Head Teacher":"Mwalimu Mkuu","Class Teacher":"Mwalimu wa Darasa","Accountant":"Mhasibu","Cash":"Fedha Taslimu","Bank":"Benki","Online":"Mtandaoni","Cheque":"Hundi","Holiday":"Sikukuu","Halfday":"Nusu Siku","Late":"Amechelewa","Present":"Yupo","Absent":"Hayupo","Half Day":"Nusu Siku",
    "Manage your school, track attendance, exams, fees and more":"Simamia shule yako: mahudhurio, mitihani, ada na zaidi",
    "Track your classes, results, attendance and notices":"Fuatilia madarasa, matokeo, mahudhurio na matangazo yako",
    "Manage your classes, students and marks":"Simamia madarasa, wanafunzi na alama zako",
    "Track student progress, attendance and academic performance in one place":"Fuatilia maendeleo, mahudhurio na ufaulu wa mwanafunzi mahali pamoja",
    "Manage courses, students, assignments and performance metrics":"Simamia kozi, wanafunzi, kazi na vipimo vya ufaulu"
  };
  var RULES = [[/^Delete (.+)$/, "Futa $1"], [/^Are you sure you want to delete this (.+)\?$/, "Una uhakika unataka kufuta $1 hii?"], [/^Class (\d+) \((\w)\)$/, "Darasa $1 ($2)"], [/^Class (\d+) \((\d{4}-\d{2})\)$/, "Darasa $1 ($2)"], [/^Add New (.+)$/, "Ongeza $1 Mpya"], [/^Add (.+)$/, "Ongeza $1"], [/^Edit (.+)$/, "Hariri $1"], [/^Enter your (.+)$/i, "Weka $1"], [/^Enter (.+)$/i, "Weka $1"],
    [/^(.+) List$/, "Orodha ya $1"], [/^(.+) Details$/, "Maelezo ya $1"], [/^View (.+)$/, "Tazama $1"], [/^Total (.+)$/, "Jumla ya $1"]];
  var KEY = "sp_lang";
  function tr(s, depth) {
    var t = s.trim();
    if (!t) return null;
    if (D[t]) return D[t];
    if ((depth || 0) < 2) for (var i = 0; i < RULES.length; i++) {
      var m = t.match(RULES[i][0]);
      if (m) { var rest = tr(m[1], (depth || 0) + 1) || m[1]; return t.replace(RULES[i][0], RULES[i][1].replace("$1", rest)); }
    }
    return null;
  }
  var SKIP = {SCRIPT: 1, STYLE: 1, TEXTAREA: 1, CODE: 1};
  function walk(root, toSw) {
    var w = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null), n, list = [];
    while ((n = w.nextNode())) list.push(n);
    list.forEach(function (n) {
      if (!n.parentNode || SKIP[n.parentNode.nodeName]) return;
      if (toSw) {
        var o = n.__sp_orig !== undefined ? n.__sp_orig : n.nodeValue, r = tr(o);
        if (r) { n.__sp_orig = o; var lead = o.match(/^\s*/)[0], trail = o.match(/\s*$/)[0]; n.nodeValue = lead + r + trail; }
      } else if (n.__sp_orig !== undefined) { n.nodeValue = n.__sp_orig; delete n.__sp_orig; }
    });
    root.querySelectorAll("[placeholder]").forEach(function (el) {
      if (toSw) { var o = el.__sp_ph || el.getAttribute("placeholder"), r = tr(o); if (r) { el.__sp_ph = o; el.setAttribute("placeholder", r); } }
      else if (el.__sp_ph) { el.setAttribute("placeholder", el.__sp_ph); delete el.__sp_ph; }
    });
  }
  var busy = false, timer;
  function apply(lang) {
    busy = true;
    walk(document.body, lang === "sw");
    document.documentElement.lang = lang;
    var flag = document.querySelector(".flag-nav > a img");
    if (flag) flag.src = lang === "sw" ? "assets/img/flags/tz.svg" : "assets/img/flags/us-flag.svg";
    setTimeout(function () { busy = false; }, 50);
  }
  function set(lang) { try { localStorage.setItem(KEY, lang); } catch (e) {} apply(lang); }
  document.addEventListener("click", function (e) {
    var a = e.target.closest("[data-lang]");
    if (a) { e.preventDefault(); set(a.getAttribute("data-lang")); }
  });
  document.addEventListener("DOMContentLoaded", function () {
    var lang = "en"; try { lang = localStorage.getItem(KEY) || "en"; } catch (e) {}
    if (lang === "sw") setTimeout(function () { apply("sw"); }, 150);
    new MutationObserver(function () {
      if (busy) return; var cur = "en"; try { cur = localStorage.getItem(KEY) || "en"; } catch (e) {}
      if (cur !== "sw") return; clearTimeout(timer); timer = setTimeout(function () { apply("sw"); }, 120);
    }).observe(document.body, {childList: true, subtree: true});
  });
  window.SomaPlusLang = {set: set};
})();
