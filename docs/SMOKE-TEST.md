# Smoke test

Manual checks to run after a deploy or a significant merge. Each PR that adds a feature adds its steps here.

## Skeleton

1. `docker compose up -d` starts PostgreSQL, Redis and Mailpit; `docker compose ps` shows all three running.
2. `php artisan migrate --seed` completes without errors.
3. `php artisan serve` starts, and the central address `http://localhost:8000` sends you to the sign-in page showing the Somaplus logo, with no broken styles.
4. A template asset loads, for example `/assets/css/somaplus.css` returns 200.
5. `vendor/bin/pest`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --level=5`, `composer audit` and `npm run build` all pass.
6. The template is unchanged. Clone `https://github.com/Swai-D/merge-temp`, check out `b716aa1`, then from the repository root both `diff -r <clone>/somaplus/assets public/assets` and `diff -r <clone>/somaplus resources/template-original -x assets` print nothing.

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
7. Dashboard figures: as the head teacher, the tiles show active students (not those who left), guardians and classes, `+N this month` for students admitted this month, and "Attendance today" splits today's register by status (or says none was recorded). As the accountant, the fee tiles, "Fees by month" chart and "Recent payments" show; reversing a payment on the Payments page lowers "Fees collected" and raises "Fees outstanding" by its amount. A teacher sees no fee blocks, an accountant no attendance block. Switch to Kiswahili: every dashboard label changes.
7. The sidebar shows only "Dashboard" and "Help". Other items appear as the features that own them are built.
8. "Log out" in the profile menu returns to `/login`.

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

1. The sidebar has a section Academics (Masomo) like the template: **Academic years**, and **Classes** which opens to **Class list** and **Subjects**. Streams are managed inside each class.
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
9. In Kiswahili all pages are in Swahili; Help has the topic "Wanafunzi" with four articles.
10. `select action, subject_type, subject_id, user_id from audit_logs` shows the actions above and no names.

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

## Production on Railway (HTTPS styling)

1. Open the deployed address in a browser: the sign-in page shows the Somaplus styling (Bootstrap look, logo), not plain unstyled HTML.
2. In the browser's developer tools, Network tab: `/assets/css/style.css` and `/assets/css/somaplus.css` load from `https://` with status 200, and the Console shows no "Mixed Content" warnings.
3. View the page source: every `href`/`src` that starts with the site address starts with `https://`.
4. Request a password reset: the emailed link starts with `https://`.

