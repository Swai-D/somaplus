---
title: Add many students from a file
summary: Download the CSV template, fill it in with Excel, and upload it to add a whole class or school at once.
keywords: import, bulk, upload, csv, excel, template, many students, parents, class, stream
order: 2
---
Use this when you have a list of students (for example from a register or an Excel sheet) and do not want to add them one by one. Owners and head teachers can import.

## Steps

1. Open **Academics** > **Students** > **Import students**.
2. Choose **Download the template**. It opens in Excel. The first row holds the column names: do not change it. Two example rows show how to fill it; delete them.
3. Type one student per row. The page lists what each column means, and the names of your classes. **Admission number**, **first name**, **last name**, **gender** and **date of birth** are needed; everything else is optional.
4. Save the file as **CSV** (in Excel: *Save as* > *CSV UTF-8*).
5. Back on the page, choose the file and **Upload and import**.

## What happens

- Every row is checked first. If even one row has a problem, **nothing** is added and the page lists the lines to correct. Fix the file and upload it again.
- A student with a **class** is placed in that class (and **stream**, when given) for the current academic year. Class and stream names must already exist; the import never creates them. Without a class the student is added without a placement and can be placed later.
- A **guardian name and phone** adds a parent or guardian as main contact. A parent with several children at the school is saved once, by phone number.
- Students already in your school are never changed. An admission number that is already used is reported as a problem, so uploading the same file twice adds nothing.
- Dates can be written 2012-05-06 or 06/05/2012 (day first). Gender can be male or female (m or f).

Every student added is written to the audit log, like a student added by hand. The file itself is not kept. See also [Add and find students](help:students/add-and-find) and [Guardians](help:students/guardians).

## If something goes wrong

- *Admission numbers lose their leading zeros in Excel*: format that column as **Text** before typing.
- *Class not found*: type the class exactly as it appears under **Classes**.
- *Too many students*: split the file; the limit is shown on the page.
