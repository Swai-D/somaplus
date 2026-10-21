---
title: Receive Selcom payments automatically
summary: Let parents pay a control number from a mobile wallet, bank, POS or agent through Selcom and have it recorded and receipted automatically.
keywords: selcom, mobile money, wallet, bank, agent, control number, integration, settings, token, lookup, notification
order: 6
---
## Before you start

Your school needs a Selcom collections (C2B) account. Selcom will ask for your **lookup**, **validation** and **notification** addresses and a **bearer token**. Selcom pays in TZS, so the school currency must be TZS. Type each invoice's control number on the invoice (**Finance** > **Invoices**): parents pay it as the payment reference.

## Set it up

Only the owner can do this. Open **Settings** > **Integrations**, choose **Selcom**, switch on and choose **Save**. Leave the token empty to let Somaplus make one; it is shown **once**, so copy it and give it to Selcom together with the three addresses shown on the page. A saved token is never shown again; leave the box empty on later saves to keep it.

## What happens

- **Lookup**: Selcom asks who owns a control number; Somaplus answers with the student's name so the parent can confirm.
- **Validation**: Selcom checks the amount (a plain number above zero).
- **Notification**: when the money has moved, the payment is recorded on the invoice, a receipt is issued and the balance changes. The same notification twice is recorded once.

Every call must carry your token; anything else is refused. Money that arrived is recorded even if it is more than the balance (the invoice then shows **Overpaid**) and even if the school is read-only. The latest notices are listed on the same page.
