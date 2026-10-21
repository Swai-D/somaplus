---
title: Receive GePG payments automatically
summary: Connect your school's GePG service provider details so paid control numbers are recorded and receipted automatically.
keywords: gepg, government, control number, payment, callback, certificate, spcode, integration, settings, automatic
order: 5
---
## Before you start

GePG gives each institution its own **service provider code (SpCode)**, **system ID (SpSysId)** and a **certificate**. You get these from GePG when you register your school. Somaplus cannot create them for you. Control numbers are issued by GePG: type each one on its invoice (**Finance** > **Invoices**) so a payment can find it.

## Set it up

Only the owner can do this. Open **Settings** > **Integrations**, choose **GePG**, type the **SpCode** and **System ID**, paste the **GePG public certificate**, and paste **your private key** (the one that belongs to the certificate you gave GePG). Switch on and choose **Save**. The private key is stored encrypted and is never shown again; leave the box empty on later saves to keep it.

Give GePG the **callback address** shown on the page as your payment notification address.

## What happens

When a control number is paid, GePG sends a signed notice. Somaplus checks the signature and your SpCode, records the payment on the invoice with that control number, issues a receipt and answers GePG so it stops resending. The same notice twice is recorded once. If no invoice has that control number yet, the notice is listed as **No invoice found**: set the control number on the invoice and record the payment from **Payments** using the GePG receipt as the reference.
