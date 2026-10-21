---
title: Ask for payment on the phone with AzamPay
summary: Send a mobile-money prompt to a parent's phone from an invoice; when they approve, the payment and receipt are recorded automatically.
keywords: azampay, mobile money, phone, prompt, airtel, tigo, halopesa, mpesa, azampesa, pay, integration, settings, callback
order: 7
---
## Before you start

Your school needs its own **AzamPay app** (app name, client ID, client secret and API key from AzamPay). The money goes straight to your AzamPay account. AzamPay takes **whole shillings in TZS** only, up to the limit AzamPay sets.

## Set it up

Only the owner can do this. Open **Settings** > **Integrations**, choose **AzamPay**, fill in the details, switch on and choose **Save**. Start with **Sandbox** to test, and change to **Production** when AzamPay has approved your app. The secrets are stored encrypted and never shown again; leave them empty on later saves to keep them. Give AzamPay the **callback address** shown on the page.

## Ask for a payment

Open an invoice, and under **Ask for payment on the phone** type the payer's phone number (for example 0712345678), choose the **Network** and the **Amount** (it cannot be more than the balance), then choose **Send prompt**. The payer approves with their PIN. The phone number is used only to send the prompt and is not kept.

The prompt appears in the list under the form as **Waiting for the payer**. When AzamPay confirms, the payment is recorded on the invoice, a receipt is issued, and the status becomes **Paid**. If the payer does not answer in five minutes it shows **Not answered in time**, but if the money still arrives later it is recorded anyway. Owners and accountants can send prompts; sending the same form twice sends one prompt.
