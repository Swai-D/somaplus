---
title: Admission numbers
summary: Choose whether you type admission numbers yourself or let Somaplus make them in a format you pick, such as SCH-2026-0001.
keywords: admission number, auto generate, automatic, format, prefix, year, running number, digits, settings
order: 3
---
Every student has an admission number that is different within your school. The school owner chooses how it is given.

## Choose how numbers are given

1. Open **Settings** > **Admission numbers** (owner only).
2. Choose **I type them myself** (the way Somaplus always worked) or **Make them for me**.
3. For **Make them for me**, build the format:
   - **Prefix**: letters or digits, for example the short name of your school.
   - **Year**: none, the full year (2026) or the short year (26).
   - **Separator**: none, slash, dash, dot or underscore.
   - **Digits**: how long the running number is, so 4 gives 0001, 0002 and so on.
4. The dark box shows the number the next student will get and two more examples as you change the format. Choose **Save**.

## What changes

- **Add student** no longer asks for the number. Somaplus gives the next one when you save, and the student page shows it.
- **Import students** may leave the admission number column empty. A number typed in a row (for example from an old register) is kept, as long as nobody has it yet.
- A number that Somaplus made cannot be edited afterwards.
- With a year in the format, counting starts again from 1 every new year (SCH-2027-0001). Without a year it keeps counting.
- **Start counting at** is for a school that already has numbers up to a point: type the next number to use. It can only move the count forward, so a number is never given twice.
- If a number is already used by a student, Somaplus skips it.

Changing the format later affects only students added after that. Existing numbers never change. See also [Add and find students](help:students/add-and-find) and [Add many students from a file](help:students/bulk-import).
