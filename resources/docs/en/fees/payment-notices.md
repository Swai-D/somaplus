---
title: Payment notices from your bank or provider
summary: Connect your bank, GePG gateway or mobile-money provider so paid control numbers are recorded automatically.
keywords: integration, integrations, settings, bank, gepg, webhook, control number, payment notice, secret, automatic, mobile money
order: 4
---
## Set it up

Only the owner can do this. Open **Settings** > **Integrations**, choose the **Provider**, switch **Switched on**, and choose **Save**. Everything is off until you do this.

Leave **Shared secret** empty to let Somaplus make one. It is shown **once** after saving: copy it and give it, together with the address shown on the page, to your bank or provider. Somaplus never shows a saved secret again. To change it, type a new one (or save with the field empty after changing the provider to make a new one). Leaving the field empty on later saves keeps the secret you already have.

## What your bank or provider sends

A signed web request to your school's address with the control number, the amount, the currency and their own reference. Somaplus checks the signature with your secret before it believes anything, and refuses old or altered messages.

## What happens next

- The payment goes to the invoice that owns the control number, a receipt is issued, and the invoice balance changes. The same message sent twice is recorded once.
- If no invoice has that control number yet, the notice waits as **No invoice found**. Set the control number on the invoice; when the provider sends the notice again it is recorded.
- A notice in a different currency from your school, or with a bad amount, is **Rejected** and records nothing.
- Money that really arrived is recorded even if it is more than the balance (the invoice then shows **Overpaid**), and even if your school is read-only.

The last 20 notices are listed on the same page so you can see what arrived.
