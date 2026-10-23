# Smoke test

Manual checks to run after a deploy or a significant merge. Each PR that adds a feature adds its steps here.

## Skeleton

1. `docker compose up -d` starts PostgreSQL, Redis and Mailpit; `docker compose ps` shows all three running.
2. `php artisan migrate --seed` completes without errors.
3. `php artisan serve` starts, and the central address `http://localhost:8000` sends you to the sign-in page showing the Somaplus logo, with no broken styles.
4. A template asset loads, for example `/assets/css/somaplus.css` returns 200.
5. The logo of the application loads and shows the whole name: `/img/somaplus-logo.svg` returns 200, and the sidebar and the header show "SomaPlus" with the last letter of "Plus" cut off. The application's own stylesheet `/css/somaplus-app.css` returns 200.
6. `vendor/bin/pest`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --level=5`, `composer audit` and `npm run build` all pass.
7. The template is unchanged. Clone `https://github.com/Swai-D/merge-temp`, check out `b716aa1`, then from the repository root both `diff -r <clone>/somaplus/assets public/assets` and `diff -r <clone>/somaplus resources/template-original -x assets` print nothing.

## Tenancy and plans

Create two schools and a plan from `php artisan tinker`:

```php
$plan = App\Models\SubscriptionPlan::where('key', 'basic')->first();
App\Models\Tenant::create(['name' => 'Acme', 'slug' => 'acme', 'subscription_plan_id' => $plan->id]);
App\Models\Tenant::create(['name' => 'Beta', 'slug' => 'beta', 'subscription_plan_id' => $plan->id]);
```

1. `php artisan migrate --seed` creates three plans: basic, medium and enterprise (`SubscriptionPlan::count()` is 3). Running the seeder again does not duplicate them.
2. `http://localhost:8000` (central domain) and `http://acme.localhost:8000` both show the sign-in page. `http://nobody.localhost:8000` answers 404.
3. Sending `X-Tenant: acme` to the central domain does not change the tenant.
4. Set `subscription_status` of Acme to `read_only`: the sign-in page still loads.

## Users and roles

From `php artisan tinker`, with the two schools Acme and Beta from the previous section:

```php
$acme = App\Models\Tenant::where('slug', 'acme')->first();
$id = App\Tenancy\TenantContext::run($acme, function () {
    $user = App\Models\User::factory()->create();
    $user->assignRole('teacher');
    dump($user->tenant_id, $user->hasRole('teacher'));

    return $user->id;
});
```

1. A new school has five roles: `Spatie\Permission\Models\Role::where('tenant_id', $acme->id)->pluck('name')` lists owner, head_teacher, teacher, accountant and parent_student.
2. The snippet above prints Acme's id and `true`.
3. Outside any school, `App\Models\User::count()` counts only super-admins, not the user just created.
4. Inside Beta (`TenantContext::run($beta, ...)`), `App\Models\User::count()` does not include Acme's user, and `hasRole('teacher')` on that user, loaded with `App\Tenancy\TenantContext::runAsSystem(fn () => App\Models\User::find($id))`, is `false`.
5. `php artisan tinker` shows the same email can be used at Acme and at Beta, but twice at Acme is refused.

## Settings > Users and custom roles

1. As owner open Settings > Users: everyone of the school is listed with a role badge, nobody from another school. Add a user (name, email, role Teacher, password of 8+ characters): the person appears, can sign in at the school address and sees the Teacher menu. The same email is refused a second time in this school but accepted at another school. Edit the user: change the role to Accountant (the menu changes at once); leave the password empty and the old password still works. Try to change the only owner to Teacher: refused, \"at least one owner\". Head teacher, teacher, accountant and parent get 403 on the page.
2. On the **Basic** plan Settings has no Roles entry and `/settings/roles` answers 403. On **Enterprise** open Settings > Roles: the five default roles are listed as Default, without edit or delete.
3. Add a role `Registrar` with only \"Add and change students...\": the form ticks \"Look at students...\" for you and will not let you untick it. Save. The role shows as Custom with 0 people and 2 permissions. `settings.manage` is not offered anywhere.
4. Edit the user: give them Registrar. Sign in as that user: the menu shows Students (list, add, import, ID cards) but no Fees, Exams or Settings; `/fees/invoices` and `/settings/users` answer 403; the profile menu and dashboard say Registrar.
5. Edit Registrar and remove \"Add and change students\": the user loses the Add student page at once. Try to name a role \"Teacher\", \"Mwalimu\" or `registrar` (again): refused. Try to delete Registrar while the user holds it: refused; give the user another role, then delete works. Every step above has a line in the audit log (`role.created`, `role.updated`, `role.deleted`, `user.created`, `user.role_changed`) with ids only and no password.
6. In a read-only school both pages open and every save answers 403. In Kiswahili everything is in Swahili.

## Sign-in, language and layout

Create a user for Acme from `php artisan tinker` (password `password`):

```php
App\Tenancy\TenantContext::run(App\Models\Tenant::where('slug', 'acme')->first(), function () {
    App\Models\User::factory()->create(['name' => 'Asha', 'email' => 'asha@acme.test'])->assignRole('head_teacher');
});
App\Models\User::factory()->superAdmin()->create(['name' => 'Root', 'email' => 'root@somaplus.test']);
```

1. `http://acme.localhost:8000` redirects to `/login`, which names the school. Signing in as `asha@acme.test` opens the dashboard with Acme's name, its plan, the subscription status and the role "Head teacher".
2. The same email and password on `http://localhost:8000/login` or on Beta's address is refused.
3. `root@somaplus.test` signs in only on `http://localhost:8000`; the dashboard shows the number of schools.
4. Five wrong passwords in a row block further attempts for about a minute, even with the right password.
5. The flag menu in the header switches between English and Kiswahili; menu, dashboard and error messages change, and the choice survives signing out.
6. Set Acme to `read_only`: a yellow notice shows on every page, and signing in, signing out and reading still work.
7. Dashboard figures: as the head teacher, the tiles show active students (not those who left), guardians, teachers and classes, `+N this month` for students admitted this month, and "Attendance today" splits today's register by status (or says none was recorded). The "People" doughnut counts pupils, teaching accounts and staff accounts, "Students by class" shows how many pupils each class holds in the current year, "Attendance of the last 30 days" draws the register of the month, and "Top students" lists the best pupils of the last locked exam with their average and their class position (it says no exam has results yet when there is none). As the accountant, the fee tiles, "Fees by month" chart and "Recent payments" show; reversing a payment on the Payments page lowers "Fees collected" and raises "Fees outstanding" by its amount. A teacher sees no fee blocks, an accountant no attendance block, and a parent or student account sees only the "Your school" card. Switch to Kiswahili: every dashboard label changes.
8. Dashboard submenu: the sidebar's Dashboard opens School and, for owner, head teacher and teacher, Teacher. The Teacher page shows school-wide student and class counts, the marks that person entered, open exams, and a table of this year's exams with Enter marks (open) or Results (locked). A person the school gave a teacher record shows what they teach there (subjects and classes, "Nothing assigned yet" without one). A person who may see the library also sees the Library block: copies on the shelf, copies out and the overdue ones in red, with a link to the copies out. An accountant opening `/dashboard/teacher` gets a 403. Switch to Kiswahili: the page changes.
9. The sidebar shows only "Dashboard" and "Help". Other items appear as the features that own them are built.
10. "Log out" in the profile menu returns to `/login`.
11. Top bar: the orange "Add new" button opens the pages of the role. As owner: add student, import students, take attendance, new exam, grading scale, fee structure and record payment. As teacher: only "Take attendance". A parent or student account has no quick menu at all. The bell has a number when there is something to report: set the school to `grace` and the notice names the day the grace period ends, an exam of this year that is not locked and an invoice that is not paid in full are reported (the money one only to the accountant, the head teacher and the owner). A school in `read_only` is told so in the bell. The gear opens the settings for the owner and is not there for the other roles.
12. The search of the top bar finds a student by name or admission number and lands on the student list; the address bar shows no name (the search is a POST). A parent or student account has no search box. Clicking the profile shows the name, the role and the school, with Settings, Help and Log out.

## Help centre

1. "Help" in the sidebar opens `/help` with a search box, the topic cards (Getting started, Academics, Account and roles, Plans and subscription, Problems and answers) and the contact card.
2. Opening an article shows its text with "On this page", the other articles of the topic and previous/next buttons; links to other articles work.
3. Searching "stream" finds "Classes and streams"; after switching to Kiswahili, "mkondo" finds "Madarasa na mikondo"; nonsense shows "No guide found".
4. After switching to Kiswahili, topics and articles are in Swahili.
5. `/help/academics/nothing-here` answers 404; without signing in, `/help` redirects to `/login`.

## Password reset

Mail goes to Mailpit (`http://localhost:8025`) with the `MAIL_*` values of `.env.example`.

1. On `http://acme.localhost:8000/login`, "Forgot password?" opens the forgot-password page naming the school.
2. Asking for a link for `asha@acme.test` shows "If that email address belongs to this school…" and an email arrives in Mailpit with a link to `acme.localhost`.
3. Asking for an address that does not exist shows the same message and sends nothing.
4. Opening the link, choosing a password of 8 or more characters (twice) and saving returns to sign-in with a success message; the new password signs in, the old one does not.
5. Using the same link again, or the same link on Beta's address, answers "This reset link is not valid or has expired."
6. After switching to Kiswahili, the pages and the email are in Swahili. Help has the article "Forgot your password".

## Academic structure

Sign in at `http://acme.localhost:8000` as the head teacher (`asha@acme.test`, created in the sign-in section).

1. The sidebar has a section Academics (Masomo) like the template: **Academic years**, and **Classes** which opens to **Class list** and **Subjects**. Every "add" form is a pop-up of the page (the page is not loaded again): the orange button opens it, and a form that is refused opens the pop-up again with what was typed. `/students/create`, `/exams/create`, `/grading-scales/create`, `/fees/structures/create`, `/fees/payments/create`, `/settings/users/create` and `/settings/roles/create` open the list with the pop-up ready. Streams are managed inside each class.
2. Academic years: add `2026` from 2026-01-01 to 2026-12-31 and mark it current. Adding `2026-27` from 2026-12-31 is refused (overlap); `2027` from 2027-01-01 is accepted. Marking `2027` current removes the badge from `2026`.
3. Open `2026`: add `Term 1` (2026-01-12 to 2026-04-03) and `Term 2` (2026-05-04 to 2026-08-14). A term dated 2025-12-01 to 2026-03-01 is refused (outside the year); one starting 2026-04-03 is refused (overlap). The dashboard shows the current year and, when today is inside a term, that term.
4. Deleting `2026` while it has terms is refused with a message; after deleting both terms it works.
5. Classes: add `Form 1` (order 1) and `Form 2` (order 2); they are listed in that order. Open `Form 1` and add streams `A` and `B`; a second `A` is refused, but `Form 2` can have its own `A`. Deleting `Form 1` while it has streams is refused.
6. Subjects: add `Mathematics` with code `MATH`; a second `Mathematics`, or another subject with code `MATH`, is refused. A subject without a code is fine.
7. Sign in as a teacher: all pages can be opened, but there are no add, edit or delete buttons, and posting a change by hand answers 403. A parent / student sees no Academics menu and gets 403 on `/academics/years`.
8. Set the school to `read_only`: pages open, changes answer 403.
9. At Beta's address nothing of Acme's years, classes or subjects is visible, and Acme's record ids give 404.

## Students and guardians

1. Sign in as owner. The sidebar has Academics > Students > Student list and Add student. Create two classes, a stream and a current academic year first.
2. Add a student with a class and stream. The student page shows the details and one line of class history.
3. Place the same student in the next academic year (promotion) and in another class (move): each adds a line, earlier lines stay. Sending the same placement twice adds no line.
4. Add a guardian with a phone number as main contact. Add the same phone to a second student: the parent is reused, not copied. Remove it from both: the guardian disappears.
5. Search "ama" in the student list: the address stays `/students/search` and shows no name. The class filter works for the current year.
6. Mark the student as left: gone from the list, found under "Left school"; placing is refused until "Bring back to school".
7. Delete a class or stream that has students: refused with a message.
8. Teacher and accountant can open the list and the pages but see no Add student, no forms, and posting answers 403. A parent/student gets 403 on `/students`.
9. In Kiswahili all pages are in Swahili; Help has the topic "Wanafunzi" with eight articles.
10. Bulk import: as owner open Academics > Students > Import students. Download the template and open it in Excel: the header row and two example rows (using your first class) show. Upload it unchanged: two students are added, placed in the current year, each with a main contact. Upload it again: refused, "admission number already used", nothing added. Put a wrong class name, a bad date and a missing last name in three rows: nothing is added and each line number is listed. Day-first dates (06/05/2012) and a phone that lost its zero (755123456) are accepted. As a teacher the menu entry and page are absent (403), and in a read-only school the page opens but uploading answers 403.
11. Admission numbers: as owner open Settings > Admission numbers (head teacher, teacher, accountant get 403). Choose "Make them for me", prefix `sch`, year Full, separator Dash, 4 digits: the dark box shows `SCH-<year>-0001` and updates while you change the controls. Save. Add student: the number field is gone, a note says what the number will be; after saving the student has it, the edit page shows it read-only and a typed number in a hand-made request is ignored. Add a student admitted next year: `SCH-<next year>-0001`. Set "Start counting at" 250 with a year-less format: the next student gets 0250; a lower value later does not move it back. Bulk import: empty number cells get numbers, a typed old number is kept and not repeated; the template's example rows have an empty number. Switch back to "I type them myself": the field returns and is required. In a read-only school the page opens and saving answers 403. Switch to Kiswahili: everything is in Swahili.
12. ID cards: open a student: **Print ID card** shows (owner, head teacher only; a teacher opening the same student sees no button and gets 403 on `/students/<id>/id-card`). The card opens in a new tab with the school name, name, admission number, class + stream, year, initials in a circle and a barcode; no date of birth. Browser print preview at 100% on A4 shows 8 cards per page. Menu Academics > Students > Print ID cards: choose a class, only its active students are listed and ticked; untick one and **Print selected cards**: only the ticked ones print. A student who left is not listed and `/students/<id>/id-card` answers 404. Each card printed adds a `student.id_card_printed` audit line. Works in a read-only school. In Kiswahili the card and pages are in Swahili.
13. The whole flow in one pass: owner picks automatic numbers (step 11) > import a CSV with three students and parents (step 10, number cells empty) > Finance > make the class invoices > record a payment and print the receipt > print the class ID cards. Every student has a number, a placement, a parent, an invoice and a card.
14. Guardians: the sidebar has Academics > Guardians with **Add New Guardian** and **Guardians List** (a teacher sees the list but not "Add New Guardian"; a parent/student sees neither and gets 403 on `/guardians`). The list is a datatable of the template: its own search box sits in the header of the card, with the page length, the sorting of the columns and the paging. **Add New Guardian** and the pencil of a row open a pop-up (the page is not loaded again; the pencil of the row fills the pop-up with that guardian). In the pop-up, a name, a phone and an email are typed and saved: a phone that is already in the school is refused and the pop-up opens again with what was typed. On the guardian's page, link a pupil with a relationship and as main contact; the pupil is listed with its class, and **Take off** unlinks them. A guardian who is left with no pupil is deleted; a guardian with a pupil is not (the delete is refused with a message).
15. `select action, subject_type, subject_id, user_id from audit_logs` shows the actions above and no names.

## Teachers

1. Sign in as owner. The sidebar has Academics > Teachers with **Add teacher** and **Teacher list**. A teacher, an accountant and a parent/student do not see the entry and get 403 on `/teachers`; the head teacher sees the list and can add a teacher too.
2. Add a teacher: the form is a pop-up of the list (the page is not loaded again). Fill in the name, the email and a password of 8+ characters, the phone and the staff number `TCH-001`, tick a subject and a class, save. The teacher is in the list with the name, the staff number, the phone, the subject and the class.
3. The same email in the same school is refused, and so is the same staff number; the pop-up opens again with what was typed. An email that is already used at another school is accepted.
4. The person signs in with that email and password and has the teacher menu (marks, attendance, and Library to look at: the pages open without the add or change buttons). Opening an exam's mark page, the **Class**, **Stream** and **Subject** lists hold only what was ticked in step 2. Entering marks for one of those saves; a hand-made post for a subject or a class the school did not tick answers 403 and saves nothing. The owner and the head teacher see and write everything.
5. Open the teacher: the page shows the details, what the teacher teaches and the account. **Edit** opens a page; leave the password empty and the old password still works, type a new one and it replaces it.
6. Search "ama" in the list: the address stays `/teachers/search` and shows no name. The filter shows only the teachers on duty or only those out of duty.
7. **Put out of duty**: the teacher is marked as such in the list and the filter finds them. **Back in duty** undoes it.
8. **Take out of the school**: the record and what it teaches are gone, the account stays and has no role any more, so the person can be given another role in Settings > Users. The marks the person entered keep their name.
9. In a read-only school the pages open and every change answers 403. A teacher of another school gives 404.
10. In Kiswahili all the pages are in Swahili; Help has the topic "Walimu" with one article.
11. `select action, subject_id from audit_logs` shows the teacher actions with ids only and no names.

## Library

1. Sign in as owner. The sidebar has Academics > Library with **Add book**, **Book list**, **Library members** and **Issue and return**. The head teacher sees everything too. A teacher sees the pages (view only: no add, no edit, no delete, every posted change answers 403, and **Print library card** works) but an accountant and a parent/student neither see the entry nor get the pages.
2. Add a book: the form is a pop-up of the book list. Fill in the name, a subject of the school, the number `BK-001`, the rack, 3 copies and the price `5500.50`; save. The book is in the list with **On the shelf: 3**. A price of another currency is saved with the decimals of the school.
3. The same book number in the same school is refused, and so is a book without a name or copies; the pop-up opens again with what was typed.
4. Add a member under **Library members**: choose a pupil (only the pupils without a card are in the list) and give the card `LIB-0001`. The list shows the pupil, their class, the phone of the guardian and the card. A second card for the same pupil and a second `LIB-0001` are refused.
5. Under **Issue and return**, use **Issue a book**: the member is in the list with their card, only the copies on the shelf are choosable, and the **Return due** holds today plus 14 days. Save. The book now shows **On the shelf: 2** and the row shows the due day.
6. Issue the last copy again for another member: the form refuses. A day in the future is refused too, and a return day before the issue day as well.
7. **Bring back** on the open issue: the copy is on the shelf again and the row shows the day. Pressing it twice is refused.
8. Open a member: the page shows the card, the class, the phone of the guardian and every borrowed book with its due day, brought back or still out. Issue one copy with a return day of yesterday: the list and the member's page mark the row **Overdue** in red. The search on each page is a POST to `/library/{books|members|issues}/search` and needs two letters.
9. Open a member and press **Print library card**: a new tab shows the card on a sheet, with the school name, the name of the pupil, the card number with a barcode, the class and the year; browser print at A4 puts two cards across. Each print leaves an audit line `library.card_printed`. An accountant opening the card address gets 403; a teacher opening their member's card works.
10. **Delete** a book the library ever lent, or a member the library ever gave a book: refused, because the history of the school stays. A book and a member without a record can be deleted.
11. In a read-only school the pages open and every change answers 403. A member of another school gives 404.
12. In Kiswahili all the pages are in Swahili; Help has the topic "Maktaba" with one article.
12. `select action, subject_id from audit_logs` shows `book.*`, `member.*` and `issue.*` with ids only and no names.

## Attendance

1. Sign in as a teacher. The sidebar has Academics > Attendance > Take attendance and Summary. An accountant does not see Attendance and gets 403 on `/attendance`.
2. Choose a class, a stream and today: the students placed in that class are listed, all as Present. A student who left school, or who is placed in another class, is not listed.
3. Mark one Absent with the note "Sick" and save. Open the same class and day again: the marks are shown. Change one and save: the row is updated, no duplicate (`select count(*) from attendance_records` stays the same).
4. A date in the future, or outside every academic year, is refused with a message.
5. Summary: choose the class and dates; counts per status and the attendance rate (present + late out of present + late + absent) are shown.
6. Move a marked student to another class on the student page: the old register still shows the old class.
7. In a read-only school the pages open but saving answers 403.
8. In Kiswahili all pages are in Swahili; Help has the topic "Mahudhurio" with two articles.
9. `select action, subject_id from audit_logs` shows `attendance.recorded`.

## Timetable

It needs the academic year with its term, the classes and streams, the subjects and the teachers of the sections above.

1. Sign in as owner. The sidebar has Academics > Timetable with Class timetable, Teacher timetable, My timetable and Lesson slots. The head teacher sees the four pages and can change everything. A teacher sees the first three pages but has no Lesson slots, and every posted change (add lesson, edit, delete, copy, slots) answers 403. An accountant and a parent/student see no entry and get 403 on `/timetable`.
2. Class timetable with no term in the year says "Add an academic year with a term first"; with a term but no slots it says the school has no lesson slots, with a link to Lesson slots.
3. Lesson slots: add Period 1 from 08:00 to 08:40, Period 2 from 08:40 to 09:20, then the Tea break from 09:20 to 09:40 with **This is a break** ticked. A slot that ends before it starts, one that overlaps another slot of the day, or the same name twice is refused. Every class uses this one list.
4. Class timetable, choose the term and Form 1: the grid holds the days Monday to Friday. **Add a lesson**: whole class (no stream), Mathematics, the teacher whose subject and class were assigned under Teachers, Monday, Period 1, Room 5. The card shows the subject, the teacher, the room and 08:00 - 08:40.
5. A second lesson for Form 1 in Monday Period 1 (any subject) is refused; so is a lesson for stream A while the whole class has that slot, and the other way round. A teacher with a lesson for Form 2 in the same slot, or the same room used there, is refused. The Tea break as a slot is refused. A teacher out of duty, or one who was not assigned this subject or class under Teachers, is refused; a teacher with nothing assigned yet is accepted for anything. Each message says which of these it is.
6. Add a lesson on Saturday: the Saturday column appears from then (Sunday as well). A day without lessons shows nothing.
7. Teacher timetable: choose the teacher, the same lessons show with the class of each one. As the teacher, My timetable shows their own week; a person without a teacher record is told there are no lessons to show.
8. Copy a term: copy Term 1 into Term 2. The message counts what was copied and what was skipped (a clash, or a teacher now out of duty), the class timetable of Term 2 shows the same lessons, and copying again adds nothing.
9. Deleting a term, a class, a stream, a subject or a slot that still holds lessons is refused with a message; a slot in use cannot be turned into a break either, and moving it needs its lessons moved first.
10. **Print** on the class, teacher, mine and family timetables: the controls, the header and the sidebar disappear in the print preview, which at A4 shows a one-week grid; saving the print preview as a PDF keeps the same.
11. Take the teacher of step 4 out of the school: the lesson stays on the timetable with **No teacher yet**.
12. Open the child of a parent that has access: a **Timetable** card/button opens the week of the child (class and stream, with the term choosable). A child of another parent answers 404; a teacher is forbidden.
13. In a read-only school the pages open and every change answers 403. A class, a teacher or a child that is not of this school answers 404.
14. Kiswahili: all the timetable pages are in Swahili; Help has the article "Ratiba ya masomo" (Timetable) under Masomo.

## Exams, marks and grades

1. Sign in as owner. Academics > Exams has Exams and Grading scales. Add the scale "Standard": 0 = F, 50 = D, 60 = C, 70 = B, 80 = A. Saving without a line at 0, with two lines at the same percentage or the same grade is refused.
2. Add an exam "Midterm" for a term, out of 100, with that scale. Add a second exam out of 50.
3. Open the exam, choose a class and a subject: the students placed in that class are listed, not students who left or are in another class.
4. Enter 85, 79.99, 80, 0, `45,5` and tick Absent for one student, then Save. Grades shown: 85 = A, 80 = A, 79.99 = B, 0 = F, 45.5 = F, absent shows "Absent". `abc`, `-1`, `1e2`, `45.555` and 100.01 are refused and nothing from that page is saved.
5. In the exam out of 50, 40 is an A and 39.99 is a B.
6. Save the same page again: no duplicate marks (`select count(*) from exam_marks`). Leave a score empty and save: the stored mark stays.
7. Edit the exam: the term, maximum and scale are fixed once marks exist; the name can change. The exam with marks cannot be deleted. The scale cannot be edited or deleted; "Copy" makes an editable copy and the exam's grades do not change.
8. Lock the exam: saving marks is refused for everyone, editing and deleting are refused, and a teacher sees no Lock/Unlock buttons and gets 403 on them. Unlock as owner: marks can be saved again.
9. A teacher can open Exams and enter marks but cannot add scales or exams. An accountant and a parent/student get 403 on `/exams`.
10. In a read-only school the sheet opens but has no Save button and posting answers 403.
11. Delete a term with an exam, a subject with marks, or a year with exams: refused.
12. In Kiswahili all pages are in Swahili; Help has the topic "Mitihani na alama" with three articles.
13. `select action, subject_id from audit_logs` shows the exam and marks actions and no names or scores.

## Results, positions and report cards

1. Add two students to a class and a third to another stream; enter marks in two subjects (for example 90/80, 80/90, 70/60) and one student with a single subject of 100. Open the exam: "Open the results" is not offered while it is unlocked and the results page says the exam must be locked.
2. Lock the exam. Open the results for the class: totals, averages, grades and positions appear; equal averages share a position (1, 1, 3); the one-subject student is ranked by average.
3. Enter 80 and 79.99 for one student: the average shows 79.99 and the grade is the lower one, not A.
4. Mark a student absent in one subject: that subject is not counted. A student with no marks is listed last with no position.
5. Choose one stream: only that stream is listed but positions are still class-wide, and "Position in stream" is inside the stream.
6. Move a student to another class afterwards: the old results do not change.
7. Open a student's page: "Report cards" lists the term; the report shows subjects, grades, total, average, overall grade and positions only for locked exams. Print works.
8. Teacher, head teacher and owner can open results; an accountant and a parent/student get 403 on results and report, and the student page shows no report cards to the accountant.
9. In a read-only school results and report cards still open. A student or exam of another school gives 404.
10. In Kiswahili the pages are in Swahili; Help > Exams has "Matokeo, nafasi na ripoti".
11. `select action, subject_id from audit_logs` shows `results.viewed` and `report.viewed` with ids only.

## Fees: structures and invoices

1. Sign in as accountant. Finance > Fee structures > Add: name "Day scholar", a term and class, lines Tuition 150000 and Uniform 25000.50. The total shows TZS 175,000.50. Amounts like `abc`, `-5`, `0`, `1e3`, `12.345` and `1 000` are refused and nothing is saved. Two lines with the same name are refused.
2. Add the same name again for the same term and class: refused. The same name for another class or term is fine.
3. Open the structure and choose Make invoices. One invoice per active student placed in that class (not students who left or are in another class). Numbers are INV-000001, INV-000002 ... Choose Make invoices again: no new invoice. Add a student to the class and run it again: only that student gets the next number. With a stream chosen only that stream is invoiced.
4. Edit or delete the structure now: refused with a message; before any invoice it can be edited or deleted.
5. Invoices: list, filter by term and class, open one, Print. The page shows no guardian data.
6. On an invoice set a control number of 8 to 20 digits. Letters, spaces and 7 or 21 digits are refused. The same number on a second invoice is refused; empty the field to clear it.
7. Head teacher can look at structures and invoices but gets 403 on add, edit, make invoices and control numbers. A teacher and a parent/student get 403 on every fee page and do not see the Finance menu.
8. In a read-only school structures and invoices open but nothing can be changed (403). An invoice or structure of another school gives 404.
9. Deleting a term or class that has a fee structure is refused.
10. Kiswahili: Fedha > Ankara and Miundo ya Ada are in Swahili; Help has the topic "Ada na ankara".
11. `select action, subject_id from audit_logs` shows the fee actions with ids only; `select number from invoices` has no gaps.

## Fees: payments and receipts

1. As accountant open an invoice of TZS 100,000. Record 40,000 cash with a reference: a receipt REC-000001 opens; the invoice shows paid 40,000, balance 60,000, status "Part paid". Print the receipt.
2. Record 60,000.01: refused (more than the balance) and no receipt number is used. Record 60,000: the invoice is "Paid" and the payment form disappears.
3. Try amounts `abc`, `-5`, `0`, `1e3`, `12.345`: refused. A future date and an unknown method are refused.
4. Go back in the browser and send the same form again (or double click): no second payment, the message says it was already recorded.
5. Finance > Payments > Record by control number: set a control number on another invoice first, then type it with an amount: the payment goes to that invoice. An unknown number is refused.
6. As owner reverse a payment with a reason: it is struck out, the invoice balance recovers, the receipt stays (marked reversed) and receipt numbers continue without a gap. The accountant sees no Reverse button and gets 403 on it.
7. Head teacher can look at payments and receipts but not record; a teacher and a parent/student get 403 and do not see Payments in the menu.
8. In a read-only school payments and receipts open but recording and reversing give 403. A receipt or payment of another school gives 404.
9. Kiswahili: Fedha > Malipo is in Swahili; Help > Ada na ankara has "Malipo na risiti".
10. `select action, subject_id from audit_logs` shows `payment.recorded`, `payment.reversed`, `receipt.viewed` with ids only; `select number from receipts` has no gaps.

## Collections report

1. As accountant open Finance > Fees > Collections report: it opens on today and shows Collected, Still owed on invoices today and Reversed. Record a cash payment of 10,000 and a mobile money payment of 20,000 (as source integration if you have a test notice): Collected is 30,000, by method and by source add up to it.
2. Choose a From/To range of several days: only payments with a date paid inside it count, both end dates included; By day lists each day.
3. Reverse a payment: it leaves Collected and appears under Reversed.
4. Send a provider notice for a control number no invoice has: it is listed under "Notices that were not recorded"; set the number on an invoice and resend: it is recorded and leaves the list.
5. A range over a year, a reversed range or a bad date is refused. Head teacher and owner can open the report; a teacher and a parent/student get 403 and do not see the menu entry.
6. Kiswahili: Fedha > Ada > Ripoti ya mapato and Help > Ada na ankara > "Ripoti ya mapato" are in Swahili.

## Settings > Integrations and payment notices

1. As owner open Settings > Integrations: payments is switched off and has no provider. Head teacher, accountant and teacher get 403 and do not see the menu entry.
2. Choose "Signed web notice", leave the secret empty, switch on, Save: a yellow box shows the new secret once. Reload: the secret is not shown, the field says it is saved.
3. Save again with the field empty: the secret stays the same. Type a secret shorter than 16 characters: refused. Switch on without a provider: refused.
4. Give an invoice control number 991234567890. Send a signed notice (HMAC-SHA256 of `timestamp.body`, headers `X-Somaplus-Timestamp` and `X-Somaplus-Signature`) to the address shown: 200 `applied`; the invoice shows the payment and a receipt exists, method Bank. Send it again: 200 and still one payment.
5. Change one digit of the amount without re-signing: 401. Wrong secret or a timestamp older than 5 minutes: 401. Unknown school id or switched-off integration: 404.
6. Send a notice for a control number no invoice has: "No invoice found"; set the number on an invoice, send again: it is recorded. Currency other than the school's: "Rejected".
7. In a read-only school the notice is still recorded; saving integrations gives 403.
8. Kiswahili: Mipangilio > Miunganisho is in Swahili; Help > Ada na ankara has "Taarifa za malipo kutoka benki au mtoa huduma".
9. `select secrets from tenant_integrations` shows encrypted text, never the secret; `select action, subject_id from audit_logs where action='integration.saved'` has ids only.

## GePG payment callback

1. As owner open Settings > Integrations > Provider "GePG": fill SpCode, SpSysId, paste GePG's public certificate and your private key, switch on, Save. The page shows the callback address `/webhooks/gepg/<school id>`. Reload: the private key box is empty and says it is saved; saving again with it empty keeps it.
2. With a test key pair (the certificate saved above), post a signed `gepgPmtSpInfo` for an invoice's control number: 200 with a signed `gepgPmtSpInfoAck` code 7101, a payment (source integration) and a receipt exist. Post it again: 7101 again and still one payment.
3. Altered amount, wrong signing key or no `gepgSignature`: 401. A different `SpCode`: 422. Unknown school, switched-off or another provider chosen, or a school host instead of the central address: 404.
4. An unknown control number is acknowledged and listed in the notices table as "No invoice found".
5. Kiswahili: the GePG labels and callback note are in Swahili.

## Railway path entry (no custom domain)

1. Set `TENANCY_ALLOW_PATH_ENTRY=true` on Railway and redeploy. Open `https://<app>.up.railway.app/t/<school slug>`: you land on the sign-in page showing the school's name.
2. Sign in as that school's owner: the dashboard opens, and Students, Fees and Settings pages work (they load that school's data).
3. Sign out: the sign-in page still shows the school. Open `/t-exit` to go back to the platform sign-in; the super-admin signs in there.
4. A user of another school cannot sign in through this school's address. With the setting off, `/t/<slug>` gives 404 and the central sign-in only accepts super-admins.

## Selcom collections

1. As owner choose Provider "Selcom", leave the token empty, switch on, Save: the token is shown once; the page lists three addresses (lookup, validation, notification). Reload: the token is not shown.
2. With the token as `Authorization: Bearer <token>`, `POST` the lookup address with `utilityref` = an invoice's control number: result 000 and the student's name. An unknown number: 010.
3. Validation with `amount` 40000: 000; `0`, `abc`: 012. Notification with `reference` SELREF1: 000, a mobile money payment (source integration) and a receipt exist; send it again: still one payment.
4. No token or a wrong token: 401. Unknown school, switched-off integration, another provider or a school host instead of the central address: 404.
5. Kiswahili: the Selcom labels and note are in Swahili; Help > Ada na ankara has "Pokea malipo ya Selcom kiotomatiki".

## AzamPay mobile money

1. As owner choose Provider "AzamPay", Environment Sandbox, fill app name, client ID, client secret and API key, switch on, Save. The callback address `/webhooks/azampay/<school id>` is shown; reload: the two secrets are not shown.
2. As accountant open an invoice of TZS 100,000: the "Ask for payment on the phone" card is there. Send 07XXXXXXXX, network Airtel, amount 40000: "Prompt sent", a row "Waiting for the payer" appears; approve on the test phone.
3. When AzamPay calls back, the row becomes "Paid", the invoice shows a payment (method mobile money) and a receipt exists. A repeated callback changes nothing.
4. A bad phone, an amount above the balance, `40000.50` or `abc` are refused. Sending the same form twice sends one prompt. A teacher or head teacher gets 403; a read-only school gets 403.
5. A callback with a wrong signature gets 401; with another amount than asked the request is "Failed" and nothing is recorded.
6. With another provider chosen the card is gone from invoices. Kiswahili: card, statuses and Help > Ada na ankara > "Omba malipo kwenye simu kwa AzamPay" are in Swahili.

## SMS fee reminders

1. As owner open Settings > Integrations > SMS to parents, provider NextSMS, enter username, password and sender name (3 to 11 characters), switch on, Save. Reload: the password is not shown.
2. As accountant open Communication > Fee reminders: invoices with a balance are listed with the number of guardians that have a phone. An invoice whose guardians have no valid phone cannot be ticked.
3. Tick one invoice, language Kiswahili, Send reminders: the status line shows 1 queued; the guardian's phone receives an SMS naming the school, student and balance; "Latest reminders" shows Sent.
4. Send again the same day: nothing new is sent ("already reminded today"). Record a full payment on the invoice: it disappears from the list.
5. Switch SMS off in Settings: sending is refused with a message pointing to Integrations. A teacher or head teacher gets 403 on the page; a read-only school can open it but cannot send.
6. Kiswahili: page, statuses and Help > Ada na ankara > "Wakumbushe wazazi ada kwa SMS" are in Swahili.

## Communication: notice board, events and messages

1. As owner open Communication in the sidebar: Notices, Events, Messages and Fee reminders sit under the one section (fee reminders used to live under Finance > Fees; they moved to the Communication section).
2. As owner put up a notice (a day, a title and a description). The teacher, the accountant and the head teacher all read the board; a notice whose day is older sits below the newer ones. Edit or take the notice down; a teacher or accountant who posts or deletes gets 403.
3. As head teacher add an event ("Parents meeting" on this month). The calendar marks today and shows the event on its day; the arrows move a month back and forth; "Coming up" lists the meeting at the side; an end before a start is refused.
4. Another school's notice or event id gives 404 on this school's address; the message of another school is never listed.
5. As accountant open Communication > Messages: the reminders sent are listed with the day, the student, the guardian and the status, and the reason for a failed one; the filter of status refuses everything else. Only the owner and the accountant may open it; the teacher and the parent student get 403.

## Family pages (parents)

1. As owner open a guardian that has an email, choose Give access: the guardian receives an email with a link on the school address; open it, choose a password, sign in.
2. The parent lands on "My children" (not the dashboard), sees only the children linked to their guardian record, with the amount owed. The sidebar shows only "My children" and Help.
3. Open a child: invoices (total, paid, balance, control number), payments with receipt numbers, and a button per term. A term's report card shows only locked exams.
4. Another child's address (`/family/children/<id>`) gives 404; staff pages (`/students`, `/fees/invoices`, `/settings/users`) give 403.
5. Unlink the child from the guardian: the parent no longer sees the child. As owner choose Take access away: the parent is signed out and cannot sign in again; the guardian cannot be deleted while access exists.
6. Kiswahili: all family pages and Help > Akaunti > "Wazazi: ona ada na matokeo ya watoto wako" are in Swahili.

## School profile

1. As owner open Settings > School profile: the school name is shown but cannot be edited. Enter phone `+255 712 345 678`, email and address, Save.
2. Open any receipt and any report card: the name, address, phone and email are printed under the school name; print preview shows them too.
3. Empty the phone and Save: it disappears from receipts. A letter in the phone, or a bad email, is refused.
4. A head teacher, teacher or accountant gets 403 on the page; a read-only school can open it but not save. Another school's receipts never show these details.
5. Kiswahili: the page and Help > Kuanza > "Weka mawasiliano ya shule kwenye risiti na ripoti za wanafunzi" are in Swahili.

## Platform: schools

1. Sign in on the central address as the platform administrator (`admin@somaplus.test` in the demo): the menu has Platform > Schools; a school owner never sees it, and opening `/platform/schools` on a school address gives 404.
2. Add a school: name, short name `demo2`, plan, currency KES, owner name and email. After saving, the school is in the list and the owner receives an email whose link starts with the school's address (`demo2.<base domain>`), not the central one.
3. Open the link, choose a password, sign in on the school address: the owner has all owner menus; the school's currency is KES.
4. Reuse the short name `demo2`, or use `admin`, `my school`, a currency `XYZ` or the central domain as own domain: each is refused.
5. Open the school and change the plan, then set the subscription to Grace (a date is required), then Read-only: the owner of that school can still sign in and read but changes get 403. The short name and the currency cannot be changed.
6. Kiswahili: all these pages are in Swahili. On Railway with path entry and no own domain, no email link is sent and the page says so.

## Production on Railway (HTTPS styling)

1. Open the deployed address in a browser: the sign-in page shows the Somaplus styling (Bootstrap look, logo), not plain unstyled HTML.
2. In the browser's developer tools, Network tab: `/assets/css/style.css` and `/assets/css/somaplus.css` load from `https://` with status 200, and the Console shows no "Mixed Content" warnings.
3. View the page source: every `href`/`src` that starts with the site address starts with `https://`.
4. Request a password reset: the emailed link starts with `https://`.
